<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\Base\Municipio;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade Municipio.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class MunicipioRepository extends FilterRepository
{

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return Municipio::class;
    }

    public function findByNomeAndUf($nome, $uf)
    {
        $ql = "SELECT m FROM CrosierSource\CrosierLibBaseBundle\Entity\Base\Municipio m WHERE m.municipioNome = :nome AND m.ufSigla = :uf";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'nome' => $nome,
            'uf' => $uf
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de um Município encontrado para [' . $nome . "] [" . $uf . ']');
        }

        return count($results) == 1 ? $results[0] : null;
    }

}
