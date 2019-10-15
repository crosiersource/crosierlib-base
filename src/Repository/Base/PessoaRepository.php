<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\Base\CategoriaPessoa;
use CrosierSource\CrosierLibBaseBundle\Entity\Base\Pessoa;
use CrosierSource\CrosierLibBaseBundle\Entity\Base\PessoaContato;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Repository para a entidade Pessoa.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class PessoaRepository extends FilterRepository
{

    public function handleFrombyFilters(QueryBuilder $qb)
    {
        return $qb->from($this->getEntityClass(), 'e')
            ->leftJoin(CategoriaPessoa::class, 'categ', 'WITH', 'categ MEMBER OF e.categorias');
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return Pessoa::class;
    }

    /**
     * @param string $str
     * @param int $maxResults
     * @return mixed
     */
    public function findPessoaByStr(string $str, int $maxResults = 30)
    {
        $dql = 'SELECT p FROM CrosierSource\CrosierLibBaseBundle\Entity\Base\Pessoa p WHERE p.documento LIKE :str OR p.nome LIKE :str OR p.nomeFantasia LIKE :str ORDER BY p.nome';
        $qry = $this->getEntityManager()->createQuery($dql);
        $qry->setParameter('str', '%' . $str . '%');
        $qry->setMaxResults($maxResults);
        return $qry->getResult();
    }

    /**
     * @param string $str
     * @param string $categ
     * @param int $maxResults
     * @return mixed
     */
    public function findPessoaByStrECateg(string $str, string $categ, int $maxResults = 30)
    {
        $dql = 'SELECT p FROM 
            CrosierSource\CrosierLibBaseBundle\Entity\Base\Pessoa p JOIN 
             CrosierSource\CrosierLibBaseBundle\Entity\Base\CategoriaPessoa categ WITH categ MEMBER OF p.categorias
             WHERE
             categ.descricao LIKE :categ AND  
             (p.documento LIKE :str OR p.nome LIKE :str OR p.nomeFantasia LIKE :str) ORDER BY p.nome';
        $qry = $this->getEntityManager()->createQuery($dql);
        $qry->setParameter('str', '%' . $str . '%');
        $qry->setParameter('categ', $categ);
        $qry->setMaxResults($maxResults);
        return $qry->getResult();
    }

    /**
     * Na bse_pessoa o documento não é UNIQUE. Este método retorna o registro com mais dados dentre os encontrados.
     * @param string $documento
     * @return mixed
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function findPessoaMaisCompletaPorDocumento(string $documento)
    {
        $documento = preg_replace("/[^0-9]/", '', $documento);
        $pessoas = $this->findBy(['documento' => $documento]);

        $lMaisCompleto = 0;
        $pessoaMaisCompleta = null;

        /** @var Pessoa $pessoa */
        foreach ($pessoas as $pessoa) {
            $l = strlen($pessoa->getNome() . $pessoa->getInscricaoEstadual());
            if ($lMaisCompleto <= $l) {
                $pessoaMaisCompleta = $pessoa;
                $lMaisCompleto = $l;
            }
        }

        $enderecoMaisCompleto = null;
        $lMaisCompleto = 0;
        foreach ($pessoas as $pessoa) {
            foreach ($pessoa->getEnderecos() as $endereco) {
                $l = strlen($endereco->getLogradouro() . $endereco->getNumero() . $endereco->getComplemento() . $endereco->getCep() . $endereco->getBairro() . $endereco->getCidade() . $endereco->getEstado());
                if ($lMaisCompleto <= $l) {
                    $enderecoMaisCompleto = $endereco;
                    $lMaisCompleto = $l;
                }
            }
        }

        $p['id'] = $pessoaMaisCompleta->getId();
        $p['documento'] = $pessoaMaisCompleta->getDocumento();
        $p['nome'] = $pessoaMaisCompleta->getNome();
        $p['nomeFantasia'] = $pessoaMaisCompleta->getNomeFantasia();
        $p['ie'] = $pessoaMaisCompleta->getInscricaoEstadual();
        if ($enderecoMaisCompleto) {
            $p['logradouro'] = $enderecoMaisCompleto->getLogradouro();
            $p['numero'] = $enderecoMaisCompleto->getNumero();
            $p['complemento'] = $enderecoMaisCompleto->getComplemento();
            $p['bairro'] = $enderecoMaisCompleto->getBairro();
            $p['cidade'] = $enderecoMaisCompleto->getCidade();
            $p['estado'] = $enderecoMaisCompleto->getEstado();
            $p['cep'] = $enderecoMaisCompleto->getCep();
        } else {
            $p['logradouro'] = '';
            $p['numero'] = '';
            $p['complemento'] = '';
            $p['bairro'] = '';
            $p['cidade'] = '';
            $p['estado'] = '';
            $p['cep'] = '';
        }

        /** @var PessoaContatoRepository $repoContatos */
        $repoContatos = $this->getEntityManager()->getRepository(PessoaContato::class);
        $fone1 = '';
        $email1 = '';
        foreach ($pessoas as $pessoa) {
            $fones = $repoContatos->findOneByFiltersSimpl([['pessoa', 'EQ', $pessoa], ['tipo', 'LIKE', '%FONE%'], ['valor', 'IS_NOT_EMPTY']]);
            $fone1 = $fones ? $fones->getValor() : null;
            $emails = $repoContatos->findByFiltersSimpl([['pessoa', 'EQ', $pessoa], ['tipo', 'LIKE', '%MAIL%'], ['valor', 'IS_NOT_EMPTY']]);
            $email1 = $emails ? $emails->getValor() : null;
        }
        $p['fone'] = $fone1;
        $p['email'] = $email1;

        return $p;
    }


}
