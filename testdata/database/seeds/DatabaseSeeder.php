<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected $tables = [
        'car_brands',
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->loadFixtures(FixtureSeeder::class, 'car_brands');
        $this->loadFixtures(FixtureSeeder::class, 'car_types');
    }

    /**
     * Seed the given connection from the given path.
     *
     * @param  string  $class
     * @return void
     */
    public function loadFixtures($class, $table)
    {
        $object = $this->resolve($class);

        $object->setTable($table);

        $object->run();

        if (isset($this->command)) {
            $this->command->getOutput()->writeln("<info>Seeded:</info> $class");
        }
    }
}
