<?php

namespace Stefanius\LaravelFixtures\Test\Contracts;

interface LaravelFixtureTesterContract
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication();

    /**
     * @param string $table
     * @param array  $item
     */
    public function seePersistedInDatabase($table, array $item);

    /**
     * Call artisan command and return code.
     *
     * @param string  $command
     * @param array   $parameters
     *
     * @return int
     */
    public function artisan($command, $parameters = []);
}