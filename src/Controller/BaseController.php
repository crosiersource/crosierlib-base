<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;

use App\Entity\Config\EntMenu;
use App\Entity\Config\Program;
use CrosierSource\CrosierLibBaseBundle\APIClient\Config\EntMenuAPIClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class BaseController.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Controller
 * @author Carlos Eduardo Pauluk
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
        // Caso não tenha sido passado o PROGRAM_UUID, utiliza o programa da Dashboard deste aplicativo
        if (!isset($parameters['PROGRAM_UUID']) || !$parameters['PROGRAM_UUID']) {
            // para o crosier-core, já retorna sem precisar pesquisar
            if ($_SERVER['CROSIERAPP_UUID'] === '175bd6d3-6c29-438a-9520-47fcee653cc5') {
                $parameters['PROGRAM_UUID'] = '4f4df268-09ef-4e9c-bbc9-82eaf85de43f';
            } else {
                $parameters['PROGRAM_UUID'] = $this->entMenuAPIClient->getAppMainProgramUUID($_SERVER['CROSIERAPP_UUID']); // '4f4df268-09ef-4e9c-bbc9-82eaf85de43f';
            }
        }
        $programUUID = $parameters['PROGRAM_UUID'];
        $menu = null;

        $entMenuId = null;

        if ($session->has('programs_menus')) {
            $programsMenus = $session->get('programs_menus');
            if (isset($programsMenus[$programUUID])) {
                $entMenuId = $programsMenus[$programUUID];
            }
        }

        if (!$entMenuId) {
            $entMenu = $this->entMenuAPIClient->getEntMenuByProgramUUID($programUUID);
            if ($entMenu) {
                $entMenuId = $entMenu['id'];
                $programsMenus[$programUUID] = $entMenuId;
                $session->set('programs_menus', $programsMenus);
            }
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