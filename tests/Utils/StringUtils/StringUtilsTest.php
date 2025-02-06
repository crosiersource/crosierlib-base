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

    public function validaTelefone()
    {

        $validos = [
            '(11) 98765-4321',
            '11 98765-4321',
            '11987654321',
            '(21) 3765-4321',
            '3765-4321',
            '98765-4321',
        ];
        
        $invalidos = [
            '123456789',
            '(00) 98765-4321',
            '1198765',
            '9876543211',
        ];


        foreach ($validos as $v) {
            $this->assertTrue(DecimalUtils::validaTelefone($v));
        }
        
        foreach ($invalidos as $v) {
            $this->assertFalse(DecimalUtils::validaTelefone($v));
        }

    }


}