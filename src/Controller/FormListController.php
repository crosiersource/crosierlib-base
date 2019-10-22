<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;


use CrosierSource\CrosierLibBaseBundle\Business\Config\StoredViewInfoBusiness;
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

    /** @var LoggerInterface */
    protected $logger;

    /** @var EntityHandler */
    // deve ser setado a partir do setEntityHandler da subclasse com '@required'
    protected $entityHandler;

    /** @var StoredViewInfoBusiness */
    protected $storedViewInfoBusiness;

    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @required
     * @param StoredViewInfoBusiness $storedViewInfoBusiness
     */
    public function setStoredViewInfoBusiness(StoredViewInfoBusiness $storedViewInfoBusiness): void
    {
        $this->storedViewInfoBusiness = $storedViewInfoBusiness;
    }

    /**
     * Monta o formulário, faz as validações, manda salvar, trata erros, etc.
     *
     * @param Request $request
     * @param EntityId|null $entityId
     * @param array $parameters
     * @param bool $preventSubmit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws ViewException
     */
    public function doForm(Request $request, EntityId $entityId = null, $parameters = [], $preventSubmit = false): Response
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

        $form = $this->createForm($parameters['typeClass'], $entityId);

        $entityHandler = $parameters['entityHandler'] ?? $this->getEntityHandler();

        $form->handleRequest($request);

        if (!$preventSubmit && $form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $entity = $form->getData();
                    $this->handleRequestOnValid($request, $entity);
                    $entityHandler->save($entity);
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    $this->afterSave($entity);
                    return $this->redirectTo($request, $entity, $parameters['formRoute'], $parameters['routeParams'] ?? []); // , $parameters);
                } catch (ViewException $e) {
                    $this->addFlash('error', $e->getMessage());
                } catch (\Exception $e) {
                    $msg = ExceptionUtils::treatException($e);
                    $this->addFlash('error', $msg);
                    $this->addFlash('error', 'Erro ao salvar!');
                }
            } else {
                $errors = $form->getErrors(true, true);
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
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

        return $this->doRender($parameters['formView'], $parameters);
    }

    /**
     * Caso seja necessário alterar alguma coisa na entity parseada após uma submissão válida do formulário.
     * @param Request $request
     * @param $entity
     */
    public function handleRequestOnValid(Request $request, $entity): void
    {

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
            $request->getSession()->get('refstoback')[$formRoute]) {

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
        $repo = $this->getDoctrine()->getRepository($this->getEntityHandler()->getEntityClass());

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
            parse_str($rParams['formPesquisar'], $formPesquisar);
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
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage(), 0, $e);
        }

        return new JsonResponse($r);
    }

    /**
     * Filtra os filterDatas por somente aqueles que contenham valores.
     *
     * @param $params
     * @return array
     */
    public function getSomenteFilterDatasComValores($params): array
    {
        $filterDatas = $this->getFilterDatas($params);
        $filterDatasComValores = [];
        if ($filterDatas && count($filterDatas) > 0) {
            foreach ($filterDatas as $filterData) {
                if ($filterData->val !== null && $filterData->val !== '') {
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
     */
    public function getFilterDatas(array $params): ?array
    {
        return null;
    }

    /**
     * A ser sobreescrito, caso seja necessário efetuar algum tratamento nos dados retornados da pesquisa.
     *
     * @param array $dados
     */
    public function handleDadosList(array &$dados)
    {

    }

    /**
     * Para listas que não utilizam o datatables.js
     *
     * @param Request $request
     * @param null $defaultFilters
     * @return Response
     * @throws ViewException
     */
    public function doListSimpl(Request $request, array $parameters = []): Response
    {
        if (isset($parameters['r']) && $parameters['r']) {
            $this->storedViewInfoBusiness->clear($parameters['listRoute']);
        }

        $filterParams = [];
        if ($request->get('filter')) {
            $filterParams['filter'] = $request->get('filter');
        } else {
            $svi = $this->storedViewInfoBusiness->retrieve($parameters['listRoute']);
            $filterParams = $svi['filterParams'] ?? null;
            if ($filterParams) {
                return $this->redirectToRoute($parameters['listRoute'], $filterParams);
            }
            // else
            $filterParams = $parameters['defaultFilters'] ?? null;
        }

        if (isset($parameters['fixedFilters'])) {
            $filterParams = array_replace_recursive($filterParams, $parameters['fixedFilters']);
        }

        $parameters['page_title'] = $parameters['listPageTitle'];
        if (isset($parameters['list_PROGRAM_UUID'])) {
            $parameters['PROGRAM_UUID'] = $parameters['list_PROGRAM_UUID'];
        }

        /** @var FilterRepository $repo */
        $repo = $this->getDoctrine()->getRepository($this->getEntityHandler()->getEntityClass());

        // Inicializadores
        $filterDatas = null;

        if ($filterParams) {
            $filterDatas = $this->getSomenteFilterDatasComValores($filterParams);
        }

        $parameters['orders'] = $parameters['orders'] ?? ['updated' => 'DESC', 'id' => 'DESC'];

        $dados = $repo->findByFilters($filterDatas, $parameters['orders'], 0, null);

        $this->handleDadosList($dados);

        $parameters['dados'] = $dados;
        $parameters['filter'] = $filterParams['filter'];

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

        return $this->redirectToRoute($parameters['listRoute'], $parameters['listRouteParams'] ?? null);
    }


}
