<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade 'Role'.
 *
 * @ORM\Entity(repositoryClass="\CrosierSource\CrosierLibBaseBundle\Repository\Security\RoleRepository")
 * @ORM\Table(name="sec_role")
 */
class Role implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="role", type="string", length=90, unique=true)
     */
    private $role;

    /**
     *
     * @ORM\Column(name="descricao", type="string", length=90)
     */
    private $descricao;

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }


}

