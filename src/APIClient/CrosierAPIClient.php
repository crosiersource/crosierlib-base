<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient;


use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Security\Core\Security;

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

    public function getAuthHeader()
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $authHeader['X-Authorization'] = 'Bearer ' . $user->getApiToken();
        return $authHeader;
    }


    public function doRequest($uri, $method, $params = null)
    {
        $base_uri = getenv('CROSIERCORE_URL');
        $client = new Client(['base_uri' => $base_uri]);

        $uri = $base_uri . $uri;

        $request = new Request(
            $method,
            $uri,
            $this->getAuthHeader(),
            $params
        );
        $response = $client->send($request, ['timeout' => 2]);


        return $response->getBody()->getContents();
    }

    public function get($uri, $params = null)
    {
        return $this->doRequest($uri, 'get', $params);
    }

    public function post($uri, $params = null)
    {
        return $this->doRequest($uri, 'post', $params);
    }

}