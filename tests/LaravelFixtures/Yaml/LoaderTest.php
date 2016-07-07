<?php

namespace Tests\LaravelFixtures\Yaml;

use Stefanius\LaravelFixtures\Yaml\Loader;
use Tests\TestCase;

class LoaderTest extends TestCase
{
    /**
     * @expectedException \Stefanius\LaravelFixtures\Exception\PathNotFoundException
     */
    public function testConstructorWithNonExistingPath()
    {
        new Loader('/this/path/does/not/exist/x/y/z');
    }

    /**
     * @expectedException \Stefanius\LaravelFixtures\Exception\FileNotFoundException
     */
    public function testConstructorWithExistingPathButNonExistingFile()
    {
        $loader = new Loader($this->formatTestApplicationPath('database/fixtures'));

        $loader->loadYmlData('does_not_exist');
    }

    /**
     * @param string  $filename
     * @param integer $numberOfItems
     * @param array   $expectedLastItem
     *
     * @dataProvider providerTestFixtureData
     */
    public function testFixtureData($filename, $numberOfItems, $expectedLastItem)
    {
        $loader = new Loader($this->formatTestApplicationPath('database/fixtures'));

        $data = $loader->loadYmlData($filename);

        $this->assertArrayHasKey('settings', $data);

        $this->assertEquals(count($data['items']), $numberOfItems);

        $lastItem = array_pop($data['items']);
        unset($lastItem['pivot']);

        $this->assertEquals($lastItem, $expectedLastItem);
    }

    /**
     * Dataprovider for testFixtureData
     *
     * @return array
     */
    public function providerTestFixtureData()
    {
        return [
            ['car_brands', 4, ['id' => 4, 'name' => 'Peugeot']],
            ['car_types', 6, ['name' => 'Kadett', 'car_brand_id' => 'car_brands@car_1']],
            ['countries', 4, ['id' => 4, 'name' => 'Undefined']],
            ['customers', 5, ['id' => 5, 'name' => 'Dewey Doe']],
            ['magazines', 6, ['id' => 6, 'name' => 'Read that']],
        ];
    }
}
