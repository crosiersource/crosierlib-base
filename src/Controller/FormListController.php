<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;


use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\ViewUtils\StoredViewInfoUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Classe pai para CRUDs padrão.
 *
 * @package App\Controller
 */
abstract class FormListController extends AbstractController
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    abstract public function getEntityHandler(): ?EntityHandler;

    /**
     * Necessário para poder passar para o createForm.
     *
     * @return mixed
     */
    abstract public function getTypeClass();

    abstract public function getFormRoute();

    abstract public function getFormView();


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
        $crudParams = [];
        $crudParams['formPageTitle'] = $this->getFormPageTitle();
        $crudParams['listPageTitle'] = $this->getListPageTitle();
        $crudParams['formRoute'] = $this->getFormRoute();
        $crudParams['listRoute'] = $this->getListRoute();
        $crudParams['listId'] = $this->getListId();

        $crudParams['listRouteAjax'] = $this->getListRouteAjax();

        $parameters = array_merge($crudParams, $parameters);

        return parent::render($view, $parameters, $response);
    }

    /**
     * Utilizado como id to <table>
     *
     * @return string
     * @throws \Exception
     */
    public function getListId()
    {
        // Por padrão...
        // Se necessário, sobreescreva!
        try {
            return strtolower((new \ReflectionClass($this->getEntityHandler()->getEntityClass()))->getShortName()) . 'List';
        } catch (\ReflectionException $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Utilizado para setar na <title>.
     * @throws \Exception
     */
    public function getFormPageTitle()
    {
        // Por padrão, retorno o nome da entidade. Se preciso, sobreescrever.
        try {
            return (new \ReflectionClass($this->getEntityHandler()->getEntityClass()))->getShortName();
        } catch (\ReflectionException $e) {
            throw new \Exception($e);
        }
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
    public function doForm(Request $request, EntityId $entityId = null, $parameters = [])
    {
        $this->checkAccess($this->getFormRoute());

        if (!$entityId) {
            $entityName = $this->getEntityHandler()->getEntityClass();
            $entityId = new $entityName();
        }

        $this->handleReferer($request);

        $form = $this->createForm($this->getTypeClass(), $entityId);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $entity = $form->getData();
                    $this->getEntityHandler()->save($entity);
                    $this->addFlash('success', 'Registro salvo com sucesso!');

                    return $this->redirectTo($request, $entity);

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
        $parameters['page_title'] = $this->getFormPageTitle();
        $parameters['e'] = $entityId;

        return $this->render($this->getFormView(), $parameters);
    }

    /**
     * Verifica para onde deve ser redirecionado após o save.
     *
     * @param Request $request
     * @param EntityId $entityId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectTo(Request $request, EntityId $entityId): ?\Symfony\Component\HttpFoundation\RedirectResponse
    {
        if ($request->getSession()->has('refstoback') and
            $request->getSession()->get('refstoback')[$this->getFormRoute()]) {

            $tos = $request->getSession()->get('refstoback');
            $url = $tos[$this->getFormRoute()];
            // limpo apenas o que já será utilizado no redirect
            $tos[$this->getFormRoute()] = null;
            $request->getSession()->set('refstoback', $tos);

            return $this->redirect($url);

        } else {
            return $this->redirectToRoute($this->getFormRoute(), array('id' => $entityId->getId()));
        }
    }

    abstract public function getFilterDatas($params);

    /**
     * @param $params
     * @return array
     */
    public function doGetFilterDatas($params)
    {
        $filterDatas = $this->getFilterDatas($params);
        $clearedFilterDatas = array();
        if ($filterDatas and count($filterDatas) > 0) {
            foreach ($filterDatas as $filterData) {
                $notEmpty = false;
                if (is_array($filterData->val)) {
                    foreach ($filterData->val as $val) {
                        if ($val) {
                            $notEmpty = true;
                            break;
                        }
                    }
                } else if ($filterData->val) {
                    $notEmpty = true;
                }
                if ($notEmpty) {
                    $clearedFilterDatas[] = $filterData;
                }
            }
        }
        return $clearedFilterDatas;
    }

    /**
     * Deve ser informado pela subclasse para poder renderizar a view da list.
     *
     * @return mixed
     */
    abstract public function getListView();

    /**
     * Deve ser informado pela subclasse. É utilizado pela 'storedViewInfoBusiness', para 'redirectToRoute' e no 'checkAccess'.
     *
     * @return mixed
     */
    abstract public function getListRoute();


    /**
     * Caso exista, deve ser informado pela subclasse.
     *
     * @return mixed
     */
    public function getListRouteAjax()
    {

    }


    /**
     * Utilizado para setar na <title>.
     * @throws \Exception
     */
    public function getListPageTitle()
    {
        // Por padrão, retorno o nome da entidade no plural. Se preciso, sobreescrever.
        try {
            return (new \ReflectionClass($this->getEntityHandler()->getEntityClass()))->getShortName() . "s";
        } catch (\ReflectionException $e) {
            throw new \Exception($e);
        }
    }


    /**
     * Verifica junto a CrosierSecurityAPI se o usuário logado tem acesso a rota requisitada.
     *
     * @param string $route
     * @return mixed
     */
    public function checkAccess(string $route)
    {
        // FIXME: implementar.
        return;
    }

    /**
     * @param Request $request
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function doList(Request $request, $parameters = array())
    {
        $this->checkAccess($this->getListRoute());

        $params = $request->query->all();
        if (!array_key_exists('filter', $params)) {
            // inicializa para evitar o erro
            $params['filter'] = null;

            if (isset($params['r']) and $params['r']) {
                StoredViewInfoUtils::clear($this->getListRoute());
            } else {
                $storedViewInfo = StoredViewInfoUtils::retrieve($this->getListRoute());
                if (false and $storedViewInfo) { //FIXME: problema no caso do grupoItemList. Não estava aceitando nova url com novo pai.
                    $blob = stream_get_contents($storedViewInfo['viewInfo']);
                    $unserialized = unserialize($blob);
                    $formPesquisar = isset($unserialized['formPesquisar']) ? $unserialized['formPesquisar'] : null;
                    if ($formPesquisar and $formPesquisar != $params) {
                        return $this->redirectToRoute($this->getListRoute(), $formPesquisar);
                    }
                }
            }
        }

        $params['page_title'] = $this->getListPageTitle();

        $params = array_merge($params, $parameters);

        return $this->render($this->getListView(), $params);
    }

    /**
     * Necessário informar quais atributos da entidades deverão ser retornados no Json.
     *
     * @return mixed
     */
    public function getNormalizeAttributes()
    {
        return null;
    }

    /**
     * @param Request $request
     * @param null $defaultFilters
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function doDatatablesJsList(Request $request, $defaultFilters = null)
    {
        $this->checkAccess($this->getListRoute());

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

        // Para que possa acessar todas os atributos de dentro do getDatatablesColumns() no DatatablesJs
        $dadosE = [];
        foreach ($dados as $dado) {
            $dadosE[]['e'] = $dado;
        }
        $dados = $dadosE;

        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));
        $data = $serializer->normalize($dados, 'json', $this->getNormalizeAttributes());

        $recordsTotal = $repo->count(array());

        $results = array(
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $countByFilter,
            'data' => $data
        );

        $json = $serializer->serialize($results, 'json');

        if ($filterDatas and count($filterDatas) > 0) {
            $viewInfo = array();
            $viewInfo['formPesquisar'] = $formPesquisar;
            StoredViewInfoUtils::store($this->getListRoute(), $viewInfo);
        }

        return new Response($json);
    }

    /**
     * @param Request $request
     * @param EntityId $entityId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function doDelete(Request $request, EntityId $entityId)
    {
//        $this->checkAccess();
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

        return $this->redirectToRoute($this->getListRoute());
    }

    /**
     * Se for passado o parâmetro 'reftoback', então seta na sessão o referer para onde deve voltar após um save no form.
     * @param Request $request
     */
    public function handleReferer(Request $request)
    {
        if ($request->get('reftoback')) {
            $to = $request->getSession()->get('refstoback');
            // Se já estiver setado, então não reseta (para não acontecer se setar para a própria formRoute no submit do form).
            if (!$to || !isset($to[$this->getFormRoute()]) || !$to[$this->getFormRoute()]) {
                // se já tiver na sessão, adiciona dentro do existente.
                if ($request->getSession()->has('refstoback')) {
                    $to = $request->getSession()->get('refstoback');
                }
                $to[$this->getFormRoute()] = $request->server->get('HTTP_REFERER');
                $request->getSession()->set('refstoback', $to);
            }
        }
    }


}