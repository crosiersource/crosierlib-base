<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierEntityIdAPIClient;


class PessoaAPIClient extends CrosierEntityIdAPIClient
{

    public static function getBaseUri(): string
    {
        return $_SERVER['CROSIERCORE_URL'] . '/api/bse/pessoa';
    }


}