<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenuLocator;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOStatement;
use Symfony\Component\Security\Core\Security;

/**
 * Repository para a entidade EntMenuLocator.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class EntMenuLocatorRepository extends FilterRepository
{

    /** @var Security */
    public $security;

    public function getEntityClass(): string
    {
        return EntMenuLocator::class;
    }

    /**
     * @param string $url
     * @param User $user
     * @return array
     * @throws ViewException
     */
    public function getMenuByUrl(string $url, User $user): array
    {
        try {
            $sql = 'SELECT menu_uuid, quem, nao_contendo FROM cfg_entmenu_locator WHERE :url REGEXP url_regexp ORDER BY length(url_regexp), length(quem)';
            /** @var Connection $conn */
            $conn = $this->getEntityManager()->getConnection();
            /** @var PDOStatement $stmt */
            $stmt = $conn->executeQuery($sql, ['url' => $url]);
            $entMenuUUID = null;
            while ($r = $stmt->fetch()) {
                $naoContendo = $r['nao_contendo'] ?? null;
                if ($naoContendo) {
                    $naoContendoExps = explode(',', $naoContendo);
                    foreach ($naoContendoExps as $naoContendoExp) {
                        if (strpos($url, $naoContendoExp) !== FALSE) {
                            continue 2;
                        }
                    }
                }
                if ($r['quem'] === '*') {
                    $entMenuUUID = $r['menu_uuid'];
                    break;
                }
                // else
                /** @var User $user */
                $user = $this->security->getUser();
                if (strpos($r['quem'], 'u:') === 0) {
                    $users = explode(',', substr($r['quem'], 2));
                    if (in_array($user->getUsername(), $users, true)) {
                        $entMenuUUID = $r['menu_uuid'];
                        break;
                    }
                }
                if (strpos($r['quem'], 'g:') === 0) {
                    $groups = explode(',', substr($r['quem'], 2));
                    if (in_array($user->getGroup()->getGroupname(), $groups, true)) {
                        $entMenuUUID = $r['menu_uuid'];
                        break;
                    }
                }
                if (strpos($r['quem'], 'r:') === 0) {
                    $roles = explode(',', substr($r['quem'], 2));
                    foreach ($roles as $role) {
                        if (in_array($role, $user->getRoles(), true)) {
                            $entMenuUUID = $r['menu_uuid'];
                            break;
                        }
                    }
                }
            }

            if (!$entMenuUUID) {
                throw new \RuntimeException('Menu não encontrado');
            }

            /** @var EntMenuRepository $repoEntMenu */
            $repoEntMenu = $this->getEntityManager()->getRepository(EntMenu::class);
            /** @var EntMenu $entMenu */
            $entMenu = $repoEntMenu->findOneBy(['UUID' => $entMenuUUID]);

            return $repoEntMenu->buildMenuByEntMenuPai($entMenu, $user);
        } catch (\Exception $e) {
            throw new ViewException('Erro ao buscar menu');
        }
    }

}
