<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
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
    private $chave;

    /**
     *
     * @ORM\Column(name="valor", type="text", nullable=true)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    private $valor;

    /**
     * @var string
     * @ORM\Column(name="app_uuid", type="string", nullable=false, length=36)
     * @NotUppercase()
     * @Groups("entity")
     */
    private $appUUID;

    /**
     * @return mixed
     */
    public function getChave(): ?string
    {
        return $this->chave;
    }

    /**
     * @param mixed $chave
     */
    public function setChave(string $chave): void
    {
        $this->chave = $chave;
    }

    /**
     * @return mixed
     */
    public function getValor(): ?string
    {
        return $this->valor;
    }

    /**
     * @param mixed $valor
     */
    public function setValor(string $valor): void
    {
        $this->valor = $valor;
    }

    /**
     * @return string
     */
    public function getAppUUID(): string
    {
        return $this->appUUID;
    }

    /**
     * @param string $appUUID
     * @return AppConfig
     */
    public function setAppUUID(string $appUUID): AppConfig
    {
        $this->appUUID = $appUUID;
        return $this;
    }


}