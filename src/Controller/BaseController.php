<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;

use CrosierSource\CrosierLibBaseBundle\Business\Config\StoredViewInfoBusiness;
use CrosierSource\CrosierLibBaseBundle\Business\Config\SyslogBusiness;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\EntMenuLocatorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class BaseController.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Controller
 * @author Carlos Eduardo Pauluk
 */
class BaseController extends AbstractController
{

    /** @var EntityManagerInterface */
    private $doctrine;

    /** @var StoredViewInfoBusiness */
    protected $storedViewInfoBusiness;

    /** @var SessionInterface */
    private $session;

    /** @var LoggerInterface */
    private $logger;

    /** @var RequestStack */
    private $requestStack;

    /** @var EntMenuLocatorRepository */
    private $entMenuLocatorRepository;

    /** @var Security */
    private $security;

    protected SyslogBusiness $syslog;

    /**
     * BaseController constructor.
     * @param EntityManagerInterface $doctrine
     * @param SessionInterface $session
     * @param LoggerInterface $logger
     * @param RequestStack $requestStack
     * @param EntMenuLocatorRepository $entMenuLocatorRepository
     * @param Security $security
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
            $this->entMenuLocatorRepository->security = $this->security;
            $menu = null;
            try {
                $menu = $this->entMenuLocatorRepository->getMenuByUrl($uri, $this->security->getUser());
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erro ao construir o menu');
            }
            $parameters = array_merge(['menu' => $menu], $parameters);
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