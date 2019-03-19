<?php


namespace CrosierSource\CrosierLibBaseBundle\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Repository\Security\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

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

    use TargetPathTrait;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(UserRepository $userRepository, RouterInterface $router, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function supports(Request $request)
    {
        $this->logger->info('APIAuthenticator supports()');
        return strpos($request->getPathInfo(), '/api/') === 0;
    }

    public function getCredentials(Request $request)
    {
        $this->logger->info('APIAuthenticator getCredentials()');
        // Lógica para poder liberar acesso em ambiente de dev.
        if (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === 'dev') {
            return 1;
        } else {
            $authorizationHeader = $request->headers->get('X-Authorization');
            // skip beyond "Bearer "
            return $authorizationHeader ? substr($authorizationHeader, 7) : '';
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // Lógica para poder liberar acesso em ambiente de dev.
        if (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === 'dev') {
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
        $this->logger->info('APIAuthenticator onAuthenticationFailure()');
        return new JsonResponse([
            'message' => $exception->getMessageKey()
        ], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->logger->info('APIAuthenticator onAuthenticationSuccess()');
        // allow the request to continue
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $this->logger->info('APIAuthenticator start()');
        throw new \Exception('Not used: entry_point from other authentication is used');
    }

    public function supportsRememberMe()
    {
        $this->logger->info('APIAuthenticator supportsRememberMe()');
        return false;
    }
}