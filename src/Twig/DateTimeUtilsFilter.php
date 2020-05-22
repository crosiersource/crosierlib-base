<?php

namespace CrosierSource\CrosierLibBaseBundle\Twig;

use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @package CrosierSource\CrosierLibBaseBundle\Twig
 * @author Carlos Eduardo Pauluk
 */
class DateTimeUtilsFilter extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('parseDateStr', [$this, 'parseDateStr']),
        ];
    }

    public function parseDateStr(string $strDate)
    {
        return DateTimeUtils::parseDateStr($strDate);
    }

}