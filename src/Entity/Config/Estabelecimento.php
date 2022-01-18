<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entidade 'Estabelecimento'.
 *
 * @ApiResource(
 *     normalizationContext={"groups"={"estabelecimento","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"estabelecimento"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/cfg/estabelecimento/{id}"},
 *          "put"={"path"="/cfg/estabelecimento/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"path"="/cfg/estabelecimento/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get"={"path"="/cfg/estabelecimento"},
 *          "post"={"path"="/cfg/estabelecimento", "security"="is_granted('ROLE_ADMIN')"}
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
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibRadxBundle\EntityHandler\Config\EstabelecimentoEntityHandler")
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Config\EstabelecimentoRepository")
 * @ORM\Table(name="cfg_estabelecimento")
 */
class Estabelecimento implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Groups("estabelecimento")
     * @var null|int
     */
    public ?int $codigo = null;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=true, length=40)
     * @Groups("estabelecimento")
     * @var null|string
     */
    public ?string $descricao = null;

    /**
     *
     * @ORM\Column(name="concreto", type="boolean", nullable=false)
     * @Groups("estabelecimento")
     * @var null|bool
     */
    public ?bool $concreto = false;


}

