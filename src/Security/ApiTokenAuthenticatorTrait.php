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

trait ApiTokenAuthenticatorTrait
{
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
        // look for header "Authorization: Bearer <token>"
        $this->logger->info('ApiTokenAuthenticator supports');
        return $request->query->has('apiTokenAuthorization');
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

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('index'));
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $this->logger->info('Autenticar pelo CrosierCore...');
        $url = getenv('CROSIERCORE_URL');
        $this->logger->info("Redirecionando para '" . $url . "'");
        return new RedirectResponse($url);
    }

    public function supportsRememberMe()
    {
        return true;
    }
}