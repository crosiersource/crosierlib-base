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
use Symfony\Component\Serializer\Annotation\SerializedName;


/**
 * @ApiResource(
 *     normalizationContext={"groups"={"entity","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"entity"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={
 *              "path"="/core/config/syslog/{id}",
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "normalization_context"={"groups"={"entity","entityId", "obs"}},
 *             },
 *          "put"={"path"="/core/config/syslog/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"path"="/core/config/syslog/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "path"="/core/config/syslog",
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "normalization_context"={"groups"={"entity","entityId", "obsp"}},
 *              },
 *          "post"={"path"="/core/config/syslog", "security"="is_granted('ROLE_ADMIN')"}
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
 *     "id": "exact",
 *     "app": "exact",
 *     "uuidSess": "exact",
 *     "tipo": "exact",
 *     "component": "partial",
 *     "act": "partial",
 *     "username": "exact",
 *     "obs": "partial"
 * })
 * 
 * @ApiFilter(OrderFilter::class, properties={"id", "app", "component", "moment", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\SyslogHandler")
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Config\SyslogRepository")
 * @ORM\Table(name="cfg_syslog")
 *
 * @author Carlos Eduardo Pauluk
 */
class Syslog
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
     * @ORM\Column(name="uuid_sess", type="string")
     * @Groups("entity")
     * @var string|null
     */
    public ?string $uuidSess = null;
    
    /**
     * @ORM\Column(name="tipo", type="string")
     * @Groups("entity")
     * @var string|null
     */
    public ?string $tipo = null;

    /**
     * @ORM\Column(name="app", type="string")
     * @Groups("entity")
     * @var string|null
     */
    public ?string $app = null;

    /**
     * @ORM\Column(name="component", type="string")
     * @Groups("entity")
     * @var string|null
     */
    public ?string $component = null;

    /**
     * @ORM\Column(name="act", type="string")
     * @Groups("entity")
     * @var string|null
     */
    public ?string $act = null;

    /**
     * @ORM\Column(name="username", type="string")
     * @Groups("entity")
     * @NotUppercase()
     * @var string|null
     */
    public ?string $username = null;

    /**
     * @ORM\Column(name="moment", type="datetime")
     * @Groups("entity")
     * @var null|\DateTime
     */
    public ?\DateTime $moment = null;

    /**
     * @ORM\Column(name="obs", type="string")
     * @Groups("obs")
     * @var string|null
     */
    public ?string $obs = null;

    /**
     * @ORM\Column(name="delete_after", type="datetime")
     * @Groups("entityId")
     * @var null|\DateTime
     */
    public ?\DateTime $deleteAfter = null;

    /**
     * @ORM\Column(name="json_data", type="json")
     * @var null|array
     * @NotUppercase()
     * @Groups("entity")
     */
    public ?array $jsonData = null;


    /**
     * @Groups("obsp")
     * @SerializedName("obs")
     * @var null|string
     */
    public function getObsp(): ?string
    {
        return $this->obs ? (substr($this->obs, 0, 200) . '...') : null;
    }


}
