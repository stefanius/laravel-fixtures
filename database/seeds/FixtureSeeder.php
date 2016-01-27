<?php

use Illuminate\Database\Seeder;

class FixtureSeeder extends Seeder
{
    protected $table;

    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate();
        $ymlFilename = '/home/vagrant/laravel-fixtures/resources/fixtures/' . $this->table . '.yml';
        $data = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($ymlFilename));

        switch ($data) {
            case (array_key_exists('entity', $data['settings']) && !is_null($data['settings']['entity'])):
                $this->withEntity($data['settings']['entity'], $data['items']);
        }
    }

    protected function withEntity($entity, $items)
    {
        foreach ($items as $item) {
            $entity::create($item);
        }
    }

    /**
     * Truncate a set of tables
     */
    public function truncate()
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            DB::table($this->table)->truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
