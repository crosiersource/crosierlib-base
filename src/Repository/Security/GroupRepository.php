<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\Group;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * RepositoryUtils para a entidade Group.
 *
 * @author Carlos Eduardo Pauluk
 */
class GroupRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return Group::class;
    }
}
