<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
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
     * @Groups("entity")
     *
     * @var string
     */
    private $UUID;

    /**
     *
     * @ORM\Column(name="nome", type="string", nullable=true, length=300)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    private $nome;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=5000)
     * @Groups("entity")
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