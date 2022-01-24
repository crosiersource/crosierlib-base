<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"entity","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"entity"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/cfg/appConfig/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "put"={"path"="/cfg/appConfig/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"path"="/cfg/appConfig/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get"={"path"="/cfg/appConfig", "security"="is_granted('ROLE_ADMIN')"},
 *          "post"={"path"="/cfg/appConfig", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "csv"={"text/csv"}}
 *     }
 * )
 *
 * @ApiFilter(SearchFilter::class, properties={"appUUID": "exact", "chave": "partial"})
 * @ApiFilter(OrderFilter::class, properties={"chave"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler")
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository")
 * @ORM\Table(name="cfg_app_config")
 *
 * @author Carlos Eduardo Pauluk
 */
class AppConfig implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="chave", type="string", nullable=false, length=255)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $chave = null;

    /**
     *
     * @ORM\Column(name="valor", type="text", nullable=true)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $valor = null;

    /**
     * @ORM\Column(name="is_json", type="boolean", nullable=false)
     * @Groups("entity")
     *
     * @var bool|null
     */
    public ?bool $isJson = false;

    /**
     * @var string
     * @ORM\Column(name="app_uuid", type="string", nullable=false, length=36)
     * @NotUppercase()
     * @Groups("entity")
     */
    public ?string $appUUID = null;

    public function __construct(?string $chave = null, ?string $appUUID = null)
    {
        $this->chave = $chave;
        $this->appUUID = $appUUID;
    }

    

    public function getValorJsonDecoded(): ?array
    {
        if ($this->isJson || strpos($this->chave, '.json') !== FALSE) {
            return json_decode($this->valor, true);
        } else {
            return null;
        }
    }


}