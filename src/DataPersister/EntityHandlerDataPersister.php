<?php

namespace CrosierSource\CrosierLibBaseBundle\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;

/**
 * @author Carlos Eduardo Pauluk
 */
class EntityHandlerDataPersister implements DataPersisterInterface
{

    private ContainerInterface $container;

    private EntityManagerInterface $doctrine;

    private EntityHandler $entityHandler;

    private EntityId $entityId;

    public function __construct(ContainerInterface $container, EntityManagerInterface $doctrine)
    {
        $this->container = $container;
        $this->doctrine = $doctrine;
    }

    public function supports($data): bool
    {
        if ($data instanceof EntityId) {
            $classAnnotations = (new AnnotationReader())->getClassAnnotations(new ReflectionClass(get_class($data)));
            foreach ($classAnnotations as $annot) {
                if ($annot instanceof \CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler) {
                    $this->entityHandler = $this->container->get($annot->entityHandlerClass);
                    return true;
                }
            }
        }
        return false;
    }


    public function persist($entityId)
    {
        $this->entityHandler->save($entityId);
    }


    public function remove($entityId)
    {
        $this->entityHandler->delete($entityId);
    }
}
