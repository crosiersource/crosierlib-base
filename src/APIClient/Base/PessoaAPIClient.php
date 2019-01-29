<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;

use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;
use GuzzleHttp\Client;

class PessoaAPIClient
{

    /**
     * @var CrosierAPIClient
     */
    private $crosierAPIClient;

    public function __construct(CrosierAPIClient $crosierAPIClient)
    {
        $this->crosierAPIClient = $crosierAPIClient;
    }

    public function getPessoaById(int $id)
    {
        $base_uri = getenv('CROSIERCORE_URL');
        $client = new Client(['base_uri' => $base_uri]);

        $uri = $base_uri . '/pessoa/findById/' . $id;
        $response = $client->post($uri, [
            'headers' => $this->crosierAPIClient->getAuthHeader()
        ]);

        $contents = $response->getBody()->getContents();
        $json = json_decode($contents);
        return $json;
    }

}