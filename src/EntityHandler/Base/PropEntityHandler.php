<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\Base\Prop;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * Class PropEntityHandler
 *
 * @author Carlos Eduardo Pauluk
 */
class PropEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Prop::class;
    }
}