<?php

namespace CrosierSource\CrosierLibBaseBundle\APIClient;


use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use Symfony\Component\Security\Core\Security;

class CrosierAPIClient
{

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getAuthHeader() {
        /** @var User $user */
        $user = $this->security->getUser();
        $authHeader['X-Authorization'] = 'Bearer ' . $user->getApiToken();
        return $authHeader;
    }

}