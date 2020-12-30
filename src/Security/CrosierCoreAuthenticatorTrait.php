<?php


namespace CrosierSource\CrosierLibBaseBundle\Security;

use CrosierSource\CrosierLibBaseBundle\Repository\Security\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
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

    /**
     * CrosierCoreAuthenticatorTrait constructor.
     * @param UserRepository $userRepository
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     */
    public function __construct(UserRepository $userRepository, RouterInterface $router, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * Ver documentação do pai.
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
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

    public function supports(Request $request)
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

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw new CustomUserMessageAuthenticationException('Erro na autenticação');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }


}
