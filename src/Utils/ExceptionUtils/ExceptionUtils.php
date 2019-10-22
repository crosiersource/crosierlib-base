<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils;

use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use GuzzleHttp\Exception\ClientException;

/**
 * Class ExceptionUtils.
 *
 * Tratamento de mensagem de exceçção do Doctrine.
 *
 * @author Carlos Eduardo Pauluk
 */
class ExceptionUtils
{

    /**
     * Trata retornos de erro do MySQL.
     *
     * @param \Exception $e
     * @return string
     */
    public static function treatException(\Exception $e): string
    {
        if ($e instanceof \Doctrine\DBAL\Exception\DriverException) {
            return self::treatDriverException($e);
        }
        if ($e->getPrevious() instanceof \Doctrine\DBAL\Exception\DriverException) {
            return self::treatDriverException($e->getPrevious());
        }
        if ($e instanceof ViewException) {
            return $e->getMessage();
        }
        if ($e->getPrevious() instanceof ViewException) {
            return $e->getPrevious()->getMessage();
        }
        if ($e->getPrevious() instanceof ClientException) {
            return $e->getPrevious()->getMessage();
        }
        return '';
    }

    /**
     * @param \Doctrine\DBAL\Exception\DriverException $e
     * @return mixed
     */
    public static function treatDriverException(\Doctrine\DBAL\Exception\DriverException $e)
    {
        $message = $e->getMessage();
        $code = $e->getErrorCode();

        $regex = '/(?:.*)(?:SQLSTATE)(?:.*)(?:' . $code . ')(?<msg>.*)/';
        preg_match($regex, $message, $matches);
        if (isset($matches['msg'])) {
            if (strpos($matches['msg'], 'foreign key constraint fails') !== FALSE) {
                $matches['msg'] = 'Registro referenciado por subregistros. Impossível deletar.';
            }
            return $matches['msg'];
        }
        return '';
    }

}