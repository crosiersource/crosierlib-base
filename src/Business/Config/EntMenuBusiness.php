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

    public function saveOrdem($ordArr)
    {
        $i = 1;
        foreach ($ordArr as $ord) {
            /** @var EntMenu $entMenu */
            $entMenu = $this->entityHandler->getDoctrine()->getRepository(EntMenu::class)->find($ord);
            $entMenu->setOrdem($i++);
            $this->entityHandler->save($entMenu);
        }

    }

}

