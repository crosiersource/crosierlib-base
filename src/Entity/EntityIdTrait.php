<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


trait EntityIdTrait
{

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     *
     * @ORM\Column(name="inserted", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    private $inserted;

    /**
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     * @Assert\Type("\DateTime")
     */
    private $updated;

    /**
     *
     * @ORM\Column(name="estabelecimento_id", type="bigint", nullable=false)
     */
    private $estabelecimentoId;

    /**
     * @ORM\Column(name="user_inserted_id", type="bigint", nullable=false)
     */
    private $userInsertedId;

    /**
     * @ORM\Column(name="user_updated_id", type="bigint", nullable=false)
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
    public function setId($id): EntityId
    {
        $this->id = $id;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getInserted()
    {
        return $this->inserted;
    }

    /**
     * @param mixed $inserted
     * @return EntityId
     */
    public function setInserted($inserted): EntityId
    {
        $this->inserted = $inserted;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     * @return EntityId
     */
    public function setUpdated($updated): EntityId
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEstabelecimentoId()
    {
        return $this->estabelecimentoId;
    }

    /**
     * @param mixed $estabelecimentoId
     * @return EntityId
     */
    public function setEstabelecimentoId($estabelecimentoId): EntityId
    {
        $this->estabelecimentoId = $estabelecimentoId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserInsertedId()
    {
        return $this->userInsertedId;
    }

    /**
     * @param mixed $userInsertedId
     * @return EntityId
     */
    public function setUserInsertedId($userInsertedId): EntityId
    {
        $this->userInsertedId = $userInsertedId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserUpdatedId()
    {
        return $this->userUpdatedId;
    }

    /**
     * @param mixed $userUpdatedId
     * @return EntityId
     */
    public function setUserUpdatedId($userUpdatedId): EntityId
    {
        $this->userUpdatedId = $userUpdatedId;
        return $this;
    }
}