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

    /**
     * @return mixed
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function checkLoginState()
    {
        return json_decode($this->get('/sec/api/checkLoginState/'));
    }

}