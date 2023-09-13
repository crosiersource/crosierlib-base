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

/**
 * @ApiResource(
 *      normalizationContext={"groups"={"municipio","entityId"},"enable_max_depth"=true},
 *      denormalizationContext={"groups"={"municipio"},"enable_max_depth"=true},
 *
 *      itemOperations={
 *           "get"={"path"="/bse/municipio/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *           "put"={"path"="/bse/municipio/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *           "delete"={"path"="/bse/municipio/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *      },
 *      collectionOperations={
 *           "get"={"path"="/bse/municipio", "security"="is_granted('ROLE_ADMIN')"},
 *           "post"={"path"="/bse/municipio", "security"="is_granted('ROLE_ADMIN')"}
 *      },
 *
 *      attributes={
 *           "pagination_items_per_page"=10,
 *           "formats"={"jsonld", "csv"={"text/csv"}}
 *      }
 *  )
 *
 * @ApiFilter(OrderFilter::class, properties={"id", "UUID", "nome", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibBaseBundle\EntityHandler\Base\MunicipioEntityHandler")
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Base\MunicipioRepository")
 * @ORM\Table(name="bse_municipio")
 * @author Carlos Eduardo Pauluk
 */
class Municipio implements EntityId
{

    use EntityIdTrait;

    /**
     * @ORM\Column(name="municipio_codigo", type="integer", nullable=false)
     * @Groups("municipio")
     * @var null|int
     */
    public ?int $municipioCodigo = null;


    /**
     * @ORM\Column(name="municipio_nome", type="string", nullable=true, length=200)
     * @Groups("municipio")
     * @var null|string
     */
    public ?string $municipioNome = null;


    /**
     * @ORM\Column(name="uf_nome", type="string", nullable=true, length=200)
     * @Groups("municipio")
     * @var null|string
     */
    public ?string $ufNome = null;


    /**
     * @ORM\Column(name="uf_sigla", type="string", nullable=true, length=2)
     * @Groups("municipio")
     * @var null|string
     */
    public ?string $ufSigla = null;


}
