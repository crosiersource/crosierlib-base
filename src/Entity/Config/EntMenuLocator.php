<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"entMenuLocator","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"entMenuLocator"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/cfg/entMenuLocator/{id}"},
 *          "put"={"path"="/cfg/entMenuLocator/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"path"="/cfg/entMenuLocator/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get"={"path"="/cfg/entMenuLocator"},
 *          "post"={"path"="/cfg/entMenuLocator", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "csv"={"text/csv"}}
 *     }
 * )
 * @ApiFilter(PropertyFilter::class)
 *
 * @ApiFilter(SearchFilter::class, properties={"codigo": "exact", "descricao": "partial", "id": "exact"})
 * @ApiFilter(OrderFilter::class, properties={"id", "codigo", "descricao", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibRadxBundle\EntityHandler\Config\EntMenuLocatorEntityHandler")
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Config\EntMenuLocatorRepository")
 * @ORM\Table(name="cfg_entmenu_locator")
 * @author Carlos Eduardo Pauluk
 */
class EntMenuLocator implements EntityId
{

    use EntityIdTrait;

    /**
     * @ORM\Column(name="menu_uuid", type="string", nullable=false, length=36)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $menuUUID = null;

    /**
     *
     * @ORM\Column(name="url_regexp", type="string", nullable=false, length=2000)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $urlRegexp = null;

    /**
     *
     * @ORM\Column(name="nao_contendo", type="string")
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $naoContendo = null;

    /**
     *
     * @ORM\Column(name="quem", type="string", nullable=false, length=2000)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $quem = null;

    
}

