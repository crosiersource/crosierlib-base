<?php


namespace CrosierSource\CrosierLibBaseBundle\Controller\Base;


use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\ItemInterface;

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

    public function getUserById(int $id): JsonResponse
    {
        $cache = new FilesystemAdapter('crosier-core', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
        return $cache->get('getUserById_' . $id, function (ItemInterface $item) use ($id) {
            $doctrine = $this->container->get('doctrine');
            $repoUser = $doctrine->getRepository(User::class);
            $user = $repoUser->find($id);
            if ($user) {
                return new JsonResponse(
                    [
                        'id' => $user->getId(),
                        'username' => $user->username,
                        'nome' => $user->nome,
                    ]
                );
            } else {
                return new JsonResponse();
            }
        });
    }

}