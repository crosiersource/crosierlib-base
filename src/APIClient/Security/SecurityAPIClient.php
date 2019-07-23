<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Security;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;

/**
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient\Base
 * @author Carlos Eduardo Pauluk
 */
class SecurityAPIClient extends CrosierAPIClient
{

    public function getBaseURI(): string
    {
        return $_SERVER['CROSIERCORE_URL'] . '/api/sec';
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkLoginState()
    {
        return json_decode($this->get('/checkLoginState/'), true);
    }

}