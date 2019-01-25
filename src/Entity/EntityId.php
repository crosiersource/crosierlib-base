<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity;


interface EntityId
{

    public function getId(): ?int;

    public function setId($id): EntityId;

    /**
     * @return mixed
     */
    public function getInserted();

    /**
     * @param mixed $inserted
     * @return EntityId
     */
    public function setInserted($inserted): EntityId;

    /**
     * @return mixed
     */
    public function getUpdated();

    /**
     * @param mixed $updated
     * @return EntityId
     */
    public function setUpdated($updated): EntityId;

    /**
     * @return mixed
     */
    public function getEstabelecimentoId();

    /**
     * @param mixed $estabelecimentoId
     * @return EntityId
     */
    public function setEstabelecimentoId($estabelecimentoId): EntityId;

    /**
     * @return mixed
     */
    public function getUserInsertedId();

    /**
     * @param mixed $userInsertedId
     * @return EntityId
     */
    public function setUserInsertedId($userInsertedId): EntityId;

    /**
     * @return mixed
     */
    public function getUserUpdatedId();

    /**
     * @param mixed $userUpdatedId
     * @return EntityId
     */
    public function setUserUpdatedId($userUpdatedId): EntityId;


}