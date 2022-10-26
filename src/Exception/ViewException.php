<?php

namespace CrosierSource\CrosierLibBaseBundle\Exception;

use CrosierSource\CrosierLibBaseBundle\Business\Config\SyslogBusiness;
use Throwable;

/**
 * Class ViewException.
 * Encapsulamento de exceções que podem ser enviadas a view para o usuário.
 *
 * @author Carlos Eduardo Pauluk
 */
class ViewException extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null, ?SyslogBusiness $syslog = null)
    {
        parent::__construct($message, $code, $previous);
        if ($syslog) {
            $syslog->err($message, $previous ? $previous->getMessage(): '');
        }
    }


}