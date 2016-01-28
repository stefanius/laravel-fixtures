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

        $data = $this->loadYmlData($this->table);

        switch ($data) {
            case (array_key_exists('entity', $data['settings']) && !is_null($data['settings']['entity'])):
                $this->withEntity($data['settings'], $data['items']);
        }
    }

    protected function withEntity($settings, $items)
    {
        $entity = $settings['entity'];
        $fk = false;

        if (array_key_exists('foreign_key', $settings)) {
            $fk = $settings['foreign_key'];
        }

        foreach ($items as $item) {
            if($fk && array_key_exists($fk, $item)) {
                $item[$fk] = $this->findRelationId($item[$fk]);
            }

            $entity::create($item);
        }
    }

    protected function loadYmlData($file)
    {
        $ymlFilename = '/home/vagrant/laravel-fixtures/resources/fixtures/' . $file . '.yml';

        return \Symfony\Component\Yaml\Yaml::parse(file_get_contents($ymlFilename));
    }

    protected function findRelationId($key)
    {
        if (strpos($key, '@') === false) {
            //trow error
        }

        $split = explode('@', $key);
        $data = $this->loadYmlData($split[0]);

        return $data['items'][$split[1]]['id'];
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
