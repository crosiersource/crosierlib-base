<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Logs\Syslog;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * @author Carlos Eduardo Pauluk
 */
class SyslogEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Syslog::class;
    }


}