<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\Base\Municipio;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * @author Carlos Eduardo Pauluk
 */
class MunicipioEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Municipio::class;
    }
    
}