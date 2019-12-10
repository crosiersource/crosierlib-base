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

}