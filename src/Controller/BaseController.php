<?php

namespace CrosierSource\CrosierLibBaseBundle\Controller;

use CrosierSource\CrosierLibBaseBundle\APIClient\Config\EntMenuAPIClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

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
        if (!isset($parameters['PROGRAM_UUID'])) {
            // throw new \CrosierSource\CrosierLibBaseBundle\Exception\ViewException('Menu indefinido.');
        } else {
            $programUUID = $parameters['PROGRAM_UUID'];
            $menu = $this->entMenuAPIClient->buildMenu($programUUID);
            $parameters = array_merge(['menu'=>$menu], $parameters);
        }
        return parent::render($view, $parameters, $response);
    }

}