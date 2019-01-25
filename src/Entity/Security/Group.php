<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade 'Group'.
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Security\GroupRepository")
 * @ORM\Table(name="sec_group")
 */
class Group implements EntityId
{
    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="groupname", type="string", length=90, unique=true)
     */
    private $groupname;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="sec_group_role",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getGroupname()
    {
        return $this->groupname;
    }

    /**
     * @param mixed $groupname
     */
    public function setGroupname($groupname): void
    {
        $this->groupname = $groupname;
    }

    /**
     *
     * @return Collection|Role[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }


}

