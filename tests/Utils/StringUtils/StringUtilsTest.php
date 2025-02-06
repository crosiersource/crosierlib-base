<?php

namespace Tests\Utils\StringUtils;

use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeUtilsTest
 *
 * @package Tests\Utils\DateTimeUtils
 * @author Carlos Eduardo Pauluk
 */
class StringUtilsTest extends TestCase
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
            '99950-0260',
        ];


        foreach ($validos as $v) {
            $this->assertTrue(StringUtils::validaTelefone($v));
        }

        foreach ($invalidos as $v) {
            $this->assertFalse(StringUtils::validaTelefone($v));
        }

    }


}