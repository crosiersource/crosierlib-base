<?php


namespace CrosierSource\CrosierLibBaseBundle\APIClient\Config;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;
use GuzzleHttp\Client;

class MainMenuAPIClient
{

    /**
     * @var CrosierAPIClient
     */
    private $crosierAPIClient;

    public function __construct(CrosierAPIClient $crosierAPIClient)
    {
        $this->crosierAPIClient = $crosierAPIClient;
    }

    public function buildMainMenu(int $app_id)
    {
        $base_uri = getenv('CROSIERCORE_URL');
        $client = new Client(['base_uri' => $base_uri]);

        $uri = $base_uri . '/cfg/mainMenu/build/' . $app_id;
        $response = $client->post($uri, [
            'headers' => $this->crosierAPIClient->getAuthHeader()
        ]);

        return $response->getBody()->getContents();
    }
}