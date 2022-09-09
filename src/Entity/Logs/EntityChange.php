<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Logs;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ApiResource(
 *     normalizationContext={"groups"={"entity","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"entity"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/core/config/entityChange/{id}", "security"="is_granted('ROLE_ENTITY_CHANGES')"},
 *          "put"={"path"="/core/config/entityChange/{id}", "security"="is_granted('ROLE_ENTITY_CHANGES')"},
 *          "delete"={"path"="/core/config/entityChange/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get"={"path"="/core/config/entityChange", "security"="is_granted('ROLE_ENTITY_CHANGES')"},
 *          "post"={"path"="/core/config/entityChange", "security"="is_granted('ROLE_ENTITY_CHANGES')"}
 *     },
 *
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "csv"={"text/csv"}}
 *     }
 * )
 *
 * @ApiFilter(DateFilter::class, properties={"moment"})
 * 
 * @ApiFilter(SearchFilter::class, properties={
 *     "entityClass": "partial", 
 *     "entityId": "exact", 
 *     "component": "partial", 
 *     "act": "partial", 
 *     "username": "partial", 
 *     "obs": "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={
 *     "id", 
 *     "app", 
 *     "component", 
 *     "moment", 
 *     "updated"
 * }, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\EntityChangeHandler")
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Config\EntityChangeRepository")
 * @ORM\Table(name="cfg_entity_change")
 *
 * @author Carlos Eduardo Pauluk
 */
class EntityChange
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     * @Groups("entityId")
     * @var null|int
     */
    public ?int $id = null;

    /**
     * @ORM\Column(name="entity_class", type="string", nullable=false)
     * @NotUppercase()
     * @Groups("entity")
     * @var null|string
     */
    public ?string $entityClass = null;

    /**
     * @ORM\Column(name="entity_id", type="bigint", nullable=false)
     * @Groups("entity")
     * @var int|null
     */
    public ?int $entityId = null;

    /**
     * @ORM\Column(name="changing_user_id", type="bigint", nullable=false)
     * @Groups("entity")
     * @var int|null
     */
    public ?int $changingUserId = null;

    /**
     * @ORM\Column(name="changing_user_username", type="string", nullable=false)
     * @Groups("entity")
     * @var string|null
     */
    public ?string $changingUserUsername = null;

    /**
     * @ORM\Column(name="changing_user_nome", type="string", nullable=false)
     * @Groups("entity")
     * @var string|null
     */
    public ?string $changingUserNome = null;

    /**
     * @ORM\Column(name="changed_at", type="datetime", nullable=false)
     * @Groups("entity")
     * @var null|\DateTime
     */
    public ?\DateTime $changedAt = null;

    /**
     * @ORM\Column(name="changes", type="string", nullable=false)
     * @Groups("entity")
     * @var string|null
     */
    public ?string $obs = null;


}
