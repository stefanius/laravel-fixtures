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

        $this->seePersistenceWithoutRelations($table, $data['items'], $data['settings']);
        $this->seePersistenceWithRelations($table, $data['items'], $data['settings']);
    }

    /**
     * @param $key
     *
     * @return mixed
     *
     * @throws \Stefanius\LaravelFixtures\Exception\FileNotFoundException
     */
    private function loadRelatedObjectData($key)
    {
        if (strpos($key, '@') === false) {
            return null;
        }

        $split = explode('@', $key);

        $loader = new Loader($this->formatTestApplicationPath('database/fixtures'));

        $data = $loader->loadYmlData($split[0]);

        return $data['items'][$split[1]];
    }

    /**
     * @param $table
     * @param $items
     * @param $settings
     */
    private function seePersistenceWithoutRelations($table, $items, $settings)
    {
        foreach ($items as $item) {
            $withoutRelations = $this->stripRelationFromItem($settings, $item);

            $this->seeInDatabase($table, $withoutRelations);
        }
    }

    /**
     * @param $table
     * @param $items
     * @param $settings
     */
    private function seePersistenceWithRelations($table, $items, $settings)
    {
        if (!array_key_exists('foreign_key', $settings)) {
            return;
        }

        foreach ($items as $item) {
            $relatedData = [];

            $object = $this->loadFromDatabase($table, $this->stripRelationFromItem($settings, $item));

            foreach ($item as $key => $value) {
                $relatedData[$key] = $this->loadRelatedObjectData($value);
            }

            foreach ($settings['foreign_key'] as $key => $value) {
                $this->assertEquals($relatedData[$key]['id'], $object->$key);
            }
        }
    }

    /**
     * Assert that a given where condition exists in the database.
     *
     * @param  string  $table
     * @param  array  $data
     *
     * @return $this
     */
    protected function loadFromDatabase($table, array $data)
    {
        $database = $this->app->make('db');

        return $database->table($table)->where($data)->first();
    }

    /**
     * @param $settings
     * @param $item
     *
     * @return array
     */
    protected function stripRelationFromItem($settings, $item)
    {
        unset($item['pivot']);

        if (!array_key_exists('foreign_key', $settings) || !is_array($settings['foreign_key'])) {
            return $item;
        }

        foreach ($settings['foreign_key'] as $key => $value) {
            unset($item[$key]);
        }

        return $item;
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
            ['magazines'],
            ['customers'],
            ['countries'],
        ];
    }
}
