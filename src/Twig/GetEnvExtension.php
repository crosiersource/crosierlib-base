<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class GetEnvExtension
 *
 * @package CrosierSource\CrosierLibBaseBundle\Twig
 * @author Carlos Eduardo Pauluk
 */
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