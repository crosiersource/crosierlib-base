<?php


namespace Tests\Utils\ViewUtils;

use CrosierSource\CrosierLibBaseBundle\Entity\Security\Group;
use CrosierSource\CrosierLibBaseBundle\Utils\ViewUtils\ChoiceTypeUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class ChoiceTypeUtilsTest
 *
 * @package Tests\Utils\ViewUtils
 * @author Carlos Eduardo Pauluk
 */
class ChoiceTypeUtilsTest extends TestCase
{

    public function test_toChoiceTypeChoicesFn()
    {
        $array = [];

        $group = new Group();
        $group->setId(1)
            ->setGroupname('GRUPO UM')
            ->setInserted(\DateTime::createFromFormat('Y-m-d', '2001-01-01'));
        $array[] = $group;

        $choices = ChoiceTypeUtils::toChoiceTypeChoicesFn($array, function ($e) {
            /** @var Group $e */
            return $e->getGroupname() . ' (' . $e->getInserted()->format('d/m/Y') . ')';
        });

        $this->assertEquals(1, count($array));

        $this->assertArrayHasKey('GRUPO UM (01/01/2001)', $choices);
        $this->assertEquals($choices['GRUPO UM (01/01/2001)'], 1);



    }

    public function test_toChoiceTypeChoices()
    {
        $array = [];

        $group = new Group();
        $group->setId(11)
            ->setGroupname('GRUPO UMMMM')
            ->setInserted(\DateTime::createFromFormat('Y-m-d', '2001-01-01'));
        $array[] = $group;

        $group = new Group();
        $group->setId(22)
            ->setGroupname('GRUPO DDOOIISS')
            ->setInserted(\DateTime::createFromFormat('Y-m-d', '2002-02-02'));
        $array[] = $group;



        $choices = ChoiceTypeUtils::toChoiceTypeChoices($array, '%s --(%s)--', ['groupname', 'id']);

        $this->assertEquals(2, count($array));

        $this->assertArrayHasKey('GRUPO UMMMM --(11)--', $choices);
        $this->assertEquals($choices['GRUPO UMMMM --(11)--'], 11);


        $this->assertArrayHasKey('GRUPO DDOOIISS --(22)--', $choices);
        $this->assertEquals($choices['GRUPO DDOOIISS --(22)--'], 22);



    }

}