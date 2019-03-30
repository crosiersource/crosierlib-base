<?php

namespace Tests\APIClient\Base;

use CrosierSource\CrosierLibBaseBundle\APIClient\Base\DiaUtilAPIClient;
use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class DiaUtilAPIClientTest
 *
 * @package Tests\APIClient\Base
 * @author Carlos Eduardo Pauluk
 */
class DiaUtilAPIClientTest extends KernelTestCase
{


    public function testDiasUteis()
    {
        self::bootKernel();
        /** @var DiaUtilAPIClient $diaUtilAPIClient */
        $diaUtilAPIClient = self::$container->get('test.CrosierSource\CrosierLibBaseBundle\APIClient\Base\DiaUtilAPIClient');

        $this->assertInstanceOf(DiaUtilAPIClient::class, $diaUtilAPIClient);



        $du = $diaUtilAPIClient->findDiaUtil(DateTimeUtils::parseDateStr('25/12/2019'), true, true);
        $prox = DateTimeUtils::parseDateStr('26/12/2019');
        $this->assertEquals($du->format('d/M/Y'), $prox->format('d/M/Y'));
    }




}


