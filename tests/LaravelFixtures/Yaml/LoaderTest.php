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
        $loader = new Loader('/this/path/does/not/exist/x/y/z');
    }

    /**
     * @expectedException \Stefanius\LaravelFixtures\Exception\FileNotFoundException
     */
    public function testConstructorWithExistingPathButNonExistingFile()
    {
        $loader = new Loader('/home/vagrant/laravel-fixtures/testdata/database/fixtures');

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
        $loader = new Loader('/home/vagrant/laravel-fixtures/testdata/database/fixtures');

        $data = $loader->loadYmlData($filename);

        $this->assertArrayHasKey('settings', $data);
        $this->assertArrayHasKey('items', $data);

        $this->assertEquals(count($data['items']), $numberOfItems);
        $this->assertEquals(array_pop($data['items']), $expectedLastItem);
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
        ];
    }
}