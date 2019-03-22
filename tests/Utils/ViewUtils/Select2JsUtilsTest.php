<?php


namespace Tests\Utils\ViewUtils;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\Group;
use CrosierSource\CrosierLibBaseBundle\Utils\ViewUtils\Select2JsUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class Select2JsUtilsTest
 *
 * @package Tests\Utils\ViewUtils
 * @author Carlos Eduardo Pauluk
 */
class Select2JsUtilsTest extends TestCase
{

    public function test_toSelect2DataFn()
    {
        $array = [];

        $group = new Group();
        $group->setId(1)
            ->setGroupname('GRUPO UM')
            ->setInserted(\DateTime::createFromFormat('Y-m-d', '2001-01-01'));
        $array[] = $group;

        $select2Data = Select2JsUtils::toSelect2DataFn($array, function ($e) {
            /** @var Group $e */
            return $e->getGroupname() . ' (' . $e->getInserted()->format('d/m/Y') . ')';
        });

        $this->assertEquals(1, count($array));

        $data = $select2Data[0];
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('text', $data);
        $this->assertEquals('GRUPO UM (01/01/2001)', $data['text']);



    }

    public function test_toSelect2Data()
    {
        $array = [];

        $group = new Group();
        $group->setId(11)
            ->setGroupname('GRUPO UMMMM')
            ->setInserted(\DateTime::createFromFormat('Y-m-d', '2001-01-01'));
        $array[] = $group;

        $select2Data = Select2JsUtils::toSelect2Data($array, '%s --(%s)--', ['groupname', 'id']);

        $this->assertEquals(1, count($array));

        $data = $select2Data[0];
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('text', $data);
        $this->assertEquals('GRUPO UMMMM --(11)--', $data['text']);



    }

}