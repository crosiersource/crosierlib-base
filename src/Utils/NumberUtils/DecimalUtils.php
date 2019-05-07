<?php


namespace CrosierSource\CrosierLibBaseBundle\Utils\NumberUtils;

use NumberFormatter;

/**
 * Class DecimalUtils
 * @package CrosierSource\CrosierLibBaseBundle\Utils\NumberUtils
 *
 * @author Carlos Eduardo Pauluk
 */
class DecimalUtils
{

    public static function parseStr($str)
    {
        $fmt = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
        return $fmt->parse($str);
    }

}