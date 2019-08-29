<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\Base\Estado;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade Estado.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class EstadoRepository extends FilterRepository
{

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return Estado::class;
    }

    public function findByUf($nome, $uf)
    {
        $ql = "SELECT e FROM CrosierSource\CrosierLibBaseBundle\Entity\Base\Estado e WHERE e.uf = :uf";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'uf' => $uf
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de um Estado encontrado para [' . $uf . ']');
        }

        return count($results) == 1 ? $results[0] : null;
    }


}
