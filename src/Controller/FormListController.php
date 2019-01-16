<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;


use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
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

    private $storedViewInfoBusiness;

    private $securityBusiness;

    private $logger;


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
     * Deve ser implementada pelo filho retornando o serviço autowired.
     */
    public abstract function getStoredViewInfoBusiness();


    /**
     * Deve ser implementada pelo filho retornando o serviço autowired.
     */
    public abstract function getSecurityBusiness();

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
        $this->securityBusiness->checkAccess($this->getFormRoute());

        if (!$entityId) {
            $entityName = $this->getEntityHandler()->getEntityClass();
            $entityId = new $entityName();
        }

        $form = $this->createForm($this->getTypeClass(), $entityId);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $entity = $form->getData();
                    $this->getEntityHandler()->save($entity);
                    $this->addFlash('success', 'Registro salvo com sucesso!');
                    return $this->redirectToRoute($this->getFormRoute(), array('id' => $entityId->getId()));
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
     * Utilizado para setar na <title>.
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
     * @param Request $request
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function doList(Request $request, $parameters = array())
    {
        $this->securityBusiness->checkAccess($this->getListRoute());

        $params = $request->query->all();
        if (!array_key_exists('filter', $params)) {
            // inicializa para evitar o erro
            $params['filter'] = null;

            if (isset($params['r']) and $params['r']) {
                $this->storedViewInfoBusiness->clear($this->getListRoute());
            } else {
                $storedViewInfo = $this->storedViewInfoBusiness->retrieve($this->getListRoute());
                if (false and $storedViewInfo) { //FIXME: problema no caso do grupoItemList. Não estava aceitando nova url com novo pai.
                    $blob = stream_get_contents($storedViewInfo->getViewInfo());
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
     * @return Response
     */
    public function doDatatablesJsList(Request $request, $defaultFilters = null)
    {
        $this->securityBusiness->checkAccess($this->getListRoute());

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
                $order['column'] = $rParams['columns'][$pOrder['column']]['name'];
                $order['dir'] = $pOrder['dir'];
                $orders[] = $order;
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
            $this->storedViewInfoBusiness->store($this->getListRoute(), $viewInfo);
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
     * @return mixed
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @required
     * @param mixed $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }


}