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
    private ?int $id = null;

    /**
     *
     * @ORM\Column(name="inserted", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     * @Groups("entityId")
     *
     * @var null|\DateTime
     */
    private ?\DateTime $inserted = null;

    /**
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     * @Groups("entityId")
     *
     * @var null|\DateTime
     */
    private ?\DateTime $updated = null;

    /**
     * @ORM\Column(name="estabelecimento_id", type="bigint", nullable=false)
     * @Groups("entityId")
     * @var null|string
     */
    private ?string $estabelecimentoId = null;

    /**
     * @ORM\Column(name="user_inserted_id", type="bigint", nullable=false)
     * @Groups("entityId")
     * @var null|string
     */
    private ?string $userInsertedId = null;

    /**
     * @ORM\Column(name="user_updated_id", type="bigint", nullable=false)
     * @Groups("entityId")
     * @var null|string
     */
    private ?string $userUpdatedId = null;

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
     * @return null|string
     */
    public function getEstabelecimentoId(): ?string
    {
        return $this->estabelecimentoId;
    }

    /**
     * @param null|string $estabelecimentoId
     * @return EntityId
     */
    public function setEstabelecimentoId(?string $estabelecimentoId): EntityId
    {
        $this->estabelecimentoId = $estabelecimentoId;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getUserInsertedId(): ?string
    {
        return (string) $this->userInsertedId;
    }

    /**
     * @param null|string
     * @return EntityId
     */
    public function setUserInsertedId($userInsertedId): EntityId
    {
        $this->userInsertedId = $userInsertedId;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getUserUpdatedId(): ?string
    {
        return $this->userUpdatedId;
    }

    /**
     * @param null|string $userUpdatedId
     * @return EntityId
     */
    public function setUserUpdatedId(?string $userUpdatedId): EntityId
    {
        $this->userUpdatedId = $userUpdatedId;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return '' . (string)($this->getId() ?? '');
    }


}
