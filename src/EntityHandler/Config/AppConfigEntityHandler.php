<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * Class AppConfigEntityHandler
 * @package App\EntityHandler\Config
 *
 * @author Carlos Eduardo Pauluk
 */
class AppConfigEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return AppConfig::class;
    }

    public function beforeSave(/** @var AppConfig $appConfig */ $appConfig)
    {
        if (strpos($appConfig->getChave(), 'json') !== FALSE) {
            $appConfig->isJson = true;
        }
    }


}