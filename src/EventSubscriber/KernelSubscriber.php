<?php

namespace CrosierSource\CrosierLibBaseBundle\EventSubscriber;

use CrosierSource\CrosierLibBaseBundle\APIClient\Security\SecurityAPIClient;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class KernelSubscriber.
 *
 * Realiza em todos os request de um CrosierApp a verificação de que o mesmo ainda permanece logado no CrosierCore.
 *
 *
 * @package CrosierSource\CrosierLibBaseBundle\EventSubscriber
 * @author Carlos Eduardo Pauluk
 */
class KernelSubscriber implements EventSubscriberInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SecurityAPIClient
     */
    private $securityAPIClient;

    public function __construct(LoggerInterface $logger, SecurityAPIClient $securityAPIClient)
    {
        $this->logger = $logger;
        $this->securityAPIClient = $securityAPIClient;
    }

    /**
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 0],
            ]
        ];
    }

    /**
     * Executa a verificação junto a api do CrosierCore se o app ainda está logado.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        // Só é ativado para apps
        if (isset($_SERVER['CROSIERAPP_ID']) && (isset($_SERVER['CROSIERAPP_LOGINBYCORE']) && filter_var($_SERVER['CROSIERAPP_LOGINBYCORE'], FILTER_VALIDATE_BOOLEAN) === true)) {
            try {
                $this->logger->info('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> App KernelSubscriber onRequest checkLoginState()');
                if (!$this->securityAPIClient->checkLoginState()) {
                    throw new \Exception('null');
                }
                $this->logger->info('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> OK');
            } catch (\Exception $e) {
                $this->logger->info('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ERRO');
                if ($e->getPrevious() instanceof \GuzzleHttp\Exception\ClientException) {
                    $exception = $e->getPrevious();
                    if ($exception->getCode() === 401) {
                        $event->setResponse(
                            new RedirectResponse(
                                $_SERVER['CROSIERCORE_URL'] . '/reauthApp/' . getenv('APP_ID')
                            )
                        );
                    }
                } else {
                    $event->setResponse(
                        new RedirectResponse(
                            $_SERVER['CROSIERCORE_URL']
                        )
                    );
                }
            }
        }


    }
}