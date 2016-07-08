<?php

namespace Stefanius\LaravelFixtures\Yaml;

use Stefanius\LaravelFixtures\Exception\FileNotFoundException;
use Stefanius\LaravelFixtures\Exception\PathNotFoundException;
use Symfony\Component\Yaml\Yaml;

class Loader
{
    protected $fixtureDataPath;

    /**
     * Loader constructor.
     *
     * @param $fixtureDataPath
     *
     * @throws PathNotFoundException
     */
    public function __construct($fixtureDataPath)
    {
        if (!is_dir($fixtureDataPath)) {
            throw new PathNotFoundException($fixtureDataPath);
        }

        $this->fixtureDataPath = rtrim($fixtureDataPath, DIRECTORY_SEPARATOR);
    }

    /**
     * @param $file
     *
     * @return array
     *
     * @throws \Stefanius\LaravelFixtures\Exception\FileNotFoundException
     */
    public function loadYmlData($file)
    {
        if (is_null($file)) {
            throw new \InvalidArgumentException(sprintf('The argument has to be a string. NULL given.'));
        }

        if (!is_string($file)) {
            throw new \InvalidArgumentException(sprintf('The argument has to be a string. %s given.', typeOf($file)));
        }

        $ymlFilename = sprintf($this->fixtureDataPath . DIRECTORY_SEPARATOR . '%s.yml', $file);

        if (!file_exists($ymlFilename)) {
            throw new FileNotFoundException($ymlFilename);
        }

        return Yaml::parse(file_get_contents($ymlFilename));
    }
}
