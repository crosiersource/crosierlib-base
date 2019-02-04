<?php


namespace CrosierSource\CrosierLibBaseBundle\APIClient\Config;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;
use GuzzleHttp\Client;

class MainMenuAPIClient extends CrosierAPIClient
{

    public function buildMainMenu(int $app_id)
    {
        $uri = '/cfg/mainMenu/build/' . $app_id;
        return $this->post($uri);
    }
}