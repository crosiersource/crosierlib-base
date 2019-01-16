<?php


namespace CrosierSource\CrosierLibBaseBundle\EntityHandler;


use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;


interface EntityHandlerInterface
{
    /**
     * Qual a entidade que este EntityHandler trabalhará.
     *
     * @return mixed
     */
    public function getEntityClass();

    /**
     * Executado no início do método save().
     *
     * @param $entityId
     * @return mixed
     */
    public function beforeSave($entityId);

    /**
     * Executa o persist/update e o flush.
     *
     * @param EntityId $entityId
     * @param bool $flush
     * @return EntityId|object
     * @throws ViewException
     */
    public function save(EntityId $entityId, $flush = true);

    /**
     * Executado após o save().
     *
     * @param $entityId
     */
    public function afterSave($entityId);

    /**
     * Executado no início do método delete().
     *
     * @param $entityId
     */
    public function beforeDelete($entityId);

    /**
     * Executa o DELETE e o flush.
     *
     * @param $entityId
     */
    public function delete($entityId);

    /**
     * Executado após o delete().
     *
     * @param $entityId
     */
    public function afterDelete($entityId);

    /**
     * Executado no início do método clone().
     *
     * @param $entityId
     */
    public function beforeClone($entityId);

    /**
     * Copia o objeto removendo informações específicas.
     *
     * @param $e
     * @return EntityId|object
     */
    public function doClone($e);


}