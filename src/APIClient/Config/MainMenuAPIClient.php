<?php


namespace CrosierSource\CrosierLibBaseBundle\APIClient\Config;


use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Security;

class MainMenuAPIClient
{

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildMainMenu(int $app_id)
    {
        $base_uri = getenv('CROSIERCORE_URL');
        $client = new Client(['base_uri' => $base_uri]);

        $uri = $base_uri . '/cfg/mainMenu/build/' . $app_id;
        $response = $client->post($uri, [
            'headers' => [
                'X-Authorization' => 'Bearer ' . $this->security->getUser()->getApiToken()
            ]
        ]);

        return $response->getBody();
    }
}