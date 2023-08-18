<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;

use CrosierSource\CrosierLibBaseBundle\Business\Config\StoredViewInfoBusiness;
use CrosierSource\CrosierLibBaseBundle\Business\Config\SyslogBusiness;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\Estabelecimento;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\EntMenuLocatorRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\ExceptionUtils\ExceptionUtils;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class BaseController.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Controller
 * @author Carlos Eduardo Pauluk
 */
class BaseController extends AbstractController
{

    public EntityManagerInterface $doctrine;

    protected StoredViewInfoBusiness $storedViewInfoBusiness;

    private SessionInterface $session;

    private LoggerInterface $logger;

    private RequestStack $requestStack;

    private EntMenuLocatorRepository $entMenuLocatorRepository;

    private Security $security;

    protected SyslogBusiness $syslog;

    /**
     * BaseController constructor.
     * @param EntityManagerInterface $doctrine
     * @param StoredViewInfoBusiness $storedViewInfoBusiness
     * @param SessionInterface $session
     * @param LoggerInterface $logger
     * @param RequestStack $requestStack
     * @param EntMenuLocatorRepository $entMenuLocatorRepository
     * @param Security $security
     * @param SyslogBusiness $syslog
     */
    public function __construct(EntityManagerInterface $doctrine,
                                StoredViewInfoBusiness $storedViewInfoBusiness,
                                SessionInterface $session,
                                LoggerInterface $logger,
                                RequestStack $requestStack,
                                EntMenuLocatorRepository $entMenuLocatorRepository,
                                Security $security,
                                SyslogBusiness $syslog)
    {
        $this->doctrine = $doctrine;
        $this->storedViewInfoBusiness = $storedViewInfoBusiness;
        $this->session = $session;
        $this->logger = $logger;
        $this->requestStack = $requestStack;
        $this->entMenuLocatorRepository = $entMenuLocatorRepository;
        $this->security = $security;
        $this->syslog = $syslog;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Chama o parent::render passando informações sobre a criação do menu.
     *
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    protected function doRender(string $view, array $parameters = [], Response $response = null): Response
    {
        try {
            $uri = $this->requestStack->getMasterRequest()->getUri();
            /** @var User $user */
            $user = $this->security->getUser();
            $this->entMenuLocatorRepository->security = $this->security;
            $menu = null;
            try {
                $menu = $this->entMenuLocatorRepository->getMenuByUrl($uri, $user);
            } catch (\Exception $e) {
                $this->logger->error('Erro ao construir o menu');
                $this->logger->error(ExceptionUtils::treatException($e));
                $this->addFlash('error', 'Erro ao construir o menu');
            }
            $parameters = array_merge(['menu' => $menu], $parameters);

            $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
            $estabelecimento = $cache->get('estabelecimento_' . $user->getEstabelecimentoId(), function (ItemInterface $item) use ($user) {
               $repoEstabelecimento = $this->getDoctrine()->getRepository(Estabelecimento::class);
               $estabelecimento = $repoEstabelecimento->find($user->getEstabelecimentoId());
               return $estabelecimento->descricao;
            });
            $parameters['estabelecimento'] = $estabelecimento;

            $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.headerOptions', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
            $headerOptions = $cache->get('estabelecimento_' . $user->getEstabelecimentoId(), function (ItemInterface $item) use ($user) {
                /** @var AppConfigRepository $repoAppConfig */
                $repoAppConfig = $this->getDoctrine()->getRepository(AppConfig::class);
                $rs = $repoAppConfig->findConfigByChaveAndAppNome('headerOptions.json', 'crosier-core');
                return $rs ? json_decode($rs->valor, true) : [];
            });
            $parameters['headerOptions'] = $headerOptions;
            
            return $this->render($view, $parameters, $response);
        } catch (\Exception $e) {
            $this->logger->error('doRender - error');
            $this->logger->error($e->getMessage());
            throw new \RuntimeException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @return SyslogBusiness
     */
    public function getSyslog(): SyslogBusiness
    {
        return $this->syslog;
    }


}