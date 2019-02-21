<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade User.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class UserRepository extends FilterRepository
{

    public static function getEntityClass()
    {
        return User::class;
    }
}
