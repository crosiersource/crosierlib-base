<?php


namespace CrosierSource\CrosierLibBaseBundle\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Repository\Security\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Trait APIAuthenticatorTrait.
 *
 * Para a autenticação em chamadas a api (url iniciando com '/api/').
 *
 * @package CrosierSource\CrosierLibBaseBundle\Security
 * @author Carlos Eduardo Pauluk
 */
trait APIAuthenticatorTrait
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Security
     */
    private $security;

    public function __construct(UserRepository $userRepository, LoggerInterface $logger, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
        $this->security = $security;
    }

    public function supports(Request $request)
    {
        if (strpos($request->getPathInfo(), '/api') === 0) {
            $this->logger->info('APIAuthenticator support!' . $request->getUri());
            if (!$request->headers->has('X-Authorization')) {
                $this->logger->error('APIAuthenticator sem header X-Authorization');
            } elseif (!$this->security->getUser()) {
                return true;
            }
        }
        return false;
    }

    public function getCredentials(Request $request)
    {
        // Lógica para poder liberar acesso em ambiente de dev.
        if ($request->getPathInfo() !== '/api/sec/checkLoginState/' && isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === 'dev') {
            $this->logger->info('APIAuthenticator com APP_ENV = dev');
            return 1;
        } else {
            $authorizationHeader = $request->headers->get('X-Authorization');
            return $authorizationHeader ? substr($authorizationHeader, 7) : '';
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // Lógica para poder liberar acesso em ambiente de dev.
        if ($credentials && isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === 'dev') {
            return $this->userRepository->find(1); // admin
        } else {
            /** @var User $user */
            $user = $this->userRepository->findOneBy([
                'apiToken' => $credentials
            ]);

            if (!$user) {
                throw new CustomUserMessageAuthenticationException(
                    'Token inválido.'
                );
            }

            if ($user->getApiTokenExpiresAt() <= new \DateTime()) {
                throw new CustomUserMessageAuthenticationException(
                    'Token expirado.'
                );
            }

            return $user;
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $this->logger->info('APIAuthenticator checkCredentials()');
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->logger->info('APIAuthenticator onAuthenticationSuccess()');
        // allow the request to continue
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        $this->logger->info('APIAuthenticator supportsRememberMe()');
        return false;
    }
}
