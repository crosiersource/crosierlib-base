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

}