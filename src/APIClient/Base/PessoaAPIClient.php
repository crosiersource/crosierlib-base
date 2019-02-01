<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient\Base;


use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;

class PessoaAPIClient extends CrosierAPIClient
{

    /**
     * @param int $id
     * @return string
     */
    public function getPessoaById(int $id)
    {
        return $this->post('/pessoa/findById/' . $id);
    }

}