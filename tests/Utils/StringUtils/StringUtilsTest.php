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

    public function test_validaTelefone()
    {

        $validos = [
            '(11) 98765-4321',
            '11 98765-4321',
            '11987654321',
            '(21) 3765-4321',
            '423765-4321',
            '4198765-4321',
            '99950-02600', // seria (99) 9500-2600
        ];

        $invalidos = [
            '123456789',
            '(00) 98765-4321',
            '1198765',
            '2032277785', // ddd inválido
        ];


        foreach ($validos as $v) {
            echo "Validando válido: " . $v . "\n";
            $this->assertTrue(StringUtils::validaTelefone($v));
        }

        foreach ($invalidos as $v) {
            echo "Validando inválido: " . $v . "\n";
            $this->assertFalse(StringUtils::validaTelefone($v));
        }

    }


}
