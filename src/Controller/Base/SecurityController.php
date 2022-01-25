<?php


namespace CrosierSource\CrosierLibBaseBundle\Controller\Base;


use CrosierSource\CrosierLibBaseBundle\Entity\Base\DiaUtil;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Repository\Base\DiaUtilRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Carlos Eduardo Pauluk
 */
class SecurityController extends AbstractController
{

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function whoami(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        return new JsonResponse(
            [
                'id' => $user->getId(), 
                'username' => $user->username,
                'nome' => $user->nome,
                'roles' => $user->getRoles()
            ]
        );
    }
    
}