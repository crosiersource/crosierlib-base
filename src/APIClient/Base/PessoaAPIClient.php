<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierEntityIdAPIClient;

/**
 * Cliente para consumir os serviços REST da PessoaAPI.
 *
 * @package CrosierSource\CrosierLibBaseBundle\APIClient\Base
 * @author Carlos Eduardo Pauluk
 */
class PessoaAPIClient extends CrosierEntityIdAPIClient
{

    public static function getBaseUri(): string
    {
        return '/api/bse/pessoa';
    }

    public function findByCategEStr(string $categ, string $str = null)
    {
        $r = $this->get(PessoaAPIClient::getBaseUri() . '/findByCategEStr/' . $categ . '/' . $str);
        return json_decode($r, true);
    }


}