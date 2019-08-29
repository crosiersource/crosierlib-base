<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use CrosierSource\CrosierLibBaseBundle\Entity\Utils\EnderecoTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entidade Pessoa.
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Base\PessoaEnderecoRepository")
 * @ORM\Table(name="bse_pessoa_endereco")
 * @author Carlos Eduardo Pauluk
 */
class PessoaEndereco implements EntityId
{

    use EntityIdTrait;

    use EnderecoTrait;

    /**
     *
     * @ORM\ManyToOne(targetEntity="CrosierSource\CrosierLibBaseBundle\Entity\Base\Pessoa")
     * @ORM\JoinColumn(name="pessoa_id", nullable=false)
     * @var Pessoa|null
     */
    private $pessoa;

    /**
     * @return Pessoa|null
     */
    public function getPessoa(): ?Pessoa
    {
        return $this->pessoa;
    }

    /**
     * @param Pessoa|null $pessoa
     */
    public function setPessoa(?Pessoa $pessoa): void
    {
        $this->pessoa = $pessoa;
    }


}

