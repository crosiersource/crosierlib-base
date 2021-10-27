<?php


namespace CrosierSource\CrosierLibBaseBundle\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Repository\Security\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

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

    private UserRepository $userRepository;

    private LoggerInterface $logger;

    private Security $security;

    public function __construct(UserRepository $userRepository, LoggerInterface $logger, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
        $this->security = $security;
    }

    private function getXAuthorization(HeaderBag $headers): ?string
    {
        foreach ($headers->all() as $key => $value) {
            if (strtolower($key) === 'x-authorization') {
                return $value[0];
            }
        }
        return null;
    }

    public function supports(Request $request): ?bool
    {
        if (strpos($request->getPathInfo(), '/api') === 0 && $this->getXAuthorization($request->headers)) {
            return true;
        } // else
        return false;
    }

    public function getCredentials(Request $request)
    {
        $authorizationHeader = $this->getXAuthorization($request->headers);
        return $authorizationHeader ? substr($authorizationHeader, 7) : '';
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['apiToken' => $credentials]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Token inválido.');
        }

        if ($user->getApiTokenExpiresAt() <= new \DateTime()) {
            throw new CustomUserMessageAuthenticationException('Token expirado.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $this->logger->info('APIAuthenticator checkCredentials()');
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?\Symfony\Component\HttpFoundation\Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?\Symfony\Component\HttpFoundation\Response
    {
        $this->logger->info('APIAuthenticator onAuthenticationSuccess()');
        return new JsonResponse(['OK']);
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

    public function authenticate(Request $request): PassportInterface
    {
        $this->logger->info('authenticate');        
    }
}
