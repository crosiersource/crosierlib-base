<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\Base\PessoaContato;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para PessoaTelefone.
 *
 * @author Carlos Eduardo Pauluk
 */
class PessoaContatoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return PessoaContato::class;
    }
}