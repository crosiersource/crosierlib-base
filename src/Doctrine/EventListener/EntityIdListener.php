<?php

namespace CrosierSource\CrosierLibBaseBundle\Doctrine\EventListener;


use App\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use ReflectionClass;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class EntityIdListener.
 * Listener para todas as entidades. Lida com os atributos da super-classe EntityId.
 *
 * @author Carlos Eduardo Pauluk
 * @package App\EventListener
 */
class EntityIdListener
{

    private $security;

    private $doctrine;

    private $params;

    public function __construct(RegistryInterface $doctrine, Security $security, ParameterBagInterface $params)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->params = $params;
    }


    public function prePersist(LifecycleEventArgs $args)
    {
        $entityId = $args->getObject();
        if (!$entityId instanceof EntityId) return;
        $this->handleUppercaseFields($entityId);
        $entityId->setInserted(new \DateTime('now'));
        if ($this->security->getUser()) {
            $entityId->setEstabelecimento($this->doctrine->getEntityManager()->merge($this->security->getUser()->getEstabelecimento()));
            $entityId->setUserInserted($this->doctrine->getEntityManager()->merge($this->security->getUser()));
        } else {
            // FIXME: corrigir isto
            $user = $this->doctrine->getRepository(User::class)->find(1);
            $entityId->setEstabelecimento($user->getEstabelecimento());
            $entityId->setUserInserted($user);
        }
        $entityId->setUpdated(new \DateTime('now'));
        $entityId->setUserUpdated($entityId->getUserInserted());
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entityId = $args->getObject();
        if (!$entityId instanceof EntityId) return;
        $this->handleUppercaseFields($entityId);
        $entityId->setUpdated(new \DateTime());
        if ($this->security->getUser()) {
            $entityId->setUserUpdated($this->doctrine->getEntityManager()->merge($this->security->getUser()));
        }
    }


    private function handleUppercaseFields($entityId)
    {
        if (!$entityId instanceof EntityId) return;
        $uppercaseFieldsJson = file_get_contents($this->getParams()->get('kernel.project_dir') . '/src/Entity/uppercaseFields.json');
        $uppercaseFields = json_decode($uppercaseFieldsJson);
        $class = str_replace('\\', '_', get_class($entityId));
        $reflectionClass = new ReflectionClass(get_class($entityId));
        $campos = isset($uppercaseFields->$class) ? $uppercaseFields->$class : array();
        foreach ($campos as $field) {
            $property = $reflectionClass->getProperty($field);
            $property->setAccessible(true);
            $property->setValue($entityId, mb_strtoupper($property->getValue($entityId)));
        }
    }

}