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
     */
    public function checkLoginState()
    {
        return json_decode($this->get('/checkLoginState/'));
    }

}