<?php

namespace CrosierSource\CrosierLibBaseBundle\EventSubscriber;

use CrosierSource\CrosierLibBaseBundle\APIClient\Security\SecurityAPIClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
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
     * @param RequestEvent $event
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function onKernelRequest(RequestEvent $event)
    {
//        // Não ativa para chamadas a APIs
//        if (strpos($event->getRequest()->getPathInfo(), '/api/') !== 0 &&
//            strpos($event->getRequest()->getPathInfo(), '/login') !== 0 &&
//            strpos($event->getRequest()->getPathInfo(), '/logout') !== 0) {
//
////            if (isset($_SERVER['CROSIERAPP_ID']) && (isset($_SERVER['CROSIERAPP_LOGINBYCORE']) && filter_var($_SERVER['CROSIERAPP_LOGINBYCORE'], FILTER_VALIDATE_BOOLEAN) === true)) {
//            $loginState = null;
//            try {
//                $loginState = $this->securityAPIClient->checkLoginState();
//                if ($loginState && isset($loginState['hasApiToken']) && $loginState['hasApiToken']) {
//                    return; // OK
//                }
//            } catch (\Throwable $e) {
//                $this->logger->error('onKernelRequest error (loginState)');
//                $this->logger->error(print_r($loginState, true));
//            }
//            // problema com o loginState... manda para o logout (que por sua vez mandará para o login)
//            $event->setResponse(
//                new RedirectResponse(
//                    $_SERVER['CROSIERCORE_URL'] . '/logout'
//                )
//            );
//
//        }
    }
}