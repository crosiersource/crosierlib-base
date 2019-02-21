<?php


namespace CrosierSource\CrosierLibBaseBundle\APIClient\Config;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;
use GuzzleHttp\Client;

class MainMenuAPIClient extends CrosierAPIClient
{

    public function buildMainMenu(int $app_id)
    {
        $uri = '/api/cfg/mainMenu/build/' . $app_id;
        return $this->post($uri);
    }

    public function buildMenu(int $programId)
    {
        $uri = '/api/cfg/entMenu/buildMenu/' . $programId;
        return $this->post($uri);
    }
}