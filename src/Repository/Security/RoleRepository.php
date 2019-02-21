<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\Role;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * RepositoryUtils para a entidade Role.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RoleRepository extends FilterRepository
{

    public static function getEntityClass()
    {
        return Role::class;
    }
}
