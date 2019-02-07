<?php

namespace App\Tests\APIClient\Base;

use CrosierSource\CrosierLibBaseBundle\APIClient\Base\DiaUtilAPIClient;
use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierAPIClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class DiaUtilAPIClientTest extends KernelTestCase
{

    public function testDiasUteis()
    {
        self::bootKernel();

        // returns the real and unchanged service container
        $container = self::$kernel->getContainer();

        // gets the special container that allows fetching private services
        $container = self::$container;

        $diaUtilAPIClient = self::$container->get(CrosierAPIClient::class);
    }

}


