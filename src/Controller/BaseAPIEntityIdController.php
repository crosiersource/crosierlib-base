<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;

use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\APIUtils\APIProblem;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Doctrine\Common\Annotations\AnnotationReader;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class APIBaseController.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Controller
 * @author Carlos Eduardo Pauluk
 */
abstract class BaseAPIEntityIdController extends AbstractController
{

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }


    /**
     * @return string
     */
    abstract public function getEntityClass(): string;


    /**
     * @param int $id
     * @return JsonResponse
     */
    public function findById(int $id): JsonResponse
    {
        try {
            /** @var FilterRepository $repo */
            $repo = $this->getDoctrine()->getRepository($this->getEntityClass());
            $r = $repo->find($id);

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
     * @param Request $request
     * @return JsonResponse
     */
    abstract public function findByFilters(Request $request): JsonResponse;

    /**
     * @param string $content
     * @return JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function doFindByFilters(string $content): JsonResponse
    {
        try {
            $this->logger->debug($content);
            $json = json_decode($content, true);
            $filtersArray = $json['filters'];
            $orders = isset($json['orders']) ? $json['orders'] : null;
            $start = isset($json['start']) ? $json['start'] : 0;
            $limit = isset($json['start']) ? $json['limit'] : 100;
            if (!$filtersArray) {
                throw new \Exception('"filters" não definido');
            } else {
                $filterDatas = [];
                foreach ($filtersArray as $filterArray) {
                    $filterDatas[] = FilterData::fromArray($filterArray);
                }
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


}