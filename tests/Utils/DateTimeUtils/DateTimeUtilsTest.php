<?php

namespace Tests\Utils\DateTimeUtils;

use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeUtilsTest
 *
 * @package Tests\Utils\DateTimeUtils
 * @author Carlos Eduardo Pauluk
 */
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
            [
                'dtIni' => '2019-04-25',
                'dtFim' => '2019-04-29',
                'dtIni_dec' => '2019-04-20',
                'dtFim_dec' => '2019-04-24',
                'dtIni_inc' => '2019-04-30',
                'dtFim_inc' => '2019-05-04'
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

            $this->assertEquals($t['dtIni_dec'], $periodo_dec['dtIni'], print_r($t, true));
            $this->assertEquals($t['dtFim_dec'], $periodo_dec['dtFim'], print_r($t, true));

            $this->assertEquals($t['dtIni_inc'], $periodo_inc['dtIni'], print_r($t, true));
            $this->assertEquals($t['dtFim_inc'], $periodo_inc['dtFim'], print_r($t, true));
        }


    }

}