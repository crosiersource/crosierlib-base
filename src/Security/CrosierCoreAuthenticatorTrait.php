<?php


namespace CrosierSource\CrosierLibBaseBundle\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Repository\Security\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
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
 * Para a autenticação de CrosierApps após acesso via CrosierCore.
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

    public function supports(Request $request)
    {
        return $request->query->has('apiTokenAuthorization') || strpos($request->getPathInfo(), '/relVendas01/listItensVendidosPorFornecedor/') !== false;
    }

    public function getCredentials(Request $request)
    {
        return $request->query->get('apiTokenAuthorization');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
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

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw new CustomUserMessageAuthenticationException(
            'Erro na autenticação'
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // Remove da sessão os menus cacheados pelo BaseController
        $session = new Session();
        $session->set('programs_menus', null);
        $session->set('crosier_menus', null);

        $whereTo = "/";
        if (isset($_SERVER['PATH_INFO'])) {
            $whereTo = $_SERVER['PATH_INFO']; // em alguns servers, não está definida
        } else {
            $whereTo = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
        }

        return new RedirectResponse($whereTo);
    }

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
}