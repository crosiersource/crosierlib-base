<?php

namespace CrosierSource\CrosierLibBaseBundle\EventListener;

use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * @author Carlos Eduardo Pauluk
 */
class ViewExceptionListener
{

    public function onKernelException(ExceptionEvent $event)
    {
        $e = $event->getThrowable();
        if ($e instanceof ViewException) {
            $response = new Response();
            $response->setContent($e->getMessage());
            $response->setStatusCode(400);
            $event->setResponse($response);
        }
    }
}