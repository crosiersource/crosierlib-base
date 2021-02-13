<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;

/**
 * Class EntMenuEntityHandler
 * @package CrosierSource\CrosierLibBaseBundle\EntityHandler\Config
 * @author Carlos Eduardo Pauluk
 */
class EntMenuEntityHandler extends EntityHandler
{

    public function beforeSave(/** @var EntMenu $entMenu */ $entMenu)
    {
        if (!$entMenu->getOrdem()) {
            if ($entMenu->getPai()) {
                if ($entMenu->getPai()->getFilhos() and $entMenu->getPai()->getFilhos()->count() > 0) {
                    $ordem = $entMenu->getPai()->getFilhos()->get($entMenu->getPai()->getFilhos()->count() - 1)->getOrdem();
                    $entMenu->setOrdem($ordem);
                } else {
                    $entMenu->setOrdem($entMenu->getPai()->getOrdem());
                }
            } else {
                $entMenu->setOrdem(9999999);
            }
        }
        
        if ($entMenu->getPai()) {
            $entMenu->setAppUUID($entMenu->getPai()->getAppUUID());
            $entMenu->setPaiUUID($entMenu->getPai()->getUUID());
        }
        
        if (!$entMenu->getPaiUUID()) {
            $entMenu->setTipo('PAI');
        }
        if ($entMenu->getTipo() === 'PAI') {
            $entMenu->setPaiUUID(null);
        }
        if (!$entMenu->getUUID()) {
            $entMenu->setUUID(StringUtils::guidv4());
        }
        
    }

    public function getEntityClass()
    {
        return EntMenu::class;
    }
}