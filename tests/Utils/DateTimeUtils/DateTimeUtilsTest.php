<?php

namespace App\Tests\Utils\DateTimeUtils;

use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use PHPUnit\Framework\TestCase;


class DateTimeUtilsTest extends TestCase
{

    public function testPeriodos()
    {

        $testes = [
            // Mês
            [
                'dtIni' => '2019-01-01',
                'dtFim' => '2019-01-31',
                'dtIni_dec' => '2018-12-01',
                'dtFim_dec' => '2018-12-31',
                'dtIni_inc' => '2019-02-01',
                'dtFim_inc' => '2019-02-28'
            ],
            [
                'dtIni' => '2019-01-01',
                'dtFim' => '2019-06-30',
                'dtIni_dec' => '2018-07-01',
                'dtFim_dec' => '2018-12-31',
                'dtIni_inc' => '2019-07-01',
                'dtFim_inc' => '2019-12-31'
            ],
            [
                'dtIni' => '2018-12-16',
                'dtFim' => '2019-01-15',
                'dtIni_dec' => '2018-11-16',
                'dtFim_dec' => '2018-12-15',
                'dtIni_inc' => '2019-01-16',
                'dtFim_inc' => '2019-02-15'
            ],
            // Quinzena
            [
                'dtIni' => '2019-01-01',
                'dtFim' => '2019-01-15',
                'dtIni_dec' => '2018-12-16',
                'dtFim_dec' => '2018-12-31',
                'dtIni_inc' => '2019-01-16',
                'dtFim_inc' => '2019-01-31'
            ],
            [
                'dtIni' => '2018-12-16',
                'dtFim' => '2019-01-31',
                'dtIni_dec' => '2018-11-01',
                'dtFim_dec' => '2018-12-15',
                'dtIni_inc' => '2019-02-01',
                'dtFim_inc' => '2019-03-15'
            ],
            // aleatórios
            [
                'dtIni' => '2019-10-01',
                'dtFim' => '2020-02-29',
                'dtIni_dec' => '2019-05-01',
                'dtFim_dec' => '2019-09-30',
                'dtIni_inc' => '2020-03-01',
                'dtFim_inc' => '2020-07-31'
            ],
            // bimestre
            [
                'dtIni' => '2020-01-01',
                'dtFim' => '2020-02-29',
                'dtIni_dec' => '2019-11-01',
                'dtFim_dec' => '2019-12-31',
                'dtIni_inc' => '2020-03-01',
                'dtFim_inc' => '2020-04-30'
            ],
            // trimestre
            [
                'dtIni' => '2020-01-01',
                'dtFim' => '2020-03-31',
                'dtIni_dec' => '2019-10-01',
                'dtFim_dec' => '2019-12-31',
                'dtIni_inc' => '2020-04-01',
                'dtFim_inc' => '2020-06-30'
            ],
            // quadrimestre
            [
                'dtIni' => '2020-01-01',
                'dtFim' => '2020-04-30',
                'dtIni_dec' => '2019-09-01',
                'dtFim_dec' => '2019-12-31',
                'dtIni_inc' => '2020-05-01',
                'dtFim_inc' => '2020-08-31'
            ],
        ];


        foreach ($testes as $t) {

            $dtIni = DateTimeUtils::parseDateStr($t['dtIni']);
            $dtFim = DateTimeUtils::parseDateStr($t['dtFim']);

            $periodo_dec = DateTimeUtils::decPeriodoRelatorial($dtIni, $dtFim);
            $periodo_inc = DateTimeUtils::incPeriodoRelatorial($dtIni, $dtFim);

            $this->assertEquals($periodo_dec['dtIni'], $t['dtIni_dec']);
            $this->assertEquals($periodo_dec['dtFim'], $t['dtFim_dec']);

            $this->assertEquals($periodo_inc['dtIni'], $t['dtIni_inc']);
            $this->assertEquals($periodo_inc['dtFim'], $t['dtFim_inc']);

//
//
//
//            if ($periodo_dec['dtIni'] != $t['dtIni_dec']) {
//                $r .=
//                    "Dt Ini Dec deveria ser: " . $t['dtIni_dec'] . "<br />" .
//                    "Retornou como         : " . $periodo_dec['dtIni'] . "<br />";
//            }
//            if ($periodo_dec['dtFim'] != $t['dtFim_dec']) {
//                $r .=
//                    "Dt Fim Dec deveria ser: " . $t['dtFim_dec'] . "<br />" .
//                    "Retornou como         : " . $periodo_dec['dtFim'] . "<br />";
//            }
//            if ($periodo_inc['dtIni'] != $t['dtIni_inc']) {
//                $r .=
//                    "Dt Ini Inc deveria ser: " . $t['dtIni_inc'] . "<br />" .
//                    "Retornou como         : " . $periodo_inc['dtIni'] . "<br />";
//            }
//            if ($periodo_inc['dtFim'] != $t['dtFim_inc']) {
//                $r .=
//                    "Dt Fim Inc deveria ser: " . $t['dtFim_inc'] . "<br />" .
//                    "Retornou como         : " . $periodo_inc['dtFim'] . "<br />";
//            }


        }


    }

}