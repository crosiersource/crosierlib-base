<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Config;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierEntityIdAPIClient;

/**
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient\Config
 * @author Carlos Eduardo Pauluk
 */
class PushMessageAPIClient extends CrosierEntityIdAPIClient
{

    public function getBaseURI(): string
    {
        return $_SERVER['CROSIERCORE_URL'] . '/api/cfg/pushMessage';
    }


}