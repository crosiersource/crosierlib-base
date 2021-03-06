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
        $entsMenu = $this->findBy(['paiUUID' => $entMenuPai->getUUID()], ['ordem' => 'ASC']);

        $rs = [];

        /** @var EntMenu $entMenu */
        foreach ($entsMenu as $entMenu) {
            $temPermissao = true;
            if ($entMenu->getRoles()) {
                $roles = explode(',', $entMenu->getRoles());
                if (!array_intersect($user->getRoles(), $roles)) {
                    $temPermissao = false;
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
        $app = $repoApp->findOneBy(['UUID' => $entMenu->getAppUUID()]);

        if (!$app) {
            throw new \RuntimeException('Nenhum app encontrado com UUID ' . $entMenu->getAppUUID());
        }

        $urlBase = $this->getEntityManager()->getRepository(AppConfig::class)->findConfigByCrosierEnv($app, 'URL');

        return [
            'id' => $entMenu->getId(),
            'label' => $entMenu->getLabel(),
            'icon' => $entMenu->getIcon(),
            'tipo' => $entMenu->getTipo(),
            'ordem' => $entMenu->getOrdem(),
            'cssStyle' => $entMenu->getCssStyle(),
            'url' => $urlBase . $entMenu->getUrl(),
            'roles' => $entMenu->getRoles(),
            'nivel' => $entMenu->getNivel(),
            'pai' => [
                'id' => $entMenu->getPai() ? $entMenu->getPai()->getId() : null,
                'tipo' => $entMenu->getPai() ? $entMenu->getPai()->getTipo() : null,
                'icon' => $entMenu->getPai() ? $entMenu->getPai()->getIcon() : null,
                'label' => $entMenu->getPai() ? $entMenu->getPai()->getLabel() : null
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
        if ($entMenu->getPaiUUID()) {
            if (!$entMenu->getPai()) {
                $pai = $this->findOneBy(['UUID' => $entMenu->getPaiUUID()]);
                $entMenu->setPai($pai);
            }

            if (!$entMenu->getFilhos()) {
                $filhos = $this->findBy(['paiUUID' => $entMenu->getUUID()], ['ordem' => 'ASC']);
                $entMenu->setFilhos($filhos);
            }
            $superPai = $entMenu->getPai();

            while ($superPai->getPaiUUID()) {
                $nivel++;
                $superPai = $this->findOneBy(['UUID' => $superPai->getPaiUUID()]);
            }
            $entMenu->setSuperPai($superPai);
        }
        $entMenu->setNivel($nivel);
    }


    public function fillFilhos(EntMenu $entMenu)
    {
        $ql = "SELECT e FROM CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu e WHERE e.paiUUID = :entMenuPaiUUID ORDER BY e.ordem";
        $qry = $this->getEntityManager()->createQuery($ql);
        $qry->setParameter('entMenuPaiUUID', $entMenu->getUUID());
        $filhos = $qry->getResult();
        if ($filhos) {
            $entMenu->setFilhos($filhos);
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
        if ($entMenu->getFilhos() && count($entMenu->getFilhos()) > 0) {
            foreach ($entMenu->getFilhos() as $filho) {
                $temPermissao = true;
                if ($filho->getRoles()) {
                    $roles = explode(',', $filho->getRoles());
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
        $qry->setParameter('entMenuPaiUUID', $entMenuPai->getUUID());

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
        if ($pai->getFilhos()) {
            $filhos = $pai->getFilhos();
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
            'label' => $entMenu->getLabel(),
            'UUID' => $entMenu->getUUID(),
            'icon' => $entMenu->getIcon(),
            'tipo' => $entMenu->getTipo(),
            'appUUID' => $entMenu->getAppUUID(),
            'cssStyle' => $entMenu->getCssStyle(),
            'url' => $entMenu->getUrl(),
        ];
        
        if ($entMenu->getFilhos()) {
            $a['filhos'] = [];
            foreach ($entMenu->getFilhos() as $entMenu) {
                $this->exportYamlEntriesFilhos($entMenu, $a['filhos']);
            }
        }
        $arr[] = $a;
    }

    private function exportYamlEntry(EntMenu $entMenu, int $nivel): string
    {
        $sep = '\t';
        $campos = [];
        $campos[] = $entMenu->getLabel();
        $campos[] = $entMenu->getUUID();
        $campos[] = $entMenu->getIcon();
        $campos[] = $entMenu->getTipo();
        $campos[] = $entMenu->getAppUUID();
        $campos[] = $entMenu->getPaiUUID();
        $campos[] = $entMenu->getOrdem();
        $campos[] = $entMenu->getCssStyle();
        $campos[] = $entMenu->getUrl();
        $str = implode($sep, $campos);
        return str_pad('', $nivel * 4, ' ', STR_PAD_LEFT) . $str . PHP_EOL;
    }


    private function exportEntriesFilhos(EntMenu $entMenu, string &$str, int $nivel)
    {
        $str .= $this->exportEntry($entMenu, $nivel);
        if ($entMenu->getFilhos()) {
            $nivel++;
            foreach ($entMenu->getFilhos() as $entMenu) {
                $this->exportEntriesFilhos($entMenu, $str, $nivel);
            }
        }
    }


    private function exportEntry(EntMenu $entMenu, int $nivel): string
    {
        $sep = '\t';
        $campos = [];
        $campos[] = $entMenu->getLabel();
        $campos[] = $entMenu->getUUID();
        $campos[] = $entMenu->getIcon();
        $campos[] = $entMenu->getTipo();
        $campos[] = $entMenu->getAppUUID();
        $campos[] = $entMenu->getPaiUUID();
        $campos[] = $entMenu->getOrdem();
        $campos[] = $entMenu->getCssStyle();
        $campos[] = $entMenu->getUrl();
        $str = implode($sep, $campos);
        return str_pad('', $nivel * 4, ' ', STR_PAD_LEFT) . $str . PHP_EOL;
    }


}
