<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @package CrosierSource\CrosierLibBaseBundle\Twig
 * @author Carlos Eduardo Pauluk
 */
class StringUtilsFilter extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('mascarar', [$this, 'mascarar']),
            new TwigFilter('mascararCnpjCpf', [$this, 'mascararCnpjCpf']),
            new TwigFilter('strpad', [$this, 'strpad']),
        ];
    }

    public function mascarar(string $valor, $mascara)
    {
        return StringUtils::mascarar($valor, $mascara);
    }

    public function mascararCnpjCpf(string $doc)
    {
        return StringUtils::mascararCnpjCpf($doc);
    }

    public function strpad($number, $pad_length, $pad_string = '0'): string
    {
        return StringUtils::strpad($number, $pad_length, $pad_string);
    }
}