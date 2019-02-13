<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity;


interface EntityId
{

    public function getId(): ?int;

    public function setId(?int $id): EntityId;

    /**
     * @return mixed
     */
    public function getInserted(): ?\DateTime;

    /**
     * @param \DateTime|null $inserted
     * @return EntityId
     */
    public function setInserted(?\DateTime $inserted): EntityId;

    /**
     * @return \DateTime|null
     */
    public function getUpdated(): ?\DateTime;

    /**
     * @param \DateTime $updated
     * @return EntityId
     */
    public function setUpdated(\DateTime $updated): EntityId;

    /**
     * @return int|null
     */
    public function getEstabelecimentoId(): ?int;

    /**
     * @param int|null $estabelecimentoId
     * @return EntityId
     */
    public function setEstabelecimentoId(?int $estabelecimentoId): EntityId;

    /**
     * @return mixed
     */
    public function getUserInsertedId(): ?int;

    /**
     * @param $userInsertedId
     * @return EntityId
     */
    public function setUserInsertedId(?int $userInsertedId): EntityId;

    /**
     * @return int|null
     */
    public function getUserUpdatedId(): ?int;

    /**
     * @param int|null $userUpdatedId
     * @return EntityId
     */
    public function setUserUpdatedId(?int $userUpdatedId): EntityId;


}