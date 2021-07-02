<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Config;

use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * @author Carlos Eduardo Pauluk
 */
class EntityChangeEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return EntityChange::class;
    }


}