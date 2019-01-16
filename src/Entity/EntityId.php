<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

abstract class EntityId
{

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


    public abstract function getId();

    public abstract function setId($id);

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
    public function setInserted($inserted): self
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
    public function setUpdated($updated): self
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
    public function setEstabelecimentoId($estabelecimentoId): self
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
    public function setUserInsertedId($userInsertedId): self
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
    public function setUserUpdatedId($userUpdatedId)
    {
        $this->userUpdatedId = $userUpdatedId;
        return $this;
    }


}