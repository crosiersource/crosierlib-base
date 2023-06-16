<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

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
        if (strpos($appConfig->chave, 'json') !== FALSE) {
            $appConfig->isJson = true;
        }
    }

    public function afterSave($entityId)
    {
        $cache = new FilesystemAdapter('cfg_app_config', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
        $cache->clear();
    }


}