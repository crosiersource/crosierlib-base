<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade 'Dia Útil'.
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Base\DiaUtilRepository")
 * @ORM\Table(name="bse_diautil")
 * @author Carlos Eduardo Pauluk
 */
class DiaUtil implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="dia", type="datetime", nullable=false)
     */
    private $dia;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=true, length=40)
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="comercial", type="boolean", nullable=false)
     */
    private $comercial = false;

    /**
     *
     * @ORM\Column(name="financeiro", type="boolean", nullable=false)
     */
    private $financeiro = false;

    /**
     * @return mixed
     */
    public function getDia()
    {
        return $this->dia;
    }

    /**
     * @param mixed $dia
     * @return DiaUtil
     */
    public function setDia($dia)
    {
        $this->dia = $dia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param mixed $descricao
     * @return DiaUtil
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComercial()
    {
        return $this->comercial;
    }

    /**
     * @param mixed $comercial
     * @return DiaUtil
     */
    public function setComercial($comercial)
    {
        $this->comercial = $comercial;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinanceiro()
    {
        return $this->financeiro;
    }

    /**
     * @param mixed $financeiro
     * @return DiaUtil
     */
    public function setFinanceiro($financeiro)
    {
        $this->financeiro = $financeiro;
        return $this;
    }


}

