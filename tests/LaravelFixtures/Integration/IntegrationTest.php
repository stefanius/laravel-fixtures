<?php

namespace Tests\LaravelFixtures\Database;

use Stefanius\LaravelFixtures\FixtureSeeder;
use Stefanius\LaravelFixtures\Yaml\Loader;
use Tests\TestCase;

class IntegrationTest extends TestCase
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
     * @dataProvider providerTestFixtureSeeder
     */
    public function testFixtureSeeder($table)
    {
        FixtureSeeder::Seed($table);

        $loader = new Loader($this->formatTestApplicationPath('database/fixtures'));
        $data = $loader->loadYmlData($table);

        $this->assertArrayHasKey('settings', $data);
        $this->assertArrayHasKey('items', $data);

        foreach ($data['items'] as $item) {
            unset($item['car_brand_id']); //Just test if a record is persisted. Relations are tested separate.

            $this->seeInDatabase($table, $item);
        }
    }

    /**
     * Dataprovider for testFixtureSeeder
     *
     * @return array
     */
    public function providerTestFixtureSeeder()
    {
        return [
            ['car_brands'],
            ['car_types'],
        ];
    }
}
