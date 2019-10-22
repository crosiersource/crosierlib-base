<?php

namespace CrosierSource\CrosierLibBaseBundle\Repository\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\App;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu;
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
        return $this->findBy(['paiUUID' => null, 'tipo' => 'PAI'], ['ordem' => 'ASC']);
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
     * @return array
     */
    public function buildMenuByEntMenuPai(EntMenu $entMenuPai): array
    {
        $entsMenu = $this->findBy(['paiUUID' => $entMenuPai->getUUID()], ['ordem' => 'ASC']);

        $rs = [];

        /** @var EntMenu $entMenu */
        foreach ($entsMenu as $entMenu) {
            $entMenuInJson = $this->entMenuInJson($entMenu);
            $this->addFilhosInJson($entMenu, $entMenuInJson);
            $rs[] = $entMenuInJson;
        }
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

        $urlBase = $this->getEntityManager()->getRepository(AppConfig::class)->findConfigByCrosierEnv($app, 'URL');

        return [
            'id' => $entMenu->getId(),
            'label' => $entMenu->getLabel(),
            'icon' => $entMenu->getIcon(),
            'tipo' => $entMenu->getTipo(),
            'ordem' => $entMenu->getOrdem(),
            'cssStyle' => $entMenu->getCssStyle(),
            'url' => $urlBase . $entMenu->getUrl(),
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
                $superPai = $this->findOneBy(['UUID' => $superPai->getPaiUUID()]);
            }
            $entMenu->setSuperPai($superPai);
        }
    }

    /**
     * @param EntMenu $entMenu
     * @param array $json
     * @return array
     */
    private function addFilhosInJson(EntMenu $entMenu, array &$json): array
    {
        $this->fillTransients($entMenu);
        if ($entMenu->getFilhos() && count($entMenu->getFilhos()) > 0) {
            foreach ($entMenu->getFilhos() as $filho) {
                $filhoJson = $this->entMenuInJson($filho);
                $this->addFilhosInJson($filho, $filhoJson);
                $json['filhos'][] = $filhoJson;
            }
        }
        return $json;
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

        $tree = array();

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
        $this->fillTransients($pai);
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


}
