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
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Config\EntMenuRepository")
 * @ORM\Table(name="cfg_entmenu")
 * @author Carlos Eduardo Pauluk
 */
class EntMenu implements EntityId
{

    use EntityIdTrait;

    /**
     * @ORM\Column(name="uuid", type="string", nullable=false, length=36)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    private $UUID;

    /**
     * Necessário para poder montar a URL corretamente (pois o domínio do App pode variar por ambiente).
     *
     * @ORM\Column(name="app_uuid", type="string", nullable=false, length=36)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    private $appUUID;

    /**
     *
     * @ORM\Column(name="label", type="string", nullable=false, length=255)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    private $label;

    /**
     *
     * @ORM\Column(name="icon", type="string", nullable=true, length=50)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    private $icon;

    /**
     *
     * @ORM\Column(name="tipo", type="string", nullable=false, length=50)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $tipo;

    /**
     *
     * @ORM\Column(name="ordem", type="integer", nullable=true)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $ordem;

    /**
     *
     * @ORM\Column(name="css_style", type="string", nullable=true, length=200)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    private $cssStyle;

    /**
     *
     * @ORM\Column(name="roles", type="string", nullable=true, length=200)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $roles;

    /**
     *
     * @ORM\Column(name="url", type="string", nullable=false, length=2000)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(name="pai_uuid", type="string", nullable=true, length=36)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    private $paiUUID;

    /**
     * TRANSIENT
     * @var EntMenu|null
     */
    private $pai;

    /**
     * TRANSIENT
     * @var EntMenu|null
     */
    private $superPai;

    /**
     * TRANSIENT
     * @var null|EntMenu[]|ArrayCollection
     */
    private $filhos;

    /**
     * TRANSIENT.
     * @var int
     */
    private $nivel;

    /**
     * TRANSIENT.
     * @var string
     */
    private string $yaml;


    public function __construct()
    {
        $this->filhos = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getUUID(): ?string
    {
        return $this->UUID;
    }

    /**
     * @param string|null $UUID
     * @return EntMenu
     */
    public function setUUID(?string $UUID): EntMenu
    {
        $this->UUID = $UUID;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAppUUID(): ?string
    {
        return $this->appUUID;
    }

    /**
     * @param string|null $appUUID
     * @return EntMenu
     */
    public function setAppUUID(?string $appUUID): EntMenu
    {
        $this->appUUID = $appUUID;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     * @return EntMenu
     */
    public function setLabel(?string $label): EntMenu
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     * @return EntMenu
     */
    public function setIcon(?string $icon): EntMenu
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    /**
     * @param string|null $tipo
     * @return EntMenu
     */
    public function setTipo(?string $tipo): EntMenu
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrdem(): ?int
    {
        return $this->ordem;
    }

    /**
     * @param int|null $ordem
     * @return EntMenu
     */
    public function setOrdem(?int $ordem): EntMenu
    {
        $this->ordem = $ordem;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCssStyle(): ?string
    {
        return $this->cssStyle;
    }

    /**
     * @param string|null $cssStyle
     * @return EntMenu
     */
    public function setCssStyle(?string $cssStyle): EntMenu
    {
        $this->cssStyle = $cssStyle;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoles(): ?string
    {
        return $this->roles;
    }

    /**
     * @param string|null $roles
     * @return EntMenu
     */
    public function setRoles(?string $roles): EntMenu
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     * @return EntMenu
     */
    public function setUrl(?string $url): EntMenu
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaiUUID()
    {
        return $this->paiUUID;
    }

    /**
     * @param mixed $paiUUID
     * @return EntMenu
     */
    public function setPaiUUID($paiUUID)
    {
        $this->paiUUID = $paiUUID;
        return $this;
    }

    /**
     * @return EntMenu|null
     */
    public function getPai(): ?EntMenu
    {
        return $this->pai;
    }

    /**
     * @param EntMenu|null $pai
     * @return EntMenu
     */
    public function setPai(?EntMenu $pai): EntMenu
    {
        $this->pai = $pai;
        return $this;
    }

    /**
     * @return EntMenu|null
     */
    public function getSuperPai(): ?EntMenu
    {
        return $this->superPai;
    }

    /**
     * @param EntMenu|null $superPai
     * @return EntMenu
     */
    public function setSuperPai(?EntMenu $superPai): EntMenu
    {
        $this->superPai = $superPai;
        return $this;
    }

    /**
     * @return EntMenu[]|ArrayCollection|null
     */
    public function getFilhos()
    {
        return $this->filhos;
    }

    /**
     * @param EntMenu[]|ArrayCollection|null $filhos
     * @return EntMenu
     */
    public function setFilhos($filhos)
    {
        $this->filhos = $filhos;
        return $this;
    }

    /**
     * @return int
     */
    public function getNivel(): int
    {
        return $this->nivel;
    }

    /**
     * @param int $nivel
     * @return EntMenu
     */
    public function setNivel(int $nivel): EntMenu
    {
        $this->nivel = $nivel;
        return $this;
    }

    /**
     * @return string
     */
    public function getYaml(): string
    {
        return $this->yaml;
    }

    /**
     * @param string $yaml
     * @return EntMenu
     */
    public function setYaml(string $yaml): EntMenu
    {
        $this->yaml = $yaml;
        return $this;
    }
    

}

