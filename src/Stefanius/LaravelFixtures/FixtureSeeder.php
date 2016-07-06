<?php

namespace Stefanius\LaravelFixtures;

use Stefanius\LaravelFixtures\Database\Seeder;
use Stefanius\LaravelFixtures\Exception\PathNotFoundException;
use Stefanius\LaravelFixtures\Yaml\Loader;

class FixtureSeeder
{
    static $command = null;

    /**
     * Sets the Command parameter if you want to verbose the output.
     * @param $command
     */
    public static function SetCommand($command)
    {
        self::$command = $command;
    }

    /**
     * The fixture path (optional)
     *
     * @var string
     */
    private static $fixturePath = null;
    
    /**
     * Sets the default fixturepath.
     *
     * @param $fixturePath
     *
     * @throws PathNotFoundException
     */
    public static function SetFixturePath($fixturePath)
    {
        if (is_null($fixturePath) || !is_dir(base_path($fixturePath))) {
            throw new PathNotFoundException($fixturePath);
        }

        self::$fixturePath = $fixturePath;
    }

    /**
     * Seeds the table with fixture data
     *
     * @param string $table
     * @param string $fixturePath
     *
     * @throws \Stefanius\LaravelFixtures\Exception\PathNotFoundException
     */
    public static function Seed($table, $fixturePath = null)
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

    /**
     * Guess the default fixture path
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function FindDefaultFixturePath()
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
