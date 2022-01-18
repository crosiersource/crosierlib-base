<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"app","entityId"},"enable_max_depth"=true},
 *     denormalizationContext={"groups"={"app"},"enable_max_depth"=true},
 *
 *     itemOperations={
 *          "get"={"path"="/cfg/app/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "put"={"path"="/cfg/app/{id}", "security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"path"="/cfg/app/{id}", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     collectionOperations={
 *          "get"={"path"="/cfg/app", "security"="is_granted('ROLE_ADMIN')"},
 *          "post"={"path"="/cfg/app", "security"="is_granted('ROLE_ADMIN')"}
 *     },
 *
 *     attributes={
 *          "pagination_items_per_page"=10,
 *          "formats"={"jsonld", "csv"={"text/csv"}}
 *     }
 * )
 *
 * @ApiFilter(OrderFilter::class, properties={"id", "UUID", "nome", "updated"}, arguments={"orderParameterName"="order"})
 *
 * @EntityHandler(entityHandlerClass="CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppEntityHandler")
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Config\AppRepository")
 * @ORM\Table(name="cfg_app")
 *
 * @author Carlos Eduardo Pauluk
 */
class App implements EntityId
{

    use EntityIdTrait;


    /**
     * @ORM\Column(name="uuid", type="string", nullable=false, length=36)
     * @NotUppercase()
     * @Groups("app")
     *
     * @var string
     */
    private $UUID;

    /**
     *
     * @ORM\Column(name="nome", type="string", nullable=true, length=300)
     * @NotUppercase()
     * @Groups("app")
     *
     * @var string|null
     */
    private $nome;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=5000)
     * @Groups("app")
     *
     * @var string|null
     */
    private $obs;

    /**
     * Transient.
     *
     * @var array|null
     */
    private $configs;


    public function __construct()
    {
        $this->configs = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getUUID(): ?string
    {
        return $this->UUID;
    }

    /**
     * @param string $UUID
     */
    public function setUUID(?string $UUID): void
    {
        $this->UUID = $UUID;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getObs()
    {
        return $this->obs;
    }

    /**
     * @param mixed $obs
     */
    public function setObs($obs): void
    {
        $this->obs = $obs;
    }

    /**
     * @return array|null
     */
    public function getConfigs(): ?array
    {
        return $this->configs;
    }

    /**
     * @param array|null $configs
     * @return App
     */
    public function setConfigs(?array $configs): App
    {
        $this->configs = $configs;
        return $this;
    }


}