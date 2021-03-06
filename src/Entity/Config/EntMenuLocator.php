<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
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
    public $menuUUID;

    /**
     *
     * @ORM\Column(name="url_regexp", type="string", nullable=false, length=2000)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var null|string
     */
    public $urlRegexp;

    /**
     *
     * @ORM\Column(name="nao_contendo", type="string")
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var null|string
     */
    public $naoContendo;

    /**
     *
     * @ORM\Column(name="quem", type="string", nullable=false, length=2000)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var null|string
     */
    public $quem;

    /**
     * @return string|null
     */
    public function getMenuUUID(): ?string
    {
        return $this->menuUUID;
    }

    /**
     * @param string|null $menuUUID
     * @return EntMenuLocator
     */
    public function setMenuUUID(?string $menuUUID): EntMenuLocator
    {
        $this->menuUUID = $menuUUID;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrlRegexp(): ?string
    {
        return $this->urlRegexp;
    }

    /**
     * @param string|null $urlRegexp
     * @return EntMenuLocator
     */
    public function setUrlRegexp(?string $urlRegexp): EntMenuLocator
    {
        $this->urlRegexp = $urlRegexp;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNaoContendo(): ?string
    {
        return $this->naoContendo;
    }

    /**
     * @param string|null $naoContendo
     */
    public function setNaoContendo(?string $naoContendo): void
    {
        $this->naoContendo = $naoContendo;
    }

    /**
     * @return string|null
     */
    public function getQuem(): ?string
    {
        return $this->quem;
    }

    /**
     * @param string|null $quem
     * @return EntMenuLocator
     */
    public function setQuem(?string $quem): EntMenuLocator
    {
        $this->quem = $quem;
        return $this;
    }

    
}

