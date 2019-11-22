<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Config\EntityChangeRepository")
 * @ORM\Table(name="cfg_entity_change")
 *
 * @author Carlos Eduardo Pauluk
 */
class EntityChange
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
     * @ORM\Column(name="entity_class", type="string", nullable=false)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string
     */
    private $entityClass;

    /**
     *
     * @ORM\Column(name="entity_id", type="bigint", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $entityId;

    /**
     *
     * @ORM\Column(name="changing_user_id", type="bigint", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $changingUserId;

    /**
     *
     * @ORM\Column(name="changed_at", type="datetime", nullable=false)
     * @Groups("entity")
     *
     * @var null|\DateTime
     */
    private $changedAt;

    /**
     *
     * @ORM\Column(name="changes", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $obs;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return EntityChange
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     * @return EntityChange
     */
    public function setEntityClass(string $entityClass): EntityChange
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    /**
     * @param int|null $entityId
     * @return EntityChange
     */
    public function setEntityId(?int $entityId): EntityChange
    {
        $this->entityId = $entityId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getChangingUserId(): ?int
    {
        return $this->changingUserId;
    }

    /**
     * @param int|null $changingUserId
     * @return EntityChange
     */
    public function setChangingUserId(?int $changingUserId): EntityChange
    {
        $this->changingUserId = $changingUserId;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getChangedAt(): ?\DateTime
    {
        return $this->changedAt;
    }

    /**
     * @param \DateTime|null $changedAt
     * @return EntityChange
     */
    public function setChangedAt(?\DateTime $changedAt): EntityChange
    {
        $this->changedAt = $changedAt;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getObs(): ?string
    {
        return $this->obs;
    }

    /**
     * @param string|null $obs
     * @return EntityChange
     */
    public function setObs(?string $obs): EntityChange
    {
        $this->obs = $obs;
        return $this;
    }


}