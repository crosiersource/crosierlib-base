<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\Base\Estado;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * @author Carlos Eduardo Pauluk
 */
class EstadoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Estado::class;
    }

}