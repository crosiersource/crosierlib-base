<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\EntityChange;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade EntityChange.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class EntityChangeRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return EntityChange::class;
    }


}

