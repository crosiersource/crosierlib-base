<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierEntityIdAPIClient;

/**
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient\Base
 * @author Carlos Eduardo Pauluk
 */
class AppConfigAPIClient extends CrosierEntityIdAPIClient
{

    public function getBaseURI(): string
    {
        return $_SERVER['CROSIERCORE_URL'] . '/api/cfg/appConfig';
    }


}