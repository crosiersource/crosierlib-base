<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Security;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entidade 'Role'.
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"role","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"role"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/sec/role/{id}"},
 *          "put"={"path"="/sec/role/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"path"="/sec/role/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get"={"path"="/sec/role"},
 *          "post"={"path"="/sec/role", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "csv"={"text/csv"}}
 *     }
 * )
 *
 * @ApiFilter(SearchFilter::class, properties={"role": "exact"})
 * @ApiFilter(OrderFilter::class, properties={"id", "role", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibBaseBundle\EntityHandler\Security\RoleEntityHandler")
 *
 * @ORM\Entity(repositoryClass="\CrosierSource\CrosierLibBaseBundle\Repository\Security\RoleRepository")
 * @ORM\Table(name="sec_role")
 *
 * @author Carlos Eduardo Pauluk
 */
class Role implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="role", type="string", length=90, unique=true)
     * @Groups("role")
     * @var null|string
     */
    public ?string $role = null;

    /**
     *
     * @ORM\Column(name="descricao", type="string", length=90)
     * @Groups("role")
     * @var null|string
     */
    public ?string $descricao = null;
    

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

    public function __toString(): string
    {
        return $this->role;
    }


}

