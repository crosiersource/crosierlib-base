<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\StoredViewInfo;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * Class StoredViewInfoEntityHandler
 *
 * @package CrosierSource\CrosierLibBaseBundle\EntityHandler\Config
 * @author Carlos Eduardo Pauluk
 */
class StoredViewInfoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return StoredViewInfo::class;
    }


}