<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils;

use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\EntityManagerClosed;
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
        if (strpos($e->getMessage(), 'Erro no PreUpdateListener') !== FALSE) {
            $msgT = $e->getMessage();
        } elseif ($e instanceof UniqueConstraintViolationException) {
            $msg = $e->getMessage();
            $pos = strpos($msg, '1062 Duplicate entry ') + 21;
            $pos2 = strpos($msg, ' for key ');
            $valorDuplicado = substr($msg, $pos, $pos2 - $pos);
            $msgT = 'O valor ' . $valorDuplicado . ' já existe na base de dados';
        } elseif ($e instanceof \Doctrine\DBAL\Exception\DriverException) {
            $msgT = self::treatDriverException($e);
        } elseif ($e->getPrevious() instanceof \Doctrine\DBAL\Exception\DriverException) {
            $msgT = self::treatDriverException($e->getPrevious());
        } elseif ($e instanceof ViewException) {
            $msgT = $e->getMessage();
            if ($e->getPrevious() instanceof ViewException) {
                $msgT .= ' (' . $e->getPrevious()->getMessage() . ')';
            }
        } elseif ($e->getPrevious() instanceof ViewException) {
            $msgT = $e->getPrevious()->getMessage();
        } elseif ($e instanceof ClientException) {
            try {
                $msgT = $e->getResponse()->getBody()->getContents();
            } catch (\Exception $e) {
                $msgT = $e->getPrevious()->getMessage();
            }
        } elseif ($e->getPrevious() instanceof ClientException) {
            $msgT = $e->getPrevious()->getMessage();
        } elseif ($e instanceof EntityManagerClosed) {
            $msgT = 'The EntityManager is closed.';
        } elseif ($e instanceof \ReflectionException) {
            $msgT = $e->getMessage();
        } else {
            $msgT = '';
        }

        if ($e->getPrevious() instanceof EntityManagerClosed) {
            $msgT .= ' [The EntityManager is closed.]';
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
        $code = $e->getCode() ?? null;

        $regex = '/(?:.*)(?:SQLSTATE)(?:.*)(?:' . $code . ')(?<msg>.*)/';
        preg_match($regex, $message, $matches);
        if (isset($matches['msg'])) {
            if (strpos($matches['msg'], 'foreign key constraint fails') !== FALSE) {
                $matches['msg'] = 'Registro referenciado por subregistros.';
            }
            return $matches['msg'];
        }
        return '';
    }

}
