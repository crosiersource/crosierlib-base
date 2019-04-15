<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * Entidade 'StoredViewInfo'.
 * Armazena informações sobre estado das páginas.
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Config\StoredViewInfoRepository")
 * @ORM\Table(name="cfg_stored_viewinfo")
 *
 * @author Carlos Eduardo Pauluk
 */
class StoredViewInfo implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="view_name", type="string", length=200, nullable=true)
     * @NotUppercase()
     */
    private $viewName;

    /**
     *
     * @ORM\Column(name="view_info", type="string", length=15000, nullable=true)
     * @NotUppercase()
     */
    private $viewInfo;

    /**
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     *
     */
    public $user;

    /**
     * @return mixed
     */
    public function getViewName()
    {
        return $this->viewName;
    }

    /**
     * @param mixed $viewName
     * @return StoredViewInfo
     */
    public function setViewName($viewName): StoredViewInfo
    {
        $this->viewName = $viewName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getViewInfo()
    {
        return $this->viewInfo;
    }

    /**
     * @param mixed $viewInfo
     * @return StoredViewInfo
     */
    public function setViewInfo($viewInfo): StoredViewInfo
    {
        $this->viewInfo = $viewInfo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return StoredViewInfo
     */
    public function setUser($user): StoredViewInfo
    {
        $this->user = $user;
        return $this;
    }

}