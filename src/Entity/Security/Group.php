<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Security;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entidade 'Group'.
 * 
 * @ApiResource(
 *     normalizationContext={"groups"={"entity","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"entity"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/core/security/group/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "put"={"path"="/core/security/group/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"path"="/core/security/group/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get"={"path"="/core/security/group", "security"="is_granted('ROLE_ADMIN')"},
 *          "post"={"path"="/core/security/group", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "csv"={"text/csv"}}
 *     }
 * )
 *
 * @ApiFilter(SearchFilter::class, properties={"groupname": "exact"})
 * @ApiFilter(OrderFilter::class, properties={"id", "groupname", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibBaseBundle\EntityHandler\Security\GroupEntityHandler")
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
     * @Groups("entity")
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

