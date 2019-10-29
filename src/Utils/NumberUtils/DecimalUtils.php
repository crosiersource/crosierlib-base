<?php

namespace CrosierSource\CrosierLibBaseBundle\Utils\NumberUtils;

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
        if (!$str) {
            return null;
        }
        $fmt = new \NumberFormatter('pt_BR', \NumberFormatter::DECIMAL);
        return $fmt->parse($str);
    }

    public static function roundUp($number, $precision = 2): float
    {
        $fig = (int)str_pad('1', $precision, '0');
        return (ceil($number * $fig) / $fig);
    }

    public static function roundDown($number, $precision = 2): float
    {
        $fig = (int)str_pad('1', $precision, '0');
        return (floor($number * $fig) / $fig);
    }

}