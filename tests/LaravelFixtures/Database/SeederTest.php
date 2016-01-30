<?php

namespace Tests\LaravelFixtures\Database;

use Stefanius\LaravelFixtures\Database\Seeder;
use Stefanius\LaravelFixtures\Yaml\Loader;
use Tests\TestCase;

class SeederTest extends TestCase
{
    /**
     * Setup the testsuite.
     */
    public function setUp()
    {
        parent::setUp();

        $this->runDatabaseMigrations();
    }

    /**
     * @param string $table
     *
     * @dataProvider providerTestPersistenceOfTheRecords
     */
    public function testPersistenceOfTheRecords($table)
    {
        $loader = new Loader($this->formatTestApplicationPath('database/fixtures'));
        $seeder = new Seeder($loader);

        $seeder->seed($table);

        $data = $loader->loadYmlData($table);

        $this->assertArrayHasKey('settings', $data);
        $this->assertArrayHasKey('items', $data);

        foreach ($data['items'] as $item) {
            unset($item['car_brand_id']); //Just test if a record is persisted. Relations are tested separate.

            $this->seeInDatabase($table, $item);
        }
    }

    /**
     * Dataprovider for testPersistenceOfTheRecords
     *
     * @return array
     */
    public function providerTestPersistenceOfTheRecords()
    {
        return [
            ['car_brands'],
            ['car_types'],
        ];
    }
}
