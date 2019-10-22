<?php

namespace CrosierSource\CrosierLibBaseBundle\Business\Config;

use CrosierSource\CrosierLibBaseBundle\Entity\Config\EntMenu;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\EntMenuEntityHandler;

class EntMenuBusiness
{
    /**
     * @var EntMenuEntityHandler
     */
    private $entityHandler;

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

}

