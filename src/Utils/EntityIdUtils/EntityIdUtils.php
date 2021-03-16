<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\EntityIdUtils;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Normalizer\EntityNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class EntityIdUtils.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Utils\EntitIdyUtils
 * @author Carlos Eduardo Pauluk
 */
class EntityIdUtils
{

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * Constrói um array de entidades sendo as chaves as ids.
     * @param array $entities
     * @return array
     */
    public static function buildArrayComIdsComoChaves(array $entities)
    {
        $r = [];
        foreach ($entities as $entity) {
            if ($entity instanceof EntityId) {
                $r[$entity->getId()] = $entity;
            } else {
                $r[$entity['id']] = $entity;

            }
        }
        return $r;
    }

    /**
     * @param array $entities
     * @param int $circularReferenceLimit
     * @param array $groups
     * @return array
     */
    public function serializeAll(array $entities, int $circularReferenceLimit = 3, array $groups = ['entity', 'entityId']): array
    {
        $a = [];
        foreach ($entities as $e) {
            $a[] = $this->serialize($e, $circularReferenceLimit, $groups);
        }
        return $a;
    }

    /**
     * Serializa uma entidade para um json.
     *
     * @param EntityId $entityId
     * @param int $circularReferenceLimit
     * @param array $groups
     * @return array
     */
    public static function serialize(EntityId $entityId, int $circularReferenceLimit = 3, array $groups = ['entity', 'entityId']): array
    {
        try {
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $normalizer = new ObjectNormalizer($classMetadataFactory, null, null, new ReflectionExtractor());
            $serializer = new Serializer([new DateTimeNormalizer(), $normalizer, new ArrayDenormalizer()]);
            return $serializer->normalize($entityId, 'json',
                [
                    'groups' => $groups,
                    'circular_reference_limit' => $circularReferenceLimit,
                    'enable_max_depth' => true
                ]);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro ao serializar', 0, $e);
        }
    }

    /**
     * Deserializa um array de uma entidade para o tipo $type.
     *
     * @param array $entityArray
     * @param string $type
     * @return object
     */
    public function unserialize(array $entityArray, string $type, int $circularReferenceLimit = 3, array $groups = ['entity', 'entityId'])
    {
        try {
            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $normalizer = new ObjectNormalizer($classMetadataFactory, null, null, new ReflectionExtractor());
            $entityNormalizer = new EntityNormalizer($this->em);
            $serializer = new Serializer([new DateTimeNormalizer(), new ArrayDenormalizer(), $entityNormalizer, $normalizer]);
            return $serializer->denormalize($entityArray, $type, 'json',
                [
                    ObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
                    'groups' => $groups,
                    'circular_reference_limit' => $circularReferenceLimit,
                    'enable_max_depth' => true
                ]);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro ao deserializar', 0, $e);
        }

    }


}
