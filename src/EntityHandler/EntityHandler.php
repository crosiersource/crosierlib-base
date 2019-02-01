<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler;


use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Classe abstrata responsável pela lógica ao salvar ou deletar entidades na base de dados.
 *
 * FIXME: se algum dia o PHP suportar herança para tipagem de parâmetros em métodos, adicionar os tipos nos before's e after's aqui.
 *
 * @package App\EntityHandler
 * @author Carlos Eduardo Pauluk
 */
abstract class EntityHandler implements EntityHandlerInterface
{

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     *
     * @return mixed
     */
    abstract public function getEntityClass();

    /**
     * Implementação vazia pois não é obrigatório.
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
        try {
            $this->beforeSave($entityId);
            if ($entityId->getId()) {
                $entityId = $this->doctrine->getEntityManager()->merge($entityId);
            } else {
                $this->doctrine->getEntityManager()->persist($entityId);
            }
            if ($flush) {
                $this->doctrine->getEntityManager()->flush();
            }
            $this->afterSave($entityId);
        } catch (\Exception $e) {
            throw new ViewException('Erro ao salvar', 0, $e);
        }
        return $entityId;
    }

    /**
     * Implementação vazia pois não é obrigatório.
     *
     * @param $entityId
     */
    public function afterSave($entityId)
    {
    }

    /**
     * Implementação vazia pois não é obrigatório.
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
     * @throws ViewException
     */
    public function delete($entityId)
    {
        try {
            $this->beforeDelete($entityId);
            $this->doctrine->getEntityManager()->remove($entityId);
            $this->doctrine->getEntityManager()->flush();
            $this->afterDelete($entityId);
        } catch (\Exception $e) {
            throw new ViewException('Erro ao deletar.', 0, $e);
        }
    }

    /**
     * Implementação vazia pois não é obrigatório.
     *
     * @param $entityId
     */
    public function afterDelete($entityId)
    {
    }

    /**
     * Implementação vazia pois não é obrigatório.
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
     * @throws ViewException
     */
    public function doClone($e)
    {
        /** @var EntityId $newE */
        $newE = clone $e;
        $newE->setId(null);
        $newE->setInserted(null);
        $newE->setUpdated(null);
        $newE->setUserInsertedId(null);
        $newE->setUserUpdatedId(null);
        $this->beforeClone($newE);
        $this->save($newE);
        return $newE;
    }


}