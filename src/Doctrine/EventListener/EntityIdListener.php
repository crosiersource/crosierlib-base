<?php

namespace CrosierSource\CrosierLibBaseBundle\Doctrine\EventListener;


use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use App\Entity\Security\User;
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

    /**
     * @required
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getDoctrine(): RegistryInterface
    {
        return $this->doctrine;
    }

    /**
     * @required
     * @param Security $security
     */
    public function setSecurity(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return mixed
     */
    public function getParams(): ParameterBagInterface
    {
        return $this->params;
    }

    /**
     * @required
     * @param mixed $params
     */
    public function setParams(ParameterBagInterface $params): void
    {
        $this->params = $params;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entityId = $args->getObject();
        if (!$entityId instanceof EntityId) return;
        $this->handleUppercaseFields($entityId);
        $entityId->setInserted(new \DateTime('now'));
        if ($this->security->getUser()) {
            $entityId->setEstabelecimento($this->getDoctrine()->getEntityManager()->merge($this->security->getUser()->getEstabelecimento()));
            $entityId->setUserInserted($this->getDoctrine()->getEntityManager()->merge($this->security->getUser()));
        } else {
            // FIXME: corrigir isto
            $user = $this->getDoctrine()->getRepository(User::class)->find(1);
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
            $entityId->setUserUpdated($this->getDoctrine()->getEntityManager()->merge($this->security->getUser()));
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