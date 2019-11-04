<?php

namespace CrosierSource\CrosierLibBaseBundle\Entity\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entidade Pessoa.
 *
 * @ORM\Entity(repositoryClass="CrosierSource\CrosierLibBaseBundle\Repository\Base\PessoaRepository")
 * @ORM\Table(name="bse_pessoa")
 * @author Carlos Eduardo Pauluk
 */
class Pessoa implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * Para Pessoa Jurídica é a Razão Social.
     *
     * @ORM\Column(name="nome", type="string", nullable=false, length=300)
     * @var null|string
     * @Groups("entity")
     */
    private $nome;

    /**
     *
     * CPF/CNPJ.
     *
     * @ORM\Column(name="documento", type="string", nullable=true, length=20)
     * @var null|string
     * @Groups("entity")
     */
    private $documento;

    /**
     * No banco é um ENUM('Pessoa Física', 'Pessoa Jurídica')
     *
     * @ORM\Column(name="tipo", type="string", nullable=false, length=100)
     * @var null|string
     * @Groups("entity")
     */
    private $tipo;

    /**
     *
     * @ORM\ManyToMany(targetEntity="CategoriaPessoa")
     * @ORM\JoinTable(name="bse_pessoa_categ_pessoa",
     *      joinColumns={@ORM\JoinColumn(name="pessoa_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="categ_id", referencedColumnName="id")}
     * )
     * @var null|CategoriaPessoa[]|array|Collection
     * @Groups("entity")
     */
    private $categorias;

    /**
     *
     * @ORM\Column(name="nome_fantasia", type="string", nullable=true, length=255)
     * @var null|string
     * @Groups("entity")
     */
    private $nomeFantasia;

    /**
     *
     * @ORM\Column(name="ie", type="string", nullable=true, length=20)
     * @var null|string
     * @Groups("entity")
     */
    private $inscricaoEstadual;

    /**
     *
     * @ORM\Column(name="rg", type="string", nullable=true, length=20)
     * @var null|string
     * @Groups("entity")
     */
    private $rg;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=true, length=3000)
     * @var null|string
     * @Groups("entity")
     */
    private $obs;


    /**
     *
     * @var PessoaEndereco[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="PessoaEndereco",
     *     cascade={"persist"},
     *      mappedBy="pessoa",
     *      orphanRemoval=true
     * )
     *
     * @Groups("entity")
     */
    private $enderecos;

    /**
     *
     * @var PessoaContato[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="PessoaContato",
     *     cascade={"persist"},
     *      mappedBy="pessoa",
     *      orphanRemoval=true
     * )
     *
     * @Groups("entity")
     */
    private $contatos;


    public function __construct()
    {
        $this->categorias = new ArrayCollection();
        $this->enderecos = new ArrayCollection();
        $this->contatos = new ArrayCollection();
    }

    /**
     * @return null|string
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * @param null|string $nome
     * @return Pessoa
     */
    public function setNome(?string $nome): Pessoa
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    /**
     * @param null|string $documento
     * @return Pessoa
     */
    public function setDocumento(?string $documento): Pessoa
    {
        $this->documento = $documento;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    /**
     * @param null|string $tipo
     * @return Pessoa
     */
    public function setTipo(?string $tipo): Pessoa
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getNomeFantasia(): ?string
    {
        return $this->nomeFantasia;
    }

    /**
     * @param null|string $nomeFantasia
     * @return Pessoa
     */
    public function setNomeFantasia(?string $nomeFantasia): Pessoa
    {
        $this->nomeFantasia = $nomeFantasia;
        return $this;
    }

    /**
     * @return string
     * @Groups("entity")
     */
    public function getNomeMontado(): string
    {
        return $this->nomeFantasia ? $this->nomeFantasia . ' (' . $this->nome . ')' : $this->nome;
    }

    /**
     * @return string
     * @Groups("entity")
     */
    public function getNomeMontadoComDocumento(): string
    {
        $r = $this->getDocumentoMascarado() . ' - ';
        if ($this->nomeFantasia) {
            $r .= $this->nomeFantasia . ' (' . $this->nome . ')';
        } else {
            $r .= $this->nome;
        }
        return $r;
    }

    /**
     * @return null|string
     * @Groups("entity")
     */
    public function getDocumentoMascarado(): ?string
    {
        return $this->documento ? StringUtils::mascararCnpjCpf($this->documento) : null;
    }


    /**
     * @return null|string
     */
    public function getInscricaoEstadual(): ?string
    {
        return $this->inscricaoEstadual;
    }

    /**
     * @param null|string $inscricaoEstadual
     * @return Pessoa
     */
    public function setInscricaoEstadual(?string $inscricaoEstadual): Pessoa
    {
        $this->inscricaoEstadual = $inscricaoEstadual;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getRg(): ?string
    {
        return $this->rg;
    }

    /**
     * @param null|string $rg
     * @return Pessoa
     */
    public function setRg(?string $rg): Pessoa
    {
        $this->rg = $rg;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getObs(): ?string
    {
        return $this->obs;
    }

    /**
     * @param null|string $obs
     * @return Pessoa
     */
    public function setObs(?string $obs): Pessoa
    {
        $this->obs = $obs;
        return $this;
    }

    /**
     * @return null|CategoriaPessoa[]|array|Collection
     */
    public function getCategorias(): ?Collection
    {
        return $this->categorias;
    }

    /**
     * @param CategoriaPessoa[]|array|Collection|null $categorias
     * @return Pessoa
     */
    public function setCategorias($categorias): Pessoa
    {
        $this->categorias = $categorias;
        return $this;
    }

    /**
     * @return PessoaEndereco[]|ArrayCollection
     */
    public function getEnderecos(): ?Collection
    {
        return $this->enderecos;
    }

    /**
     * @param PessoaEndereco[]|ArrayCollection $enderecos
     * @return Pessoa
     */
    public function setEnderecos($enderecos): Pessoa
    {
        $this->enderecos = $enderecos;
        return $this;
    }

    public function addEndereco(PessoaEndereco $endereco): Pessoa
    {
        $endereco->setPessoa($this);
        $this->enderecos->add($endereco);
        return $this;
    }

    public function removeEndereco(PessoaEndereco $endereco): Pessoa
    {
        if ($this->enderecos->contains($endereco)) {
            $this->enderecos->removeElement($endereco);
            $endereco->setPessoa(null);
        }
        return $this;
    }

    /**
     * @return PessoaContato[]|ArrayCollection
     */
    public function getContatos(): ?Collection
    {
        return $this->contatos;
    }

    /**
     * @param PessoaContato[]|ArrayCollection $contatos
     * @return Pessoa
     */
    public function setContatos($contatos): Pessoa
    {
        $this->contatos = $contatos;
        return $this;
    }

    public function addContato(PessoaContato $contato): Pessoa
    {
        $contato->setPessoa($this);
        $this->contatos->add($contato);
        return $this;
    }

    public function removeContato(PessoaContato $contato): Pessoa
    {
        if ($this->enderecos->contains($contato)) {
            $this->enderecos->removeElement($contato);
            $contato->setPessoa(null);
        }
        return $this;
    }


}
