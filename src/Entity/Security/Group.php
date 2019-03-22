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
 *
 * @author Carlos Eduardo Pauluk
 */
class Group implements EntityId
{
    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="groupname", type="string", length=90, unique=true)
     * @var null|string
     */
    private $groupname;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="sec_group_role",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     * @var Collection
     */
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * @return null|string
     */
    public function getGroupname(): ?string
    {
        return $this->groupname;
    }

    /**
     * @param null|string $groupname
     * @return Group
     */
    public function setGroupname(?string $groupname): Group
    {
        $this->groupname = $groupname;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @param Collection $roles
     * @return Group
     */
    public function setRoles(Collection $roles): Group
    {
        $this->roles = $roles;
        return $this;
    }


}

