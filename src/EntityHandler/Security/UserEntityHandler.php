<?php

namespace CrosierSource\CrosierLibBaseBundle\EntityHandler\Security;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class UserEntityHandler
 * @package App\EntityHandler\Security
 * @author Carlos Eduardo Pauluk
 */
class UserEntityHandler extends EntityHandler
{

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * UserEntityHandler constructor.
     * @param RegistryInterface $doctrine
     * @param Security $security
     * @param ParameterBagInterface $parameterBag
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(RegistryInterface $doctrine, Security $security, ParameterBagInterface $parameterBag, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->parameterBag = $parameterBag;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct($doctrine, $security, $parameterBag);
        
    }

    public function beforeSave($user)
    {
        /** @var User $user */
        if ($user->getPassword() && strlen($user->getPassword()) < 53) {
            $encoded = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
        } elseif ($user->getId() && !$user->getPassword()) {
            $savedPassword = $this->doctrine->getRepository(User::class)->getPassword($user);
            $user->setPassword($savedPassword);
        }
    }

    /**
     * @param User $user
     * @return mixed
     * @throws \Exception
     */
    public function renewTokenApi(User $user)
    {
        if (!$user->getApiToken()) {
            $user->setApiToken(bin2hex(random_bytes(60)));
        }
        $user->setApiTokenExpiresAt(new \DateTime('+1680 hour'));
        $this->save($user);
        return $user->getApiToken();
    }

    /**
     * @param User $user
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function revogarApiToken(User $user): void
    {
        $user->setApiToken(null);
        $this->save($user);
    }


    public function getEntityClass()
    {
        return User::class;
    }
}