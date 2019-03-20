<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;

class PessoaAPIClient extends CrosierEntityIdAPIClient
{

    public function getBaseUri(): string {
        return '/api/pessoa';
    }



}