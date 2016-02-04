<?php

namespace Tests\LaravelFixtures\Database;

use Stefanius\LaravelFixtures\Test\Contracts\LaravelFixtureTesterContract;
use Stefanius\LaravelFixtures\Test\Traits\TestableFixtureTrait;
use Tests\TestCase;

class ProjectSeederTesterTest extends TestCase implements LaravelFixtureTesterContract
{
    use TestableFixtureTrait;

    public function getFixturePath()
    {
        return $this->formatTestApplicationPath('database/fixtures');
    }

    /**
     * Dataprovider for providerTestYamlFiles
     *
     * @return array
     */
    public function providerTestYamlFiles()
    {
        return [
            ['car_brands'],
            ['car_types'],
            ['countries'],
        ];
    }
}
