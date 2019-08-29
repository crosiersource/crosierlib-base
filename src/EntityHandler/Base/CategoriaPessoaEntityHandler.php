<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Base;

use CrosierSource\CrosierLibBaseBundle\Entity\Base\CategoriaPessoa;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para CategoriaPessoa.
 *
 * @author Carlos Eduardo Pauluk
 */
class CategoriaPessoaEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return CategoriaPessoa::class;
    }
}