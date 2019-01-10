<?php

namespace CrosierSource\CrosierLibBaseBundle;

use CrosierSource\CrosierLibUtilsBundle\DependencyInjection\CrosierLibUtilsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CrosierLibBaseBundle extends Bundle
{


    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new CrosierLibUtilsExtension();
        }
        return $this->extension;

    }


}