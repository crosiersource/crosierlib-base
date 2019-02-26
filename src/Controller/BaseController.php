<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;

use App\Entity\Config\EntMenu;
use App\Entity\Config\Program;
use CrosierSource\CrosierLibBaseBundle\APIClient\Config\EntMenuAPIClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class BaseController
 * @package CrosierSource\CrosierLibBaseBundle\Controller
 */
class BaseController extends AbstractController
{

    /** @var EntMenuAPIClient */
    private $entMenuAPIClient;

    public function __construct(EntMenuAPIClient $entMenuAPIClient)
    {
        $this->entMenuAPIClient = $entMenuAPIClient;
    }

    /**
     * Chama o parent::render passando informações sobre a criação do menu.
     *
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     * @throws \Exception
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $session = new Session();
        if (!isset($parameters['PROGRAM_UUID'])) {
            // Por padrão, exibe o menu principal do Crosier.
            $parameters['PROGRAM_UUID'] = '72a9aa1dc9024905b60ea8009a9bf50e';
        }
        $programUUID = $parameters['PROGRAM_UUID'];
        $menu = null;

        $entMenuId = null;

        if ($session->has('programs_menus')) {
            $programsMenus = $session->get('programs_menus');
            if (isset($programsMenus[$programUUID])) {
                $entMenuId = $programsMenus[$programUUID];
            }
        } else {
            $entMenu = $this->entMenuAPIClient->getEntMenuByProgramUUID($programUUID);
            $entMenuId = $entMenu['id'];
            $programsMenus[$programUUID] = $entMenuId;
            $session->set('programs_menus', $programsMenus);
        }

        if ($entMenuId && $session->has('crosier_menus')) {
            $crosierMenus = $session->get('crosier_menus');
            if (isset($crosierMenus[$entMenuId])) {
                $menu = $crosierMenus[$entMenuId];
            }
        }
        if (!$menu) {
            $menu = $this->entMenuAPIClient->buildMenu($programUUID);
            $crosierMenus[$entMenuId] = $menu;
            $session->set('crosier_menus', $crosierMenus);
        }
        $parameters = array_merge(['menu' => $menu], $parameters);

        return parent::render($view, $parameters, $response);
    }

}