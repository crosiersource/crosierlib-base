<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\Estabelecimento;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade Estabelecimento.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class EstabelecimentoRepository extends FilterRepository
{
    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return Estabelecimento::class;
    }
}
