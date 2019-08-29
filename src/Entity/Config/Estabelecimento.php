<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entidade 'Estabelecimento'.
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Config\EstabelecimentoRepository")
 * @ORM\Table(name="cfg_estabelecimento")
 */
class Estabelecimento implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Groups("entity")
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=true, length=40)
     * @Groups("entity")
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="concreto", type="boolean", nullable=false)
     * @Groups("entity")
     */
    private $concreto = false;

    /**
     *
     * @ORM\ManyToOne(targetEntity="CrosierSource\CrosierLibBaseBundle\Entity\Config\Estabelecimento", cascade={"persist"})
     * @ORM\JoinColumn(name="pai_id", nullable=false)
     *
     */
    public $pai;


    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param mixed $codigo
     */
    public function setCodigo($codigo): void
    {
        $this->codigo = $codigo;
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
     */
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * @return mixed
     */
    public function getConcreto()
    {
        return $this->concreto;
    }

    /**
     * @param mixed $concreto
     */
    public function setConcreto($concreto): void
    {
        $this->concreto = $concreto;
    }

    /**
     * @return mixed
     */
    public function getPai()
    {
        return $this->pai;
    }

    /**
     * @param mixed $pai
     */
    public function setPai($pai): void
    {
        $this->pai = $pai;
    }


}

