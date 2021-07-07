<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait EntityIdTrait
 *
 * @package CrosierSource\CrosierLibBaseBundle\Entity
 * @author Carlos Eduardo Pauluk
 */
trait EntityIdTrait
{

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     * @Groups("entityId")
     */
    private $id;

    /**
     *
     * @ORM\Column(name="inserted", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     * @Groups("entityId")
     *
     * @var null|\DateTime
     */
    private $inserted;

    /**
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     * @Groups("entityId")
     * 
     * @var null|\DateTime
     */
    private $updated;

    /**
     *
     * @ORM\Column(name="estabelecimento_id", type="bigint", nullable=false)
     * @Groups("entityId")
     */
    private $estabelecimentoId;

    /**
     * @ORM\Column(name="user_inserted_id", type="bigint", nullable=false)
     * @Groups("entityId")
     */
    private $userInsertedId;

    /**
     * @ORM\Column(name="user_updated_id", type="bigint", nullable=false)
     * @Groups("entityId")
     */
    private $userUpdatedId;

    /**
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return self
     */
    public function setId(?int $id): EntityId
    {
        $this->id = $id;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getInserted(): ?\DateTime
    {
        return $this->inserted;
    }

    /**
     * @param mixed $inserted
     * @return EntityId
     */
    public function setInserted(?\DateTime $inserted): EntityId
    {
        $this->inserted = $inserted;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     * @return EntityId
     */
    public function setUpdated(?\DateTime $updated): EntityId
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEstabelecimentoId(): ?int
    {
        return $this->estabelecimentoId;
    }

    /**
     * @param mixed $estabelecimentoId
     * @return EntityId
     */
    public function setEstabelecimentoId(?int $estabelecimentoId): EntityId
    {
        $this->estabelecimentoId = $estabelecimentoId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserInsertedId(): ?int
    {
        return $this->userInsertedId;
    }

    /**
     * @param mixed $userInsertedId
     * @return EntityId
     */
    public function setUserInsertedId(?int $userInsertedId): EntityId
    {
        $this->userInsertedId = $userInsertedId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserUpdatedId(): ?int
    {
        return $this->userUpdatedId;
    }

    /**
     * @param mixed $userUpdatedId
     * @return EntityId
     */
    public function setUserUpdatedId(?int $userUpdatedId): EntityId
    {
        $this->userUpdatedId = $userUpdatedId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function __toString(): ?int
    {
        return $this->getId();
    }


}
