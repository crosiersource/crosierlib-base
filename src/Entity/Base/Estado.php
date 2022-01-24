<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Base\EstadoRepository")
 * @ORM\Table(name="bse_uf")
 * @author Carlos Eduardo Pauluk
 */
class Estado implements EntityId
{

    use EntityIdTrait;

    /**
     * @ORM\Column(name="nome", type="string", nullable=false, length=50)
     * @Assert\NotBlank(message="O campo 'nome' deve ser informado")
     * @var null|string
     */
    public ?string $nome = null;

    /**
     * @ORM\Column(name="sigla", type="string", nullable=false, length=2)
     * @Assert\NotBlank(message="O campo 'sigla' deve ser informado")
     */
    public ?string $sigla = null;

    /**
     * @ORM\Column(name="codigoIBGE", type="integer", nullable=false)
     * @Assert\NotBlank(message="O campo 'codigoIBGE' deve ser informado")
     * @Assert\Range(min = 0)
     * @var null|int
     */
    public ?int $codigoIBGE = null;

    
}