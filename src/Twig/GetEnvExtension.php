<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class GetEnvExtension extends AbstractExtension
{

    public function getFunctions()
    {
        return array(
            new TwigFunction('getEnv', array($this, 'getEnv')),
        );
    }

    function getEnv($var)
    {
        return trim(getenv(trim($var)));
    }
}