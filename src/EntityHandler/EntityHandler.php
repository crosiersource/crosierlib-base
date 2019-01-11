<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler;


use App\Business\Base\EntityIdBusiness;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use App\Exception\ViewException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class EntityHandler
 *
 * Classe abstrata responsável pela lógica ao salvar ou deletar entidades na base de dados.
 * FIXME: se algum dia o PHP suportar herança para tipagem de parâmetros em métodos, adicionar os tipos nos before's e after's aqui.
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
abstract class EntityHandler
{
    protected $entityManager;

    private $security;

    /**
     * EntityHandler constructor.
     * @param RegistryInterface $doctrine
     * @param Security $security
     */
    public function __construct(RegistryInterface $doctrine, Security $security)
    {
        $this->entityManager = $doctrine->getManager();
        $this->security = $security;
    }

    /**
     * A ser sobreescrito.
     *
     * @return mixed
     */
    abstract public function getEntityClass();

    /**
     * A ser sobreescrito.
     *
     * @param $entityId
     */
    public function beforeSave($entityId)
    {
    }

    /**
     * Executa o persist/update e o flush.
     *
     * @param EntityId $entityId
     * @param bool $flush
     * @return EntityId|object
     * @throws ViewException
     */
    public function save(EntityId $entityId, $flush = true)
    {
        $this->beforeSave($entityId);
        if ($entityId->getId()) {
            $entityId = $this->getEntityManager()->merge($entityId);
        } else {
            $this->getEntityManager()->persist($entityId);
        }
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        $this->afterPersist($entityId);
        return $entityId;
    }

    /**
     * A ser sobreescrito.
     *
     * @param $entityId
     */
    public function afterPersist($entityId)
    {
    }

    /**
     * A ser sobreescrito.
     *
     * @param $entityId
     */
    public function beforeDelete($entityId)
    {
    }

    /**
     * Executa o DELETE e o flush.
     *
     * @param $entityId
     */
    public function delete($entityId)
    {
        $this->beforeDelete($entityId);
        $this->entityManager->remove($entityId);
        $this->entityManager->flush();
        $this->afterDelete($entityId);
    }

    /**
     * A ser sobreescrito.
     *
     * @param $entityId
     */
    public function afterDelete($entityId)
    {
    }

    /**
     * A ser sobreescrito.
     *
     * @param $entityId
     */
    public function beforeClone($entityId)
    {
    }

    /**
     * Copia o objeto removendo informações específicas.
     *
     * @param $e
     * @return EntityId|object
     */
    public function doClone($e) {
        $newE = clone $e;
        $newE->setId(null);
        $newE->setInserted(null);
        $newE->setUpdated(null);
        $newE->setUserInserted(null);
        $newE->setUserUpdated(null);
        $this->beforeClone($newE);
        $newE = $this->save($newE);
        $this->getEntityManager()->flush($newE);
        return $newE;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getEntityManager(): \Doctrine\Common\Persistence\ObjectManager
    {
        return $this->entityManager;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

}