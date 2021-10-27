<?php


namespace CrosierSource\CrosierLibBaseBundle\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Trait CrosierCoreAuthenticatorTrait.
 *
 * Como a autenticação dos crosierapps é feita pelo rememberme, este Authenticator tem apenas a funcionalidade de
 * redirecionar para o login do crosier-core caso não esteja autenticado.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Security
 * @author Carlos Eduardo Pauluk
 */
trait CrosierCoreAuthenticatorTrait
{

    use TargetPathTrait;

    private LoggerInterface $logger;


    /**
     * Ver documentação do pai.
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $request->getSession()->set('uri_to_redirect_after_login', $request->getUri());
        $url = $_SERVER['CROSIERCORE_URL'] ?? null;
        if (!$url) {
            throw new \RuntimeException('CROSIERCORE_URL não informada');
        }
        return new RedirectResponse($url);
    }

    public function supportsRememberMe()
    {
        return true;
    }

    public function supports(Request $request): ?bool
    {
        return false;
    }

    public function getCredentials(Request $request)
    {
        return null;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return null;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return false;
    }

    public function authenticate(Request $request): PassportInterface
    {
        throw new AuthenticationException('authenticate - CrosierCoreAuthenticator do not authenticate');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw new AuthenticationException('onAuthenticationFailure - CrosierCoreAuthenticator do not authenticate');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
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
