<?php

namespace Tests\Utils\EntityIdUtils;

use CrosierSource\CrosierLibBaseBundle\Utils\EntityIdUtils\EntityIdUtils;
use PHPUnit\Framework\TestCase;

class EntityIdUtilsTest extends TestCase
{

    function dataProvider_testExtrairIdDeUri() {
        return [
            ['/api/nomeDoPacote/nomeDaEntidade/123', 123],
            ['/api/nomeDoPacote/nomeDaEntidade/123456789', 123456789],
            ['/api/nomeDoPacote/nomeDaEntidade/123456789?bla=123', 123456789],
            ['/api123456/nomeDoPacote123456789/nomeDaEntidade123456789/987654321?bla=123', 987654321],
            ['/api123456', null],
            ['/api/1', 1],
            ['/api/0', 0],
            ['/api/0aaa/a1', null],
            ['/api/0aaa/a/1', 1],
        ];
    }

    /**
     * @dataProvider dataProvider_testExtrairIdDeUri
     */
    public function testExtrairIdDeUri(string $uri, ?int $id = null): void
    {        
        $this->assertEquals($id, EntityIdUtils::extrairIdDeUri($uri));
        
    }

}