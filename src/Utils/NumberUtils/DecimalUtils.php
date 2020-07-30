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

    public const ROUND_UP = 1;
    public const ROUND_HALF_UP = 2;
    public const ROUND_DOWN = 3;
    public const ROUND_HALF_DOWN = 4;

    /**
     * Converte um número na notação brasileira para double.
     *
     * @param $str
     * @return false|float|int|mixed|null
     */
    public static function parseStr($str)
    {
        if ((float)$str === 0.0) {
            return 0.0;
        }
        if (!$str) {
            return null;
        }
        $fmt = new \NumberFormatter('pt_BR', \NumberFormatter::DECIMAL);
        return $fmt->parse($str);
    }

    /**
     * @param $number
     * @param int $precision
     * @param int $tipo
     * @return float
     */
    public static function round($number, $precision = 2, $tipo = self::ROUND_HALF_UP)
    {
        switch ($tipo) {
            case self::ROUND_HALF_UP:
                return round($number, $precision, PHP_ROUND_HALF_UP);
            case self::ROUND_UP:
                return self::roundUp($number, $precision);
            case self::ROUND_HALF_DOWN:
                return round($number, $precision, PHP_ROUND_HALF_DOWN);
            case self::ROUND_DOWN:
                return self::roundDown($number, $precision);
            default:
                throw new \RuntimeException('tipo indefinido');
        }
    }

    /**
     * @param $number
     * @param int $precision
     * @return float
     */
    public static function roundUp($number, $precision = 2): float
    {
        $neg = $number < 0 ? -1 : 1;
        $fig = (int)str_pad('1', $precision + 1, '0');
        $aux = abs($number) * $fig;
        $ceilAux = $neg === -1 ? floor($aux) : ceil($aux);
        $div = $ceilAux / $fig;
        return $div * $neg;
    }

    /**
     * @param $number
     * @param int $precision
     * @return float
     */
    public static function roundDown($number, $precision = 2): float
    {
        $neg = $number < 0 ? -1 : 1;
        $fig = (int)str_pad('1', $precision + 1, '0');
        $aux = abs($number) * $fig;
        $ceilAux = $neg === -1 ? ceil($aux) : floor($aux);
        $div = $ceilAux / $fig;
        return $div * $neg;
    }

}