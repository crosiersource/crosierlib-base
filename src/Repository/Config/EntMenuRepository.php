<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\App;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu;
use CrosierSource\CrosierLibBaseBundle\Entity\Security\User;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade EntMenu.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class EntMenuRepository extends FilterRepository
{


    public function getEntityClass(): string
    {
        return EntMenu::class;
    }

    /**
     * @return array
     */
    public function getMenusPais(): array
    {
        return $this->findBy(['paiUUID' => null, 'tipo' => 'PAI'], ['label' => 'ASC']);
    }


    /**
     * @return array
     */
    public function getMenusPaisOuDropdowns(): array
    {
        $dql = "SELECT e FROM CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu e WHERE e.paiUUID IS NULL OR e.tipo = :tipo ORDER BY e.label";
        $qry = $this->getEntityManager()->createQuery($dql);
        $qry->setParameter('tipo', 'DROPDOWN');
        return $qry->getResult();
    }

    /**
     * @param EntMenu $entMenuPai
     * @param User $user
     * @return array
     */
    public function buildMenuByEntMenuPai(EntMenu $entMenuPai, User $user): array
    {
        $entsMenu = $this->findBy(['paiUUID' => $entMenuPai->UUID], ['ordem' => 'ASC']);

        $rs = [];

        /** @var EntMenu $entMenu */
        foreach ($entsMenu as $entMenu) {
            $temPermissao = true;
            if ($entMenu->roles) {
                $roles = explode(',', $entMenu->roles);

                foreach ($roles as $role) {
                    if ($role[0] === '!') {
                        if (in_array(substr($role, 1), $user->getRoles(), true)) {
                            $temPermissao = false;
                            break;
                        }
                    } else {
                        if (!in_array($role, $user->getRoles(), true)) {
                            $temPermissao = false;
                            break;
                        }
                    }
                }

            }
            if ($temPermissao) {
                $entMenuInJson = $this->entMenuInJson($entMenu);
                $this->addFilhosInJson($entMenu, $entMenuInJson, $user);
                $rs[] = $entMenuInJson;
            }
        }
        $this->limparPaisSemFilhos($rs);
        return $rs;
    }

    /**
     * @param EntMenu $entMenu
     * @return array
     */
    private function entMenuInJson(EntMenu $entMenu): array
    {
        $this->fillTransients($entMenu);
        /** @var AppRepository $repoApp */
        $repoApp = $this->getEntityManager()->getRepository(App::class);
        /** @var App $app */
        $app = $repoApp->findOneBy(['UUID' => $entMenu->appUUID]);

        if (!$app) {
            $errMsg = 'Problema ao construir menu (entMenuInJson). Nenhum app encontrado com UUID ' . $entMenu->appUUID;
            throw new \RuntimeException($errMsg);
        }

        $urlBase = $this->getEntityManager()->getRepository(AppConfig::class)->findConfigByCrosierEnv($app, 'URL');

        return [
            'id' => $entMenu->getId(),
            'label' => $entMenu->label,
            'icon' => $entMenu->icon,
            'tipo' => $entMenu->tipo,
            'ordem' => $entMenu->ordem,
            'cssStyle' => $entMenu->cssStyle,
            'url' => $urlBase . $entMenu->url,
            'roles' => $entMenu->roles,
            'nivel' => $entMenu->nivel,
            'pai' => [
                'id' => $entMenu->pai ? $entMenu->pai->getId() : null,
                'tipo' => $entMenu->pai ? $entMenu->pai->tipo : null,
                'icon' => $entMenu->pai ? $entMenu->pai->icon : null,
                'label' => $entMenu->pai ? $entMenu->pai->label : null
            ]
        ];
    }

    /**
     * Preenche os atributos transientes.
     *
     * @param EntMenu $entMenu
     */
    public function fillTransients(EntMenu $entMenu): void
    {
        $nivel = 0;
        if ($entMenu->paiUUID) {
            if (!$entMenu->pai) {
                $pai = $this->findOneBy(['UUID' => $entMenu->paiUUID]);
                $entMenu->pai = $pai;
            }

            if (!$entMenu->filhos) {
                $filhos = $this->findBy(['paiUUID' => $entMenu->UUID], ['ordem' => 'ASC']);
                $entMenu->filhos = $filhos;
            }
            $superPai = $entMenu->pai;

            while ($superPai->paiUUID) {
                $nivel++;
                $superPai = $this->findOneBy(['UUID' => $superPai->paiUUID]);
            }
            $entMenu->superPai = $superPai;
        }
        $entMenu->nivel = $nivel;
    }


    public function fillFilhos(EntMenu $entMenu)
    {
        $ql = "SELECT e FROM CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu e WHERE e.paiUUID = :entMenuPaiUUID ORDER BY e.ordem";
        $qry = $this->getEntityManager()->createQuery($ql);
        $qry->setParameter('entMenuPaiUUID', $entMenu->UUID);
        $filhos = $qry->getResult();
        if ($filhos) {
            $entMenu->filhos = $filhos;
            foreach ($filhos as $filho) {
                $this->fillFilhos($filho);
            }
        }
    }

    /**
     * @param EntMenu $entMenu
     * @param array $json
     * @param User $user
     * @return array
     */
    private function addFilhosInJson(EntMenu $entMenu, array &$json, User $user): array
    {
        if ($entMenu->filhos && count($entMenu->filhos) > 0) {
            foreach ($entMenu->filhos as $filho) {
                $temPermissao = true;
                if ($filho->roles) {
                    $roles = explode(',', $filho->roles);
                    if (!array_intersect($user->getRoles(), $roles)) {
                        $temPermissao = false;
                    }
                }
                if ($temPermissao) {
                    $filhoJson = $this->entMenuInJson($filho);
                    $this->addFilhosInJson($filho, $filhoJson, $user);
                    $json['filhos'][] = $filhoJson;
                }
            }
        }
        return $json;
    }

    private function limparPaisSemFilhos(array &$rs)
    {
        // Limpa os DROPDOWN que não tenham filhos (por falta de permissão ou outra coisa)
        foreach ($rs as $key => $r) {
            if ($r['tipo'] === 'DROPDOWN') {
                if (!isset($r['filhos']) || count($r['filhos']) < 1) {
                    unset($rs[$key]);
                } else {
                    $this->limparPaisSemFilhos($r['filhos']);
                }
            }
        }
    }

    /**
     * Cria a árvore do menu para ser manipulada na tela de organização de menus.
     *
     * @param EntMenu $entMenuPai
     * @return array
     */
    public function makeTree(EntMenu $entMenuPai): array
    {
        $ql = "SELECT e FROM CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu e WHERE e.paiUUID = :entMenuPaiUUID ORDER BY e.ordem";
        $qry = $this->getEntityManager()->createQuery($ql);
        $qry->setParameter('entMenuPaiUUID', $entMenuPai->UUID);

        $pais = $qry->getResult();

        $tree = [];

        foreach ($pais as $pai) {
            $tree[] = $this->entMenuInJson($pai);
            $this->getFilhos($pai, $tree);
        }
        return $tree;
    }


    /**
     * @param EntMenu $pai
     * @param $tree
     */
    private function getFilhos(EntMenu $pai, &$tree): void
    {
        if ($pai->filhos) {
            $filhos = $pai->filhos;
            foreach ($filhos as $filho) {
                $tree[] = $this->entMenuInJson($filho);
                $this->getFilhos($filho, $tree);
            }
        } else {
            return;
        }
    }


    public function exportMenuEntries(EntMenu $entMenu): string
    {
        //$tree = $this->makeTree($entMenu);
        $this->fillFilhos($entMenu);
        $arr = [];
        $this->exportYamlEntriesFilhos($entMenu, $arr);
        $yaml = yaml_emit($arr);
        return $yaml;
    }

    private function exportYamlEntriesFilhos(EntMenu $entMenu, array &$arr)
    {
        $a = [
            'label' => $entMenu->label,
            'UUID' => $entMenu->UUID,
            'icon' => $entMenu->icon,
            'tipo' => $entMenu->tipo,
            'appUUID' => $entMenu->appUUID,
            'cssStyle' => $entMenu->cssStyle,
            'url' => $entMenu->url,
        ];

        if ($entMenu->filhos) {
            $a['filhos'] = [];
            foreach ($entMenu->filhos as $entMenu) {
                $this->exportYamlEntriesFilhos($entMenu, $a['filhos']);
            }
        }
        $arr[] = $a;
    }

    private function exportYamlEntry(EntMenu $entMenu, int $nivel): string
    {
        $sep = '\t';
        $campos = [];
        $campos[] = $entMenu->label;
        $campos[] = $entMenu->UUID;
        $campos[] = $entMenu->icon;
        $campos[] = $entMenu->tipo;
        $campos[] = $entMenu->appUUID;
        $campos[] = $entMenu->paiUUID;
        $campos[] = $entMenu->ordem;
        $campos[] = $entMenu->cssStyle;
        $campos[] = $entMenu->url;
        $str = implode($sep, $campos);
        return str_pad('', $nivel * 4, ' ', STR_PAD_LEFT) . $str . PHP_EOL;
    }


    private function exportEntriesFilhos(EntMenu $entMenu, string &$str, int $nivel)
    {
        $str .= $this->exportEntry($entMenu, $nivel);
        if ($entMenu->filhos) {
            $nivel++;
            foreach ($entMenu->filhos as $entMenu) {
                $this->exportEntriesFilhos($entMenu, $str, $nivel);
            }
        }
    }


    private function exportEntry(EntMenu $entMenu, int $nivel): string
    {
        $sep = '\t';
        $campos = [];
        $campos[] = $entMenu->label;
        $campos[] = $entMenu->UUID;
        $campos[] = $entMenu->icon;
        $campos[] = $entMenu->tipo;
        $campos[] = $entMenu->appUUID;
        $campos[] = $entMenu->paiUUID;
        $campos[] = $entMenu->ordem;
        $campos[] = $entMenu->cssStyle;
        $campos[] = $entMenu->url;
        $str = implode($sep, $campos);
        return str_pad('', $nivel * 4, ' ', STR_PAD_LEFT) . $str . PHP_EOL;
    }


}
