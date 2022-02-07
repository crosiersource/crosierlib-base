<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\Estabelecimento;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * @author Carlos Eduardo Pauluk
 */
class EstabelecimentoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Estabelecimento::class;
    }
}