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


    public static function getBaseUri(): string
    {
        return $_SERVER['CROSIERCORE_URL'] . '/api/cfg/entMenu';
    }

    public function buildMenu(string $programUUID)
    {
        $uri = '/buildMenu/' . $programUUID;
        $json = $this->post($uri);
        return json_decode($json, true);
    }

    public function getEntMenuByProgramUUID(string $programUUID)
    {
        $uri = '/getEntMenuByProgramUUID/' . $programUUID;
        $json = $this->post($uri);
        return json_decode($json, true);
    }

    public function getDashboardProgramUUID(string $appUUID)
    {
        $uri = '/getDashboardProgramUUID/' . $appUUID;
        $r = $this->post($uri);
        $json = json_decode($r, true);
        return $json['programUUID'];
    }
}