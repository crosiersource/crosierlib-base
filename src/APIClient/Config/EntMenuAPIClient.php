<?php


namespace CrosierSource\CrosierLibBaseBundle\APIClient\Config;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;

/**
 * Cliente para consumir os serviços REST da EntMenuAPI.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient\Config
 * @author Carlos Eduardo Pauluk
 */
class EntMenuAPIClient extends CrosierAPIClient
{

    public function buildMenu(string $programUUID)
    {
        $uri = '/api/cfg/entMenu/buildMenu/' . $programUUID;
        $json = $this->post($uri);
        return json_decode($json, true);
    }

    public function getEntMenuByProgramUUID(string $programUUID)
    {
        $uri = '/api/cfg/entMenu/getEntMenuByProgramUUID/' . $programUUID;
        $json = $this->post($uri);
        return json_decode($json, true);
    }
}