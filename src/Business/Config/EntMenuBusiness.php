<?php

namespace CrosierSource\CrosierLibBaseBundle\Business\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\EntMenuEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use http\Exception\RuntimeException;

class EntMenuBusiness
{
    private EntMenuEntityHandler $entityHandler;

    public function __construct(EntMenuEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    /**
     * @param array $ordArr
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function saveOrdem(array $ordArr): void
    {
        $i = 1;
        $dropDownAtual = null;
        foreach ($ordArr as $ord) {
            /** @var EntMenu $entMenu */
            $entMenu = $this->entityHandler->getDoctrine()->getRepository(EntMenu::class)->find($ord);
            if ($entMenu->getTipo() === 'DROPDOWN' && $dropDownAtual !== $entMenu) {
                $dropDownAtual = $entMenu;
            }
            if ($dropDownAtual && $entMenu->getTipo() === 'ENT') {
                $entMenu->setPaiUUID($dropDownAtual->getUUID());
            }
            $entMenu->setOrdem($i++);
            $this->entityHandler->save($entMenu);
        }
    }

    /**
     * @param EntMenu $entMenu
     * @param string $yaml
     * @return EntMenu
     * @throws \Doctrine\DBAL\Exception
     */
    public function recreateFromYaml(EntMenu $entMenu, string $yaml): EntMenu
    {
        try {
            $yamlArr = yaml_parse($yaml);
            if (!($yamlArr[0]['filhos'] ?? false) || $yamlArr[0]['UUID'] !== $entMenu->getUUID()) {
                throw new \RuntimeException();
            }
            $conn = $this->entityHandler->getDoctrine()->getConnection();
            $conn->beginTransaction();
            $conn->delete('cfg_entmenu', ['pai_uuid' => $entMenu->getUUID()]);
            $this->saveEntries($entMenu, $yamlArr[0]['filhos']);
            $conn->commit();
            return $entMenu;
        } catch (\Throwable $e) {
            $conn->rollBack();
            throw new ViewException('Erro ao recriar o menu pelo yaml', 0, $e);
        }
    }

    /**
     * @param EntMenu $entMenuPai
     * @param array $yamlArr
     */
    private function saveEntries(EntMenu $entMenuPai, array $yamlArr)
    {
        foreach ($yamlArr as $y) {
            $entMenu = $this->saveEntry($entMenuPai, $y);
            if ($y['filhos'] ?? false) {
                $this->saveEntries($entMenu, $y['filhos']);
            }
        }
    }

    /**
     * @param EntMenu $pai
     * @param array $yamlArr
     * @return \CrosierSource\CrosierLibBaseBundle\Entity\EntityId|object
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    private function saveEntry(EntMenu $pai, array $yamlArr)
    {
        $entMenu = new EntMenu();
        $entMenu->setPaiUUID($pai->getUUID());
        $entMenu->setLabel($yamlArr['label']);
        $entMenu->setUUID($yamlArr['UUID']);
        $entMenu->setIcon($yamlArr['icon']);
        $entMenu->setTipo($yamlArr['tipo']);
        $entMenu->setAppUUID($yamlArr['appUUID']);
        $entMenu->setCssStyle($yamlArr['cssStyle']);
        $entMenu->setUrl($yamlArr['url']);
        return $this->entityHandler->save($entMenu);
    }

}

