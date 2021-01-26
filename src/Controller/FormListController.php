<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;


use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
use Doctrine\Common\Annotations\AnnotationReader;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Classe pai para CRUDs padrão.
 *
 * @author Carlos Eduardo Pauluk
 */
abstract class FormListController extends BaseController
{

    protected LoggerInterface $logger;

    /**
     * deve ser setado a partir do setEntityHandler da subclasse com '@ required'
     * @var EntityHandler $entityHandler
     */
    protected $entityHandler;


    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Monta o formulário, faz as validações, manda salvar, trata erros, etc.
     *
     * @param Request $request
     * @param EntityId|null $entityId
     * @param array $parameters
     * @param bool $preventSubmit
     * @param \Closure|null $fnHandleRequestOnValid
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws ViewException
     */
    public function doForm(Request $request, EntityId $entityId = null, array $parameters = [], bool $preventSubmit = false, ?\Closure $fnHandleRequestOnValid = null): Response
    {
        if (!isset($parameters['typeClass'])) {
            throw new ViewException('typeClass não informado');
        }
        if (!isset($parameters['formRoute'])) {
            throw new ViewException('formRoute não informado');
        }

        if (!$entityId) {
            $entityName = $parameters['entityClass'] ?? $this->getEntityHandler()->getEntityClass();
            $entityId = new $entityName();
        }

        $formOptions = $preventSubmit ? ['disabled' => true] : [];
        $form = $this->createForm($parameters['typeClass'], $entityId, $formOptions);

        $entityHandler = $parameters['entityHandler'] ?? $this->getEntityHandler();

        $form->handleRequest($request);

        if (!$preventSubmit && $form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $entity = $form->getData();
                    if ($fnHandleRequestOnValid) {
                        $fnHandleRequestOnValid($request, $entity, $parameters);
                    } else {
                        $this->handleRequestOnValid($request, $entity, $parameters);
                    }
                    $entityHandler->save($entity);
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    $this->afterSave($entity);
                    return $this->redirectTo($request, $entity, $parameters['formRoute'], $parameters['routeParams'] ?? []); // , $parameters);
                } catch (\Exception $e) {
                    $msg = ExceptionUtils::treatException($e);
                    $this->logger->error($msg);
                    $this->logger->error($e->getMessage());
                    $this->addFlash('error', $msg);
                    $this->addFlash('error', 'Erro ao salvar!');
                }
            } else {
                $errors = $form->getErrors(true, true);
                foreach ($errors as /** @var \Symfony\Component\Form\FormError $error */ $error) {
                    $this->addFlash('error', $error->getMessage() . ' - ' . $error->getOrigin()->getName());
                }
            }
        }

        $this->handleReferer($request, $parameters);

        // Pode ou não ter vindo algo no $parameters. Independentemente disto, só adiciono form e foi-se.
        $parameters['form'] = $form->createView();
        $parameters['page_title'] = $parameters['formPageTitle'] ?? '';
        $parameters['e'] = $entityId;
        if (!isset($parameters['PROGRAM_UUID']) && isset($parameters['form_PROGRAM_UUID'])) {
            $parameters['PROGRAM_UUID'] = $parameters['form_PROGRAM_UUID'];
        }
        $parameters['formView'] = isset($parameters['formView']) ? $parameters['formView'] : '@CrosierLibBase/form.html.twig';

        // unset nos parâmetros que não tem utilidade para o 'twig'
        $parameters['entityClass'] = null;
        $parameters['typeClass'] = null;
        $parameters['entityHandler'] = null;
        $parameters['preventSubmit'] = $preventSubmit;

        return $this->doRender($parameters['formView'], $parameters);
    }

    /**
     * Neste caso é necessário o getter pois (??)
     * @return EntityHandler
     */
    public function getEntityHandler(): EntityHandler
    {
        return $this->entityHandler;
    }

    /**
     * Caso seja necessário alterar alguma coisa na entity parseada após uma submissão válida do formulário.
     * @param Request $request
     * @param $entity
     */
    public function handleRequestOnValid(Request $request, $entity, ?array &$parameters = []): void
    {

    }

    /**
     * A ser sobreescrito.
     *
     * @param EntityId $entityId
     */
    public function afterSave(EntityId $entityId)
    {

    }

    /**
     * Verifica para onde deve ser redirecionado após o save.
     *
     * @param Request $request
     * @param EntityId $entityId
     * @param string $formRoute
     * @param array|null $formRouteParams
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectTo(Request $request, EntityId $entityId, string $formRoute, ?array $formRouteParams = []): ?\Symfony\Component\HttpFoundation\RedirectResponse
    {
        if ($request->getSession()->has('refstoback') &&
            ($request->getSession()->get('refstoback')[$formRoute] ?? false)) {

            $tos = $request->getSession()->get('refstoback');
            $url = $tos[$formRoute];
            // limpo apenas o que já será utilizado no redirect
            $tos[$formRoute] = null;
            $request->getSession()->set('refstoback', $tos);

            return $this->redirect($url);
        }

        $redirectParams = array_merge($formRouteParams, ['id' => $entityId->getId()]);
        return $this->redirectToRoute($formRoute, $redirectParams);
    }

    /**
     * Se for passado o parâmetro 'reftoback', então seta na sessão o referer para onde deve voltar
     * após um save no form dentro do 'refstoback'.
     *
     * ** Atenção para a diferença entre reftoback (na querystring) e refstoback (na session).
     *
     * @param Request $request
     * @param array $parameters
     */
    public function handleReferer(Request $request, array $parameters): void
    {
        $to = $request->getSession()->get('refstoback') ?? [];
        if ($request->get('reftoback')) {
            $to[$parameters['formRoute']] = $request->server->get('HTTP_REFERER');
        } else {
            $to[$parameters['formRoute']] = null;
        }
        $request->getSession()->set('refstoback', $to);
    }

    /**
     * Sobreescreve o parent::render com atributos padrão para CRUD.
     *
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    protected function doRender(string $view, array $parameters = [], Response $response = null): Response
    {
        // $parameters = array_merge($parameters, $parameters);  ??????
        return parent::doRender($view, $parameters, $response);
    }

    /**
     * @param Request $request
     * @param array $parameters
     * @return Response
     * @throws ViewException
     */
    public function doList(Request $request, $parameters = []): Response
    {
        if (!isset($parameters['listRoute'])) {
            throw new ViewException('listRoute não informado');
        }
        $queryParams = $request->query->all();
        if (!array_key_exists('filter', $queryParams)) {
            // inicializa para evitar o erro
            $queryParams['filter'] = null;

            if (isset($queryParams['r']) and $queryParams['r']) {
                $this->storedViewInfoBusiness->clear($parameters['listRoute']);
            } else {
                if ($svi = $this->storedViewInfoBusiness->retrieve($parameters['listRoute'])) {
                    $filter = $svi['filter'] ?? null;
                    if ($filter) {
                        $redirectParams = ['filter' => $filter];
                        // Caso a route do list tenha algum parâmetro obrigatório, precisa ser repassado aqui
                        if (isset($parameters['routeParams'])) {
                            $redirectParams = array_merge($redirectParams, $parameters['routeParams']);
                        }
                        return $this->redirectToRoute($parameters['listRoute'], $redirectParams);
                    }
                }
            }
        }

        $queryParams['page_title'] = $parameters['listPageTitle'] ?? '';
        $queryParams = array_replace_recursive($queryParams, $parameters);

        if (($queryParams['filter'] ?? null) && (count($queryParams['filter']) > 0)) {
            $this->storedViewInfoBusiness->store($parameters['listRoute'], ['filter' => $queryParams['filter']]);
        }
        $parameters['listView'] = $parameters['listView'] ?? '@CrosierLibBase/list.html.twig';
        return $this->doRender($parameters['listView'], $queryParams);
    }

    /**
     * @param Request $request
     * @param null $defaultFilters
     * @param array|null $dadosProntos
     * @param int|null $countByFilter
     * @return Response
     * @throws ViewException
     */
    public function doDatatablesJsList(Request $request, $defaultFilters = null, ?array $dadosProntos = null, ?int $countByFilter = null): Response
    {
        /** @var FilterRepository $repo */
        $repo = $this->getRepository();

        $rParams = $request->request->all();

        // Inicializadores
        $filterDatas = null;
        $start = 0;
        $limit = 10;
        $orders = null;
        $draw = 1;

        $formPesquisar = null;
        if ($rParams) {
            $start = $rParams['start'];
            $limit = $rParams['length'] !== '-1' ? $rParams['length'] : null;

            $orders = array();
            foreach ($rParams['order'] as $pOrder) {
                $column = $rParams['columns'][$pOrder['column']]['name'];
                $dir = $pOrder['dir'];
                $orders[$column] = $dir;
            }
            $draw = (int)$rParams['draw'];
            parse_str($rParams['formPesquisar'] ?? null, $formPesquisar);
            if (is_array($defaultFilters)) {
                $formPesquisar['filter'] = array_replace_recursive($formPesquisar['filter'], $defaultFilters['filter']);
            }
            $filterDatas = $this->getSomenteFilterDatasComValores($formPesquisar);
        }

        // Caso os dados já tenham sido passados, não refaz a busca...
        if (!$dadosProntos) {
            $countByFilter = $repo->doCountByFilters($filterDatas);
            if ($countByFilter < $start) {
                $start = 0;
            }
            $dados = $repo->findByFilters($filterDatas, $orders, $start, $limit);
        } else {
            $dados = $dadosProntos;
        }

        $this->handleDadosList($dados);

        // Para que possa acessar todas os atributos de dentro do getDatatablesColumns() no DatatablesJs
        $dadosE = [];

        foreach ($dados as $dado) {
            $dadosE[]['e'] = $dado;
        }
        $dados = $dadosE;

        try {
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $serializer = new Serializer([new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)]);
            $context['groups'] = ['entityId', 'entity'];
            $recordsTotal = $repo->count(array());
            $results = array(
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $countByFilter,
                'data' => $dados
            );
            $context['circular_reference_limit'] = 3;
            $context['enable_max_depth'] = true;
            $r = $serializer->normalize($results, 'json', $context);
            $this->handleSerializedList($r);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage(), 0, $e);
        }

        return new JsonResponse($r);
    }

    /**
     * Possibilidade de ser sobreescrito...
     */
    public function getRepository()
    {
        return $this->getDoctrine()->getRepository($this->getEntityHandler()->getEntityClass());
    }

    /**
     * Filtra os filterDatas por somente aqueles que contenham valores.
     *
     * @param $params
     * @param \Closure|null $fnGetFilterDatas
     * @return array
     */
    public function getSomenteFilterDatasComValores($params, ?\Closure $fnGetFilterDatas = null): array
    {
        $filterDatas = $fnGetFilterDatas ? $fnGetFilterDatas($params) : $this->getFilterDatas($params);
        $filterDatasComValores = [];
        if ($filterDatas && count($filterDatas) > 0) {
            foreach ($filterDatas as $filterData) {
                if (is_array($filterData->val)) {
                    foreach ($filterData->val as $val) {
                        if ($val !== null && $val !== '') {
                            $filterDatasComValores[] = $filterData;
                            break;
                        }
                    }
                } else if ($filterData->val !== null && $filterData->val !== '') {
                    $filterDatasComValores[] = $filterData;
                }
            }
        }
        return $filterDatasComValores;
    }

    /**
     * Se existir filtro na tela, sobreescrever.
     *
     * @param array $params
     * @return array|null
     * @noinspection PhpUnusedParameterInspection
     */
    public function getFilterDatas(array $params): ?array
    {
        return null;
    }

    /**
     * A ser sobreescrito, caso seja necessário efetuar algum tratamento nos dados retornados da pesquisa.
     *
     * @param array $dados
     * @param int|null $totalRegistros
     */
    public function handleDadosList(array &$dados, ?int $totalRegistros = null): void
    {

    }

    /**
     * A ser sobreescrito, caso seja necessário efetuar algum tratamento nos dados já serializados em JSON.
     *
     * @param array $r
     */
    public function handleSerializedList(array &$r): void
    {

    }

    /**
     * Para listas que não utilizam o datatables.js
     *
     * @param Request $request
     * @param array $parameters
     * @param \Closure|null $fnGetFilterDatas
     * @param \Closure|null $fnHandleDadosList
     * @return Response
     * @throws ViewException
     */
    public function doListSimpl(Request $request, array $parameters = [], ?\Closure $fnGetFilterDatas = null, ?\Closure $fnHandleDadosList = null): Response
    {
        if ($request->get('r')) {
            $this->storedViewInfoBusiness->clear($parameters['listRoute']);
        }

        $filterParams = [];
        if ($request->get('filter')) {
            $filterParams['filter'] = $request->get('filter');
        } else {
            $svi = $this->storedViewInfoBusiness->retrieve($parameters['listRoute']);
            $filterParams = $svi['filterParams'] ?? [];
            if ($filterParams) {
                return $this->redirectToRoute($parameters['listRoute'], $filterParams);
            }
            // else
            $filterParams = $parameters['defaultFilters'] ?? [];
        }

        if (isset($parameters['fixedFilters'])) {
            $filterParams = array_replace_recursive($filterParams, $parameters['fixedFilters']);
        }

        $parameters['page_title'] = $parameters['listPageTitle'] ?? '';

        /** @var FilterRepository $repo */
        $repo = $this->getDoctrine()->getRepository($this->getEntityHandler()->getEntityClass());

        // Inicializadores
        $filterDatas = null;

        if ($filterParams) {
            $filterDatas = $this->getSomenteFilterDatasComValores($filterParams, $fnGetFilterDatas);
        }

        if ($request->get('filter_order') && ($parameters['colunas'] ?? false)) {
            $filterOrders = json_decode($request->get('filter_order'), true);
            $parameters['orders'] = [];
            foreach ($filterOrders as $filterOrder) {
                $idx = $filterOrder[0];
                $parameters['orders'][$parameters['colunas'][$idx]] = strtoupper($filterOrder[1]);
            }
        }

        $parameters['orders'] = $parameters['orders'] ?? ['updated' => 'DESC', 'id' => 'DESC'];

        $countByFilter = $repo->doCountByFilters($filterDatas);

        $parameters['totalRegistros'] = $countByFilter;

        $dados = $repo->findByFilters($filterDatas, $parameters['orders'], $parameters['start'] ?? 0, $parameters['limit'] ?? null);

        if (isset($fnHandleDadosList)) {
            $fnHandleDadosList($dados, $countByFilter, $filterDatas);
        } else {
            $this->handleDadosList($dados, $countByFilter);
        }

        $parameters['dados'] = $dados;
        $parameters['filter'] = $filterParams['filter'] ?? [];

        if ($filterDatas and count($filterDatas) > 0) {
            $viewInfo = [];
            $viewInfo['filterParams'] = $filterParams;
            $this->storedViewInfoBusiness->store($parameters['listRoute'], $viewInfo);
        }

        return $this->doRender($parameters['listView'], $parameters);
    }

    /**
     * @param Request $request
     * @param EntityId $entityId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function doDelete(Request $request, EntityId $entityId, array $parameters): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'Erro interno do sistema.');
        } else {
            try {
                $this->getEntityHandler()->delete($entityId);
                $this->addFlash('success', 'Registro deletado com sucesso.');
            } catch (ViewException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erro ao deletar registro.');
            }
        }
        if ($request->server->get('HTTP_REFERER')) {
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        return $this->redirectToRoute($parameters['listRoute'], $parameters['listRouteParams'] ?? []);
    }


}
