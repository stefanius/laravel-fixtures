<?php

namespace Stefanius\LaravelFixtures\Test;

use Stefanius\LaravelFixtures\Database\Seeder;
use Stefanius\LaravelFixtures\Test\Contracts\LaravelFixtureTesterContract;
use Stefanius\LaravelFixtures\Yaml\Loader;

class ProjectSeederTester
{
    /**
     * @var LaravelFixtureTesterContract
     */
    protected $testObject;

    /**
     * @var Loader
     */
    protected $loader;

    /**
     * ProjectSeederTester constructor.
     *
     * @param LaravelFixtureTesterContract $testObject
     * @param Loader $loader
     */
    public function __construct(LaravelFixtureTesterContract $testObject, Loader $loader)
    {
        $this->testObject = $testObject;
        $this->loader = $loader;
    }

    public function TestData($table)
    {
        $seeder = new Seeder($this->loader);
        $seeder->seed($table);

        $data = $this->loader->loadYmlData($table);

        foreach ($data['items'] as $item) {
            unset($item['car_brand_id']); //Just test if a record is persisted. Relations are tested separate.
            unset($item['pivot']); //Just test if a record is persisted. Relations are tested separate.
            
            $this->testObject->seePersistedInDatabase($table, $item);
        }
    }
}
