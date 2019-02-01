<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient;


use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Security\Core\Security;
use GuzzleHttp\Client;

class CrosierAPIClient
{

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getAuthHeader() {
        /** @var User $user */
        $user = $this->security->getUser();
        $authHeader['X-Authorization'] = 'Bearer ' . $user->getApiToken();
        return $authHeader;
    }

    public function get($uri, $params = null) {
        $base_uri = getenv('CROSIERCORE_URL');
        $client = new Client(['base_uri' => $base_uri]);

        $uri = $base_uri . $uri;


        $request = new Request('get', $uri,
            [
                'headers' => $this->getAuthHeader()
            ],$params);
        $response = $client->send($request, ['timeout' => 2]);


        return $response->getBody()->getContents();
    }

}