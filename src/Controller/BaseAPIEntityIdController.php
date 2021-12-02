<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;

use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\PushMessageEntityHandler;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\APIUtils\APIProblem;
use CrosierSource\CrosierLibBaseBundle\Utils\EntityIdUtils\EntityIdUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class APIBaseController.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Controller
 * @author Carlos Eduardo Pauluk
 */
abstract class BaseAPIEntityIdController extends AbstractController
{

    protected LoggerInterface $logger;

    protected PushMessageEntityHandler $entityHandler;

    protected EntityIdUtils $entityIdUtils;

    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return EntityHandler
     */
    public function getEntityHandler(): EntityHandler
    {
        return $this->entityHandler;
    }

    /**
     * @required
     * @param EntityIdUtils $entityIdUtils
     */
    public function setEntityIdUtils(EntityIdUtils $entityIdUtils): void
    {
        $this->entityIdUtils = $entityIdUtils;
    }


    /**
     * @return string
     */
    abstract public function getEntityClass(): string;


    /**
     * Por precisar declarar a rota como uma anotação, então a estratégia é este método ser abstrato e, por convenção,
     * a chamada ser feita ao doFindById pelo filho no corpo do método.
     *
     * @return JsonResponse
     */
    abstract public function findById(int $id): JsonResponse;


    /**
     * @param int $id
     * @return JsonResponse
     */
    public function doFindById(int $id): JsonResponse
    {
        try {
            /** @var FilterRepository $repo */
            $repo = $this->getDoctrine()->getRepository($this->getEntityClass());
            $r = $repo->find($id);
            $this->handleFindById($r);

            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $serializer = new Serializer([new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)]);
            $serialized = $serializer->normalize($r, 'json',
                ['groups' => ['entity', 'entityId']]);
            $result = array('result' => $serialized);
            return new JsonResponse($result);
        } catch (\Throwable $e) {
            return (new APIProblem(
                400,
                ApiProblem::TYPE_INTERNAL_ERROR
            ))->toJsonResponse();
        }
    }

    /**
     * A ser sobreescrito.
     * Chamado após o retorno do resultado pelo findById.
     * @param array $results
     */
    public function handleFindById($result): void
    {

    }


    /**
     * Por precisar declarar a rota como uma anotação, então a estratégia é este método ser abstrato e, por convenção,
     * a chamada ser feita ao doFindByFilters pelo filho no corpo do método.
     *
     * @param Request $request
     * @return JsonResponse
     */
    abstract public function findByFilters(Request $request): JsonResponse;

    /**
     * Pode-se passar pelo body via post, ou via get com os atributos 'filters', 'start' e 'limit'.
     *
     * @param string $content
     * @return JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function doFindByFilters(Request $request): JsonResponse
    {
        try {
            $filters = null;
            if ($request->query->has('filters')) {
                $filters = json_decode(urldecode($request->query->get('filters')), true);
                $start = $request->query->get('start') ?? 0;
                $limit = $request->query->get('limit') ?? 100;
                $orders = json_decode(urldecode($request->query->get('orders')), true) ?? null;
            } else {
//                $json = json_decode($request->getContent(), true);
//                $filters = $json['filters'];
//                $start = $json['start'] ?? 0;
//                $limit = $json['limit'] ?? 100;
//                $orders = $json['orders'] ?? null;
                $filters = $request->get('filters');
                $start = $request->get('start') ?? 0;
                $limit = $request->get('limit') ?? 100;
                $orders = $request->get('orders') ?? null;

            }


            if (!$filters) {
                throw new \Exception('"filters" não definido');
            }
            // else
            $filterDatas = [];
            foreach ($filters as $filterArray) {
                $filterDatas[] = FilterData::fromArray($filterArray);
            }

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return (new APIProblem(
                400,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
            ))->toJsonResponse();
        }

        try {
            /** @var FilterRepository $repo */
            $repo = $this->getDoctrine()->getRepository($this->getEntityClass());
            $r = $repo->findByFilters($filterDatas, $orders, $start, $limit);
            $this->handleFindByFilters($r);

            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $serializer = new Serializer([new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory)]);
            $serialized = $serializer->normalize($r, 'json',
                ['groups' => ['entity', 'entityId']]);
            $results = array('results' => $serialized);
            return new JsonResponse($results);
        } catch (\Throwable $e) {
            return (new APIProblem(
                400,
                ApiProblem::TYPE_INTERNAL_ERROR
            ))->toJsonResponse();
        }
    }


    /**
     * A ser sobreescrito.
     * Chamado após o retorno dos resultados pelo findByFilters.
     * @param array $results
     */
    public function handleFindByFilters(array $results): void
    {

    }


    /**
     * Por precisar declarar a rota como uma anotação, então a estratégia é este método ser abstrato e, por convenção,
     * a chamada ser feita ao doSave pelo filho no corpo do método.
     *
     * Não é definido como abstract pois o filho pode não necessitar do save().
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request): JsonResponse
    {

    }

    /**
     * @param Request $request
     * @return JsonResponse|APIProblem
     */
    public function doSave(Request $request): JsonResponse
    {
        try {
            $json = json_decode($request->getContent(), true);
            if (!$entityArray = $json['entity']) {
                throw new \Exception('"entity" não definido');
            }
            $entity = $this->entityIdUtils->unserialize($entityArray, $this->getEntityClass());
            $this->prepareEntity($entity);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $apiProblem = new APIProblem(
                400,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
            );
            $apiProblem->set('msg', $e->getMessage());
            return $apiProblem->toJsonResponse();
        }

        try {
            $editando = $entity->getId();
            $e = $this->getEntityHandler()->save($entity);
            $codRet = $editando ? 200 : 201;
            return new JsonResponse(EntityIdUtils::serialize($e), $codRet);
        } catch (\Throwable $e) {
            $errorTratado = ExceptionUtils::treatException($e);
            $apiProblem = new APIProblem(400, ApiProblem::TYPE_INTERNAL_ERROR);
            $apiProblem->set('error', $errorTratado);
            return $apiProblem->toJsonResponse();
        }
    }


    /**
     * Utilizar caso seja necessário algum procedimento antes de salvar a entidade.
     * 
     * @param $entity
     */
    public function prepareEntity(&$entity): void {

    }

    /**
     * @return null|JsonResponse
     */
    public function getNew(): ?JsonResponse
    {
        return null;
    }

    /**
     * @return JsonResponse
     */
    public function doGetNew(): JsonResponse
    {
        $entityClass = $this->getEntityClass();
        return new JsonResponse(['entity' => EntityIdUtils::serialize(new $entityClass)]);
    }


}