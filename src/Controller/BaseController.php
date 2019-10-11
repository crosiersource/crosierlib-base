<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\EntMenuRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class BaseController.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Controller
 * @author Carlos Eduardo Pauluk
 */
class BaseController extends AbstractController
{

    /** @var RegistryInterface */
    private $doctrine;

    /** @var SessionInterface */
    private $session;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(RegistryInterface $doctrine,
                                SessionInterface $session,
                                LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->session = $session;
        $this->logger = $logger;
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
            /** @var EntMenuRepository $repoEntMenu */
            $repoEntMenu = $this->doctrine->getRepository(EntMenu::class);
            // Caso não tenha sido passado o PROGRAM_UUID, utiliza o programa da Dashboard deste aplicativo
            if (!isset($parameters['PROGRAM_UUID']) || !$parameters['PROGRAM_UUID']) {
                // para o crosier-core, já retorna sem precisar pesquisar
                if ($_SERVER['CROSIERAPP_UUID'] === '175bd6d3-6c29-438a-9520-47fcee653cc5') {
                    $parameters['PROGRAM_UUID'] = '4f4df268-09ef-4e9c-bbc9-82eaf85de43f';
                } else {
                    $parameters['PROGRAM_UUID'] = $repoEntMenu->findAppMainProgramUUID($_SERVER['CROSIERAPP_UUID']); // '4f4df268-09ef-4e9c-bbc9-82eaf85de43f';
                }
            }
            $programUUID = $parameters['PROGRAM_UUID'];
            $menu = null;

            $entMenuId = null;

            if ($this->session->has('programs_menus')) {
                $programsMenus = $this->session->get('programs_menus');
                if (isset($programsMenus[$programUUID])) {
                    $entMenuId = $programsMenus[$programUUID];
                }
            }

            if (!$entMenuId) {
                $entMenu = $repoEntMenu->getEntMenuByProgramUUID($programUUID);
                if ($entMenu) {
                    $entMenuId = $entMenu['id'];
                    $programsMenus[$programUUID] = $entMenuId;
                    $this->session->set('programs_menus', $programsMenus);
                }
            }

            if ($entMenuId && $this->session->has('crosier_menus')) {
                $crosierMenus = $this->session->get('crosier_menus');
                if (isset($crosierMenus[$entMenuId])) {
                    $menu = $crosierMenus[$entMenuId];
                }
            }
            if (!$menu) {
                $menu = $repoEntMenu->buildMenuByProgram($programUUID);
                $crosierMenus[$entMenuId] = $menu;
                $this->session->set('crosier_menus', $crosierMenus);
            }
            $parameters = array_merge(['menu' => $menu], $parameters);

            return $this->render($view, $parameters, $response);
        } catch (\Exception $e) {
            $this->logger->error('doRender - error');
            $this->logger->error($e->getMessage());
            throw new \RuntimeException($e->getMessage(), 0, $e);
        }
    }

}