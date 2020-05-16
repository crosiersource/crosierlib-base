<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class GetEnvExtension
 *
 * @package CrosierSource\CrosierLibBaseBundle\Twig
 * @author Carlos Eduardo Pauluk
 */
class StringUtilsExtension extends AbstractExtension
{

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('isJson', array($this, 'isJson')),
        );
    }

    /**
     * @param $var
     * @return bool
     */
    function isJson($var)
    {
        return StringUtils::isJson($var);
    }
}