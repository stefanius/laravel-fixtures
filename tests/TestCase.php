<?php

namespace Tests;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require $this->formatTestApplicationPath('bootstrap/app.php');

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
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
     * Get the path of the root of the testsuite.
     *
     * @return string
     */
    protected function getTestsRootPath()
    {
        return dirname(__FILE__);
    }

    /**
     * Get the project root-path.
     *
     * @return string
     */
    protected function getProjectRootPath()
    {
        return dirname($this->getTestsRootPath());
    }

    /**
     * Get the path of the testapplication.
     *
     * @return string
     */
    protected function getTestApplicationPath()
    {
        return $this->getProjectRootPath() . '/testdata';
    }

    /**
     * Format and return a directory within the testapplication structure.
     *
     * @param string $path
     *
     * @return string
     */
    protected function formatTestApplicationPath($path = null)
    {
        if (is_null($path)) {
            return $this->getTestApplicationPath();
        }

        return sprintf('%s/%s', $this->getTestApplicationPath(), $path);
    }
}
