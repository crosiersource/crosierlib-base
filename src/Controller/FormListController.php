<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;


use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\ViewUtils\StoredViewInfoUtils;
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

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var EntityHandler */
    protected $entityHandler;

    protected $crudParams =
        [
            'typeClass' => '',
            'formView' => '',
            'formRoute' => '',
            'formPageTitle' => '',
            'listView' => '',
            'listRoute' => '',
            'listRouteAjax' => '',
            'listPageTitle' => '',
            'listId',
            'deleteRoute' => '',
        ];

    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getEntityHandler(): EntityHandler
    {
        return $this->entityHandler;
    }

    /**
     * Monta o formulário, faz as validações, manda salvar, trata erros, etc.
     *
     * @param Request $request
     * @param EntityId|null $entityId
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function doForm(Request $request, EntityId $entityId = null, $parameters = []): Response
    {
        $this->checkAccess($this->crudParams['formRoute']);

        if (!$entityId) {
            $entityName = $this->getEntityHandler()->getEntityClass();
            $entityId = new $entityName();
        }

        $this->handleReferer($request);

        $form = $this->createForm($this->crudParams['typeClass'], $entityId);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $entity = $form->getData();
                    $this->getEntityHandler()->save($entity);
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    return $this->redirectTo($request, $entity, $parameters);
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

        // Pode ou não ter vindo algo no $parameters. Independentemente disto, só adiciono form e foi-se.
        $parameters['form'] = $form->createView();
        $parameters['page_title'] = $this->crudParams['formPageTitle'];
        $parameters['e'] = $entityId;
        if (!isset($parameters['PROGRAM_UUID']) && isset($this->crudParams['form_PROGRAM_UUID'])) {
            $parameters['PROGRAM_UUID'] = $this->crudParams['form_PROGRAM_UUID'];
        }

        return $this->render($this->crudParams['formView'], $parameters);
    }

    /**
     * Verifica para onde deve ser redirecionado após o save.
     *
     * @param Request $request
     * @param EntityId $entityId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectTo(Request $request, EntityId $entityId, array $parameters = []): ?\Symfony\Component\HttpFoundation\RedirectResponse
    {
        if ($request->getSession()->has('refstoback') and
            $request->getSession()->get('refstoback')[$this->crudParams['formRoute']]) {

            $tos = $request->getSession()->get('refstoback');
            $url = $tos[$this->crudParams['formRoute']];
            // limpo apenas o que já será utilizado no redirect
            $tos[$this->crudParams['formRoute']] = null;
            $request->getSession()->set('refstoback', $tos);

            return $this->redirect($url);

        } else {
            return $this->redirectToRoute($this->crudParams['formRoute'], array_merge($parameters, ['id' => $entityId->getId()]));
        }
    }

    /**
     * Se existir filtro na tela, sobreescrever.
     *
     * @param array $params
     * @return array|null
     */
    public function getFilterDatas(array $params): ?array
    {

    }

    /**
     * Filtra os filterDatas por somente aqueles que contenham valores.
     *
     * @param $params
     * @return array
     */
    public function doGetFilterDatas($params): array
    {
        $filterDatas = $this->getFilterDatas($params);
        $filterDatasComValores = [];
        if ($filterDatas && count($filterDatas) > 0) {
            foreach ($filterDatas as $filterData) {
                if ($filterData->val) {
                    $filterDatasComValores[] = $filterData;
                }
            }
        }
        return $filterDatasComValores;
    }

    /**
     * Verifica junto a CrosierSecurityAPI se o usuário logado tem acesso a rota requisitada.
     *
     * @param string $route
     * @return bool
     */
    public function checkAccess(string $route): bool
    {
        // FIXME: implementar.
        return true;
    }

    /**
     * @param Request $request
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function doList(Request $request, $parameters = array()): Response
    {
        $this->checkAccess($this->crudParams['listRoute']);

        $params = $request->query->all();
        if (!array_key_exists('filter', $params)) {
            // inicializa para evitar o erro
            $params['filter'] = null;

            if (isset($params['r']) and $params['r']) {
                StoredViewInfoUtils::clear($this->crudParams['listRoute']);
            } else {
                $storedViewInfo = StoredViewInfoUtils::retrieve($this->crudParams['listRoute']);
                if (false and $storedViewInfo) { //FIXME: problema no caso do grupoItemList. Não estava aceitando nova url com novo pai.
                    $blob = stream_get_contents($storedViewInfo['viewInfo']);
                    $unserialized = unserialize($blob);
                    $formPesquisar = isset($unserialized['formPesquisar']) ? $unserialized['formPesquisar'] : null;
                    if ($formPesquisar and $formPesquisar != $params) {
                        return $this->redirectToRoute($this->crudParams['listRoute'], $formPesquisar);
                    }
                }
            }
        }

        $params['page_title'] = $this->crudParams['listPageTitle'];
        if (isset($this->crudParams['list_PROGRAM_UUID'])) {
            $params['PROGRAM_UUID'] = $this->crudParams['list_PROGRAM_UUID'];
        }
        $params = array_merge($params, $parameters);

        return $this->render($this->crudParams['listView'], $params);
    }

    /**
     * @param Request $request
     * @param null $defaultFilters
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function doDatatablesJsList(Request $request, $defaultFilters = null): Response
    {
        $this->checkAccess($this->crudParams['listRoute']);

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
            $limit = $rParams['length'] != '-1' ? $rParams['length'] : null;

            $orders = array();
            foreach ($rParams['order'] as $pOrder) {
                $column = $rParams['columns'][$pOrder['column']]['name'];
                $dir = $pOrder['dir'];
                $orders[$column] = $dir;
            }
            $draw = (int)$rParams['draw'];
            parse_str($rParams['formPesquisar'], $formPesquisar);
            if (is_array($defaultFilters)) {
                $formPesquisar = array_merge_recursive($formPesquisar, $defaultFilters);
            }
            $filterDatas = $this->doGetFilterDatas($formPesquisar);
        }

        $countByFilter = $repo->doCountByFilters($filterDatas);
        $dados = $repo->findByFilters($filterDatas, $orders, $start, $limit);

        $this->handleDadosList($dados);

        // Para que possa acessar todas os atributos de dentro do getDatatablesColumns() no DatatablesJs
        $dadosE = [];
        foreach ($dados as $dado) {
            $dadosE[]['e'] = $dado;
        }
        $dados = $dadosE;



        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $serializer = new Serializer([new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)]);

        $context = [];
        // se foi passado uma lista de atributos da entidade, utiliza
        if (isset($this->crudParams['normalizedAttrib'])) {
            $context['attributes'] = $this->crudParams['normalizedAttrib'];
        } else {
            // caso contrário, tenta serializar pelos grupos setados no @Groups
            $context['groups'] = ['entityId', 'entity'];
        }



        $recordsTotal = $repo->count(array());

        $results = array(
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $countByFilter,
            'data' => $dados
        );

        $r = $serializer->normalize($results, 'json', $context);

        if ($filterDatas and count($filterDatas) > 0) {
            $viewInfo = array();
            $viewInfo['formPesquisar'] = $formPesquisar;
            StoredViewInfoUtils::store($this->crudParams['listRoute'], $viewInfo);
        }

        return new JsonResponse($r);
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
     * @param Request $request
     * @param EntityId $entityId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function doDelete(Request $request, EntityId $entityId): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $this->checkAccess($this->crudParams['deleteRoute']);
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'Erro interno do sistema.');
        } else {
            try {
                $this->getEntityHandler()->delete($entityId);
                $this->addFlash('success', 'Registro deletado com sucesso.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erro ao deletar registro.');
            }
        }
        if ($request->server->get('HTTP_REFERER')) {
            return $this->redirect($request->server->get('HTTP_REFERER'));
        } else {

            return $this->redirectToRoute($this->crudParams['listRoute']);
        }
    }

    /**
     * Se for passado o parâmetro 'reftoback', então seta na sessão o referer para onde deve voltar após um save no form.
     * @param Request $request
     */
    public function handleReferer(Request $request): void
    {
        if ($request->get('reftoback')) {
            $to = $request->getSession()->get('refstoback');
            // Se já estiver setado, então não reseta (para não acontecer se setar para a própria formRoute no submit do form).
            if (!$to || !isset($to[$this->crudParams['formRoute']]) || !$to[$this->crudParams['formRoute']]) {
                // se já tiver na sessão, adiciona dentro do existente.
                if ($request->getSession()->has('refstoback')) {
                    $to = $request->getSession()->get('refstoback');
                }
                $to[$this->crudParams['formRoute']] = $request->server->get('HTTP_REFERER');
                $request->getSession()->set('refstoback', $to);
            }
        }
    }

    /**
     * Sobreescreve o parent::render com atributos padrão para CRUD.
     *
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     * @throws \Exception
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $parameters = array_merge($this->crudParams, $parameters);
        return parent::render($view, $parameters, $response);
    }


}