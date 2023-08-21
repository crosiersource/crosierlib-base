<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Repository para a entidade User.
 *
 * @author Carlos Eduardo Pauluk
 */
class UserRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return User::class;
    }

    public function getPassword(User $user): ?string
    {
        $sql = 'SELECT password FROM sec_user WHERE id = ?';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('password', 'password');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $user->getId());
        $rs = $query->getResult();
        return $rs[0]['password'] ?? null;
    }

    public function getUsersByEmail(string $email): array
    {
        return $this->findAllByFiltersSimpl([['email', 'LIKE', '%' . $email . '%']], ['username' => 'ASC']);
    }

}
