<?php

namespace Stefanius\LaravelFixtures\Database;

use Stefanius\LaravelFixtures\Yaml\Loader;

class Seeder
{
    /**
     * @var Loader
     */
    protected $yamlLoader;

    /**
     * Seeder constructor.
     *
     * @param Loader $yamlLoader
     */
    public function __construct(Loader $yamlLoader)
    {
        $this->yamlLoader = $yamlLoader;
    }

    /**
     * Seed the fixtures.
     */
    public function seed($table)
    {
        //$this->truncate($table);

        $data = $this->yamlLoader->loadYmlData($table);

        switch ($data) {
            case (array_key_exists('entity', $data['settings']) && !is_null($data['settings']['entity'])):
                $this->withEntity($data['settings'], $data['items']);
        }
    }

    /**
     * @param $settings
     * @param $items
     */
    protected function withEntity($settings, $items)
    {
        $entity = $settings['entity'];
        $fk = false;

        if (array_key_exists('foreign_key', $settings)) {
            $fk = $settings['foreign_key'];
        }

        foreach ($items as $item) {
            if($fk && is_array($fk)) {
                foreach ($fk as $foreign => $primary) {
                    if (array_key_exists($foreign, $item)) {
                        $item[$foreign] = $this->findRelation($item[$foreign])[$primary];
                    }
                }
            }

            $entity::create($item);
        }
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    protected function findRelation($key)
    {
        if (strpos($key, '@') === false) {
            //trow error
        }

        $split = explode('@', $key);
        $data = $this->yamlLoader->loadYmlData($split[0]);

        return $data['items'][$split[1]];
    }

    /**
     * Truncate a set of tables
     */
    public function truncate($table)
    {
        \DB::table($table)->truncate();
    }
}
