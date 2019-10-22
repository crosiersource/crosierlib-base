<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenuLocator;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * Class EntMenuLocatorEntityHandler
 * @package CrosierSource\CrosierLibBaseBundle\EntityHandler\Config
 * @author Carlos Eduardo Pauluk
 */
class EntMenuLocatorEntityHandler extends EntityHandler
{


    public function getEntityClass()
    {
        return EntMenuLocator::class;
    }
}