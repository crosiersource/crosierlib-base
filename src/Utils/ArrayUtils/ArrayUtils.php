<?php


namespace CrosierSource\CrosierLibBaseBundle\Utils\ArrayUtils;

/**
 * @author Carlos Eduardo Pauluk
 */
class ArrayUtils
{

    /**
     * Ordena um array por seus índices recursivamente.
     *
     * @param $array
     * @return bool
     */
    public static function rksort(&$array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) self::rksort($value);
        }
        return ksort($array);
    }


}