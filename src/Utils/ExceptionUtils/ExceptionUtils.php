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
    public static function treatException(\Throwable $e, ?string $preMsg = null): string
    {
        if ($e instanceof \Doctrine\DBAL\Exception\DriverException) {
            $msgT = self::treatDriverException($e);
        } elseif ($e->getPrevious() instanceof \Doctrine\DBAL\Exception\DriverException) {
            $msgT = self::treatDriverException($e->getPrevious());
        } elseif ($e instanceof ViewException) {
            $msgT = $e->getMessage();
        } elseif ($e->getPrevious() instanceof ViewException) {
            $msgT = $e->getPrevious()->getMessage();
        } elseif ($e->getPrevious() instanceof ClientException) {
            $msgT = $e->getPrevious()->getMessage();
        } elseif ($e instanceof \ReflectionException) {
            $msgT = $e->getMessage();
        } else {
            $msgT = '';
        }
        if ($preMsg) {
            $msg = $preMsg;
            $msg .= $msgT ? (' (' . $msgT . ')') : '';
        } else {
            $msg = $msgT;
        }
        return $msg;
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
