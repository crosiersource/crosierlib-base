<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Base;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *       normalizationContext={"groups"={"estado","entityId"},"enable_max_depth"=true},
 *       denormalizationContext={"groups"={"estado"},"enable_max_depth"=true},
 *
 *       itemOperations={
 *            "get"={"path"="/bse/estado/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *            "put"={"path"="/bse/estado/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *            "delete"={"path"="/bse/estado/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *       },
 *       collectionOperations={
 *            "get"={"path"="/bse/estado", "security"="is_granted('ROLE_ADMIN')"},
 *            "post"={"path"="/bse/estado", "security"="is_granted('ROLE_ADMIN')"}
 *       },
 *
 *       attributes={
 *            "pagination_items_per_page"=10,
 *            "formats"={"jsonld", "csv"={"text/csv"}}
 *       }
 *   )
 *
 * @ApiFilter(OrderFilter::class, properties={"id", "UUID", "nome", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibBaseBundle\EntityHandler\Base\EstadoEntityHandler")
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Base\EstadoRepository")
 * @ORM\Table(name="bse_uf")
 * @author Carlos Eduardo Pauluk
 */
class Estado implements EntityId
{

    use EntityIdTrait;

    /**
     * @ORM\Column(name="nome", type="string", nullable=false, length=50)
     * @Assert\NotBlank(message="O campo 'nome' deve ser informado")
     * @Groups("estado")
     * @var null|string
     */
    public ?string $nome = null;

    /**
     * @ORM\Column(name="sigla", type="string", nullable=false, length=2)
     * @Assert\NotBlank(message="O campo 'sigla' deve ser informado")
     * @Groups("estado")
     */
    public ?string $sigla = null;

    /**
     * @ORM\Column(name="codigo_IBGE", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'codigoIBGE' deve ser informado")
     * @Assert\Range(min = 0)
     * @Groups("estado")
     * @var null|int
     */
    public ?int $codigoIBGE = null;


}