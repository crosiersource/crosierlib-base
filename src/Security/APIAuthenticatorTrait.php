<?php


namespace CrosierSource\CrosierLibBaseBundle\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

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

    private EntityManagerInterface $em;

    private LoggerInterface $logger;

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

    public function authenticate(Request $request): PassportInterface
    {
        $authorizationHeader = $this->getXAuthorization($request->headers);
        $apiToken = $authorizationHeader ? substr($authorizationHeader, 7) : '';

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['apiToken' => $apiToken]);

        if ($user && ($user->getApiTokenExpiresAt() <= new \DateTime())) {
            throw new CustomUserMessageAuthenticationException('Token expirado.');
        }

        return new SelfValidatingPassport(new UserBadge($apiToken));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $this->logger->error('onAuthenticationFailure... ' . $exception->getMessage());
        $this->logger->error('(key:data) ... ' . $exception->getMessageKey() . ': ' . $exception->getMessageData());
        if ($exception instanceof TooManyLoginAttemptsAuthenticationException) {
            $errMsg = [
                'messageKey' => 'Login bloqueado (Causa: muitas tentativas de login)'
            ];
        } elseif ($exception instanceof BadCredentialsException) {
            $errMsg = [
                'messageKey' => 'Usuário ou senha inválidos'
            ];
        } else {
            $errMsg = [
                'messageKey' => 'Erro ao efetuar login'
            ];
        }

        return new JsonResponse($errMsg, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $this->logger->info('APIAuthenticator onAuthenticationSuccess()');
        return null;
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

    /**
     * @required
     * @param EntityManagerInterface $em
     */
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

}
