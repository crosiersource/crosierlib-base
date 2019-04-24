<?php

namespace Tests\APIClient\Base;

use CrosierSource\CrosierLibBaseBundle\APIClient\Base\PropAPIClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class PropAPIClientTest
 *
 * @package Tests\APIClient\Base
 * @author Carlos Eduardo Pauluk
 */
class PropAPIClientTest extends KernelTestCase
{


    public function test()
    {
        self::bootKernel();
        /** @var PropAPIClient $propAPIClient */
        $propAPIClient = self::$container->get('test.CrosierSource\CrosierLibBaseBundle\APIClient\Base\PropAPIClient');

        $this->assertInstanceOf(PropAPIClient::class, $propAPIClient);


        $r = $propAPIClient->findByNome('GRADES');
        $this->assertEquals($r['UUID'], '1803b5bc-b3d4-4177-9134-4e3282258398');

        $r = $propAPIClient->findTamanhosByGradeId(1);
        $this->assertCount(7, $r);

        $r = $propAPIClient->findTamanhosByGradeIdAndOrdem(1, 2);
        $this->assertEquals('P', $r['tamanho']);

        $r = $propAPIClient->findGrades();
        print_r($r);

    }


}


