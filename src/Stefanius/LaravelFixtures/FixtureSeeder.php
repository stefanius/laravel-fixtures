<?php

namespace Stefanius\LaravelFixtures;

use Stefanius\LaravelFixtures\Database\Seeder;
use Stefanius\LaravelFixtures\Exception\PathNotFoundException;
use Stefanius\LaravelFixtures\Yaml\Loader;

class FixtureSeeder
{
    /**
     * The Laravel console command
     */
    static $command = null;

    /**
     * The fixture path (optional)
     *
     * @var string
     */
    static $fixturePath = null;

    /**
     * Sets the Command parameter if you want to verbose the output.
     *
     * @param $command
     */
    static function SetCommand($command)
    {
        self::$command = $command;
    }

    /**
     * Sets the default fixturepath.
     *
     * @param $fixturePath
     *
     * @throws PathNotFoundException
     */
    static function SetFixturePath($fixturePath)
    {
        if (is_null($fixturePath) || !is_dir(base_path($fixturePath))) {
            throw new PathNotFoundException($fixturePath);
        }

        self::$fixturePath = $fixturePath;
    }

    /**
     * @param string $table
     * @param string $fixturePath
     *
     * @throws \Stefanius\LaravelFixtures\Exception\PathNotFoundException
     */
    static function Seed($table, $fixturePath = null)
    {
        if (is_null($table) || !is_string($table)) {
            throw new \InvalidArgumentException('The $table argument has to be a string and may not be NULL.');
        }

        if (!is_null($fixturePath) && !is_dir(base_path($fixturePath))) {
            throw new PathNotFoundException($fixturePath);
        }

        if (is_null($fixturePath) && !is_null(self::$fixturePath)) {
            $fixturePath = self::$fixturePath;
        }

        if (is_null($fixturePath)) {
            $fixturePath = self::FindDefaultFixturePath();
        }

        $loader = new Loader($fixturePath);
        $seeder = new Seeder($loader, self::$command);

        $seeder->seed($table);
    }

    static function FindDefaultFixturePath()
    {
        $paths = [
            'database/fixtures',
            'resources/fixtures'
        ];

        foreach ($paths as $path) {
            if (is_dir(base_path($path))) {
                return base_path($path);
            }
        }

        throw new \Exception('The default fixture paths does not exists.');
    }
}