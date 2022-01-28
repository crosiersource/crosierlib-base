<?php

namespace CrosierSource\CrosierLibBaseBundle\Business\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\EntMenuEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Doctrine\DBAL\Exception;
use Throwable;

class EntMenuBusiness
{
    private EntMenuEntityHandler $entityHandler;

    public function __construct(EntMenuEntityHandler $entityHandler)
    {
        $this->entityHandler = $entityHandler;
    }

    /**
     * @param array $ordArr
     * @throws ViewException
     */
    public function saveOrdem(array $ordArr): void
    {
        $i = 1;
        $dropDownAtual = null;
        foreach ($ordArr as $ord) {
            /** @var EntMenu $entMenu */
            $entMenu = $this->entityHandler->getDoctrine()->getRepository(EntMenu::class)->find($ord);
            if ($entMenu->tipo === 'DROPDOWN' && $dropDownAtual !== $entMenu) {
                $dropDownAtual = $entMenu;
            }
            if ($dropDownAtual && $entMenu->tipo === 'ENT') {
                $entMenu->paiUUID = $dropDownAtual->UUID;
            }
            $entMenu->ordem = $i++;
            $this->entityHandler->save($entMenu);
        }
    }

    /**
     * @param EntMenu $entMenu
     * @param string $yaml
     * @return EntMenu
     * @throws ViewException
     */
    public function recreateFromYaml(EntMenu $entMenu, string $yaml): EntMenu
    {
        $conn = $this->entityHandler->getDoctrine()->getConnection();
        try {
            $yamlArr = yaml_parse($yaml);
            if (!($yamlArr[0]['filhos'] ?? false) || $yamlArr[0]['UUID'] !== $entMenu->UUID) {
                throw new \RuntimeException();
            }
            $conn->beginTransaction();
            $conn->delete('cfg_entmenu', ['pai_uuid' => $entMenu->UUID]);
            $this->saveEntries($entMenu, $yamlArr[0]['filhos']);
            $conn->commit();
            return $entMenu;
        } catch (Throwable $e) {
            try {
                if ($conn->isTransactionActive()) {
                    $conn->rollBack();
                }
            } catch (Exception $e) {
                throw new ViewException("Erro ao efetuar o rollback - recreateFromYaml", 0, $e);
            }
            throw new ViewException('Erro ao recriar o menu pelo yaml', 0, $e);
        }
    }

    /**
     * @param EntMenu $entMenuPai
     * @param array $yamlArr
     * @throws ViewException
     */
    private function saveEntries(EntMenu $entMenuPai, array $yamlArr)
    {
        foreach ($yamlArr as $y) {
            /** @var EntMenu $entMenu */
            $entMenu = $this->saveEntry($entMenuPai, $y);
            if ($y['filhos'] ?? false) {
                $this->saveEntries($entMenu, $y['filhos']);
            }
        }
    }

    /**
     * @param EntMenu $pai
     * @param array $yamlArr
     * @return EntityId|object
     * @throws ViewException
     */
    private function saveEntry(EntMenu $pai, array $yamlArr)
    {
        $entMenu = new EntMenu();
        $entMenu->paiUUID = $pai->UUID;
        $entMenu->label = $yamlArr['label'];
        $entMenu->UUID = $yamlArr['UUID'];
        $entMenu->icon = $yamlArr['icon'];
        $entMenu->tipo = $yamlArr['tipo'];
        $entMenu->appUUID = $yamlArr['appUUID'];
        $entMenu->cssStyle = $yamlArr['cssStyle'];
        $entMenu->url = $yamlArr['url'];
        return $this->entityHandler->save($entMenu);
    }

}

