<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity;

/**
 * Interface EntityId
 *
 * @package CrosierSource\CrosierLibBaseBundle\Entity
 * @author Carlos Eduardo Pauluk
 */
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
     * @return string|null
     */
    public function getEstabelecimentoId(): ?string;

    /**
     * @param string|null $estabelecimentoId
     * @return EntityId
     */
    public function setEstabelecimentoId(?string $estabelecimentoId): EntityId;

    /**
     * @return null|string
     */
    public function getUserInsertedId(): ?string;

    /**
     * @param null|string $userInsertedId
     * @return EntityId
     */
    public function setUserInsertedId(?string $userInsertedId): EntityId;

    /**
     * @return string|null
     */
    public function getUserUpdatedId(): ?string;

    /**
     * @param string|null $userUpdatedId
     * @return EntityId
     */
    public function setUserUpdatedId(?string $userUpdatedId): EntityId;


}