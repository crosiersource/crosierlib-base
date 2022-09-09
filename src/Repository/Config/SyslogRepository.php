<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Logs\Syslog;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * @author Carlos Eduardo Pauluk
 */
class SyslogRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return Syslog::class;
    }
}
