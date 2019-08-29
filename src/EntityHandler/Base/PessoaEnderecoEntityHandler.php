<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\Base\PessoaEndereco;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para PessoaEndereco.
 *
 * @author Carlos Eduardo Pauluk
 */
class PessoaEnderecoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return PessoaEndereco::class;
    }
}