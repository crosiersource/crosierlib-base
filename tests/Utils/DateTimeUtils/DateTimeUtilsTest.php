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

    public function testParseDateStr()
    {
        $testes = [
            '01/02/2021 14:59:59' => '01/02/2021 14:59:59',
            '2023-07-03T14:38:40.0775003Z' => '03/07/2023 14:38:40',
            '2020-05-02T10:54:44.000-04:00' => '02/05/2020 10:54:44',
            '29/11' => '29/11/' . date('Y') . ' 12:00:00',
            date('Y') . '-11' => '01/11/' . date('Y') . ' 12:00:00',
            '29/11/84' => '29/11/1984 12:00:00',
            '29/11/1984' => '29/11/1984 12:00:00',
            '29/8/1984' => '29/08/1984 12:00:00',
            '7/8/1984' => '07/08/1984 12:00:00',
            '1984-11-29' => '29/11/1984 12:00:00',
            '1984-8-29' => '29/08/1984 12:00:00',
            '1984-8-2' => '02/08/1984 12:00:00',
            '29/11/1984 12:34' => '29/11/1984 12:34:00',
            '29/11/1984 12:34:56' => '29/11/1984 12:34:56',
            '2019-04-30T18:15:02-03:00' => '30/04/2019 18:15:02',
            'Sun Aug 01 2021 21:22:23 GMT-0300 (Horário Padrão de Brasília)' => '01/08/2021 21:22:23',
        ];

        $strDateFormat = 'd/m/Y H:i:s';

        foreach ($testes as $t => $expected) {
            $date = DateTimeUtils::parseDateStr($t);
            $this->assertEquals($date->format($strDateFormat), $expected);
        }


    }


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

    public function test_getMonthsList()
    {

        $testes = [
            [
                'dtIni' => '01/01/2021',
                'dtFim' => '31/12/2021',
                'result' => [
                    '01/01/2021',
                    '01/02/2021',
                    '01/03/2021',
                    '01/04/2021',
                    '01/05/2021',
                    '01/06/2021',
                    '01/07/2021',
                    '01/08/2021',
                    '01/09/2021',
                    '01/10/2021',
                    '01/11/2021',
                    '01/12/2021',
                ]
            ],
            [
                'dtIni' => '01/10/2021',
                'dtFim' => '02/02/2022',
                'result' => [
                    '01/10/2021',
                    '01/11/2021',
                    '01/12/2021',
                    '01/01/2022',
                    '01/02/2022',
                ]
            ],
            [
                'dtIni' => '01/01/2021',
                'dtFim' => '01/01/2021',
                'result' => [
                    '01/01/2021',
                ]
            ],
            [
                'dtIni' => '02/02/2021',
                'dtFim' => '02/02/2021',
                'result' => [
                    '01/02/2021',
                ]
            ],
            [
                'dtIni' => '02/03/2021',
                'dtFim' => '02/01/2021',
                'result' => [
                    '01/03/2021',
                    '01/02/2021',
                    '01/01/2021',
                ]
            ],
            [
                'dtIni' => '31/12/2021',
                'dtFim' => '01/01/2021',
                'result' => [
                    '01/12/2021',
                    '01/11/2021',
                    '01/10/2021',
                    '01/09/2021',
                    '01/08/2021',
                    '01/07/2021',
                    '01/06/2021',
                    '01/05/2021',
                    '01/04/2021',
                    '01/03/2021',
                    '01/02/2021',
                    '01/01/2021',
                ]
            ],
        ];


        foreach ($testes as $t) {

            $dtIni = DateTimeUtils::parseDateStr($t['dtIni']);
            $dtFim = DateTimeUtils::parseDateStr($t['dtFim']);

            $list = DateTimeUtils::getMonthsList($dtIni, $dtFim);
            $r = [];
            /** @var \DateTime $mes */
            foreach ($list as $mes) {
                $r[] = $mes->format('d/m/Y');
            }
            $this->assertEquals($r, $t['result']);
        }


    }


    public function test_getWeeksList()
    {

        $testes = [
            [
                'dtIni' => '26/03/2021',
                'dtFim' => '26/03/2021',
                'result' => [
                    ['21/03/2021', '27/03/2021'],
                ]
            ],
            [
                'dtIni' => '21/03/2021',
                'dtFim' => '27/03/2021',
                'result' => [
                    ['21/03/2021', '27/03/2021'],
                ]
            ],
            [
                'dtIni' => '27/03/2021',
                'dtFim' => '21/03/2021',
                'result' => [
                    ['21/03/2021', '27/03/2021'],
                ]
            ],
            [
                'dtIni' => '18/03/2021',
                'dtFim' => '16/03/2021',
                'result' => [
                    ['14/03/2021', '20/03/2021'],
                ]
            ],
            [
                'dtIni' => '07/03/2021',
                'dtFim' => '14/03/2021',
                'result' => [
                    ['07/03/2021', '13/03/2021'],
                    ['14/03/2021', '20/03/2021'],
                ]
            ],
            [
                'dtIni' => '13/01/2021',
                'dtFim' => '01/01/2021',
                'result' => [
                    ['10/01/2021', '16/01/2021'],
                    ['03/01/2021', '09/01/2021'],
                    ['27/12/2020', '02/01/2021'],
                ]
            ],
            [
                'dtIni' => '01/01/2021',
                'dtFim' => '13/01/2021',
                'result' => [
                    ['27/12/2020', '02/01/2021'],
                    ['03/01/2021', '09/01/2021'],
                    ['10/01/2021', '16/01/2021'],
                ]
            ],
            [
                'dtIni' => '21/02/2021',
                'dtFim' => '01/03/2021',
                'result' => [
                    ['21/02/2021', '27/02/2021'],
                    ['28/02/2021', '06/03/2021'],
                ]
            ],
        ];


        foreach ($testes as $t) {
            $dtIni = DateTimeUtils::parseDateStr($t['dtIni']);
            $dtFim = DateTimeUtils::parseDateStr($t['dtFim']);

            $list = DateTimeUtils::getWeeksList($dtIni, $dtFim);
            foreach ($list as $k => $semana) {
                $this->assertEquals($semana[0]->format('d/m/Y'), $t['result'][$k][0]);
                $this->assertEquals($semana[1]->format('d/m/Y'), $t['result'][$k][1]);
            }

        }
    }


    public function test_ehAntes_ehAntesOuIgual_ehDepois_ehDepoisOuIgual()
    {
        $testes = [
            [
                'dt1' => '01/01/2021 00:00:00',
                'dt2' => '01/01/2021 00:00:00',
                'ehAntesIgnorandoHorario' => false,
                'ehAntesConsiderandoHorario' => false,
                'ehAntesOuIgualIgnorandoHorario' => true,
                'ehAntesOuIgualConsiderandoHorario' => true,
                'ehDepoisIgnorandoHorario' => false,
                'ehDepoisConsiderandoHorario' => false,
                'ehDepoisOuIgualIgnorandoHorario' => true,
                'ehDepoisOuIgualConsiderandoHorario' => true,
            ],
            [
                'dt1' => '01/01/2021 00:00:00',
                'dt2' => '01/01/2021 00:00:01',
                'ehAntesIgnorandoHorario' => false,
                'ehAntesConsiderandoHorario' => true,
                'ehAntesOuIgualIgnorandoHorario' => true,
                'ehAntesOuIgualConsiderandoHorario' => true,
                'ehDepoisIgnorandoHorario' => false,
                'ehDepoisConsiderandoHorario' => false,
                'ehDepoisOuIgualIgnorandoHorario' => true,
                'ehDepoisOuIgualConsiderandoHorario' => false,
            ],
            [
                'dt1' => '01/01/2021 00:00:00',
                'dt2' => '31/12/2020 23:59:00',
                'ehAntesIgnorandoHorario' => false,
                'ehAntesConsiderandoHorario' => false,
                'ehAntesOuIgualIgnorandoHorario' => false,
                'ehAntesOuIgualConsiderandoHorario' => false,
                'ehDepoisIgnorandoHorario' => true,
                'ehDepoisConsiderandoHorario' => true,
                'ehDepoisOuIgualIgnorandoHorario' => true,
                'ehDepoisOuIgualConsiderandoHorario' => true,
            ],
        ];

        foreach ($testes as $t) {
            $dt1 = DateTimeUtils::parseDateStr($t['dt1']);
            $dt2 = DateTimeUtils::parseDateStr($t['dt2']);

            $this->assertEquals(DateTimeUtils::ehAntes($dt1, $dt2, true), $t['ehAntesIgnorandoHorario']);
            $this->assertEquals(DateTimeUtils::ehAntes($dt1, $dt2, false), $t['ehAntesConsiderandoHorario']);

            $this->assertEquals(DateTimeUtils::ehAntesOuIgual($dt1, $dt2, true), $t['ehAntesOuIgualIgnorandoHorario']);
            $this->assertEquals(DateTimeUtils::ehAntesOuIgual($dt1, $dt2, false), $t['ehAntesOuIgualConsiderandoHorario']);

            $this->assertEquals(DateTimeUtils::ehDepois($dt1, $dt2, true), $t['ehDepoisIgnorandoHorario']);
            $this->assertEquals(DateTimeUtils::ehDepois($dt1, $dt2, false), $t['ehDepoisConsiderandoHorario']);

            $this->assertEquals(DateTimeUtils::ehDepoisOuIgual($dt1, $dt2, true), $t['ehDepoisOuIgualIgnorandoHorario']);
            $this->assertEquals(DateTimeUtils::ehDepoisOuIgual($dt1, $dt2, false), $t['ehDepoisOuIgualConsiderandoHorario']);
        }
    }


}