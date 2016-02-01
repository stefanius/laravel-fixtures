<?php

namespace Stefanius\LaravelFixtures\Test\Traits;

use Stefanius\LaravelFixtures\Test\ProjectSeederTester;
use Stefanius\LaravelFixtures\Yaml\Loader;

trait TestableFixtureTrait
{
    /**
     * @var ProjectSeederTester
     */
    protected $projectSeederTester;

    /**
     * @param string $table
     * @param array  $item
     */
    public function seePersistedInDatabase($table, array $item)
    {
        $this->seeInDatabase($table, $item);
    }

    /**
     * Setup the testsuite.
     */
    public function setUp()
    {
        parent::setUp();

        $loader = new Loader($this->getFixturePath());

        $this->projectSeederTester = new ProjectSeederTester($this, $loader);

        $this->runDatabaseMigrations();
    }

    /**
     * Run database migrations
     */
    public function runDatabaseMigrations()
    {
        $this->artisan('migrate');

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback');
        });
    }

    /**
     * @param string $table
     *
     * @dataProvider providerTestYamlFiles
     */
    public function testYamlFiles($table)
    {
        $this->projectSeederTester->TestData($table);
    }

    /**
     * @param string $table
     * @param array  $item
     * @param null   $connection
     */
    abstract function seeInDatabase($table, array $item, $connection = NULL);

    /**
     * @return string
     */
    abstract function getFixturePath();

    /**
     * @return array
     */
    abstract function providerTestYamlFiles();
}
