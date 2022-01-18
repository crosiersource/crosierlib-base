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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entidade 'Group'.
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"group","role","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"group"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/sec/group/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "put"={"path"="/sec/group/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"path"="/sec/group/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get"={"path"="/sec/group", "security"="is_granted('ROLE_ADMIN')"},
 *          "post"={"path"="/sec/group", "security"="is_granted('ROLE_ADMIN')"}
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
     * @Groups("group")
     */
    public ?string $groupname = null;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="sec_group_role",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     * @Groups("group")
     */
    public $roles;


    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    
}

