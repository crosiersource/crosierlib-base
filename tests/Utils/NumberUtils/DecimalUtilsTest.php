<?php

namespace Tests\Utils\DateTimeUtils;

use CrosierSource\CrosierLibBaseBundle\Utils\NumberUtils\DecimalUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeUtilsTest
 *
 * @package Tests\Utils\DateTimeUtils
 * @author Carlos Eduardo Pauluk
 */
class DecimalUtilsTest extends TestCase
{

    public function testRounds()
    {

        $testes = [
            // valor, precisão, UP, HALF_UP, DOWN, HALF_DOWN
            [1.2340, 2, 1.240, 1.23, 1.230, 1.230],
            [1.2340, 3, 1.234, 1.234, 1.234, 1.234],
            [1.2345, 3, 1.235, 1.235, 1.234, 1.234],
            [0.99, 2, 0.99, 0.99, 0.99, 0.99],
            [0.99, 1, 1.00, 1.00, 0.90, 1.00],
            [123.456, 2, 123.46, 123.46, 123.45, 123.46],
            [123.455, 2, 123.46, 123.46, 123.45, 123.45],
            [123.454, 2, 123.46, 123.45, 123.45, 123.45],
            [-123.454, 2, -123.45, -123.45, -123.46, -123.45], // ??
        ];


        foreach ($testes as $t) {
            $this->assertEquals($t[2], DecimalUtils::round($t[0], $t[1], DecimalUtils::ROUND_UP));
            $this->assertEquals($t[3], DecimalUtils::round($t[0], $t[1], DecimalUtils::ROUND_HALF_UP));
            $this->assertEquals($t[4], DecimalUtils::round($t[0], $t[1], DecimalUtils::ROUND_DOWN));
            $this->assertEquals($t[5], DecimalUtils::round($t[0], $t[1], DecimalUtils::ROUND_HALF_DOWN));
        }

    }


    public function testDividirValorProporcionalmente()
    {
        $testes = [
            [100, [200, 200, 200, 200, 200], [20, 20, 20, 20, 20]],
            [100, [10, 10, 10], [33.34, 33.33, 33.33]],
            [12.34, [10, 20, 30], [2.07, 4.11, 6.16]],
            [23.45, [7, 17, 97], [1.37, 3.29, 18.79]],
            [159.76, [13.48, 19.17, 7.3], [53.92, 76.65, 29.19]],
            [0.00, [1, 1, 1], [0.00, 0.00, 0.00]],
            [0.00, [0, 0, 0], [0.00, 0.00, 0.00]],
            [0.01, [1, 1, 1], [0.01, 0.00, 0.00]],
            [0.01, [0, 0, 1], [0.00, 0.00, 0.01]],
            [0.01, [0, 1, 0], [0.00, 0.01, 0.00]],
            [0.02, [0, 1, 0], [0.00, 0.02, 0.00]],
            [0.02, [50, 50, 0], [0.01, 0.01, 0.00]],
        ];
        foreach ($testes as $t) {
            $this->assertEquals($t[2], DecimalUtils::dividirValorProporcionalmente($t[0], $t[1]));
            $somaPartes = DecimalUtils::somarValoresMonetarios($t[2]);
            $this->assertEquals($t[0], $somaPartes);
        }


    }


}