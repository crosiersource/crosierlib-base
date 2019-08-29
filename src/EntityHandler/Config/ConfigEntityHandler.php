<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\Config;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

class ConfigEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return Config::class;
    }
}