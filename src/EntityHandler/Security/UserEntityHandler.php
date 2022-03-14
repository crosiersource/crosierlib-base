<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Security;

use CrosierSource\CrosierLibBaseBundle\Business\Config\SyslogBusiness;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\Role;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @author Carlos Eduardo Pauluk
 */
class UserEntityHandler extends EntityHandler
{

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * @param EntityManagerInterface $doctrine
     * @param Security $security
     * @param ParameterBagInterface $parameterBag
     * @param SyslogBusiness $syslog
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $doctrine,
                                Security $security,
                                ParameterBagInterface $parameterBag,
                                SyslogBusiness $syslog,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($doctrine, $security, $parameterBag, $syslog->setApp('core')->setComponent(self::class));
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param User $user
     * @return mixed|void
     */
    public function beforeSave($user)
    {
        if (is_array($user->userRoles)) {
            $roles = $user->userRoles;
            $user->userRoles = new ArrayCollection();
            foreach ($roles as $role) {
                $user->userRoles->add($role);
            }
        }
        /** @var User $user */
        if ($user->password && strlen($user->password) < 53) {
            $encoded = $this->passwordEncoder->encodePassword($user, $user->password);
            $user->password = $encoded;
        } elseif ($user->getId() && !$user->password) {
            $savedPassword = $this->doctrine->getRepository(User::class)->getPassword($user);
            $user->password = $savedPassword;
        }
        $user->email = mb_strtolower($user->email);
        $user->username = mb_strtolower($user->username);
    }

    /**
     * @param User $user
     * @return mixed
     * @throws \Exception
     */
    public function renewTokenApi(User $user)
    {
        if (!$user->apiToken) {
            $user->apiToken = bin2hex(random_bytes(60));
        }
        $user->apiTokenExpiresAt = new \DateTime('+1680 hour');
        $this->save($user);
        return $user->apiToken;
    }

    /**
     * @param User $user
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function revogarApiToken(User $user): void
    {
        $user->apiToken = null;
        $this->save($user);
    }

    /**
     * Verifica e conserta as roles de um usuário (ex.: usuário que seja ROLE_ADMIN deve ter também todas as outras roles)
     *
     * @param User $user
     */
    public function fixRoles(User $user): void
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $todas = $this->getDoctrine()->getRepository(Role::class)->findAll();
            /** @var Role $role */
            foreach ($todas as $role) {
                if (!in_array($role->role, $user->getRoles())) {
                    $user->userRoles->add($role);
                }
            }
            $this->save($user);
        }
    }


    public function getEntityClass()
    {
        return User::class;
    }
}