<?php

namespace CrosierSource\CrosierLibBaseBundle;


use CrosierSource\CrosierLibBaseBundle\DependencyInjection\CrosierLibBaseExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CrosierLibBaseBundle extends Bundle
{


    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new CrosierLibBaseExtension();
        }
        return $this->extension;

    }


}