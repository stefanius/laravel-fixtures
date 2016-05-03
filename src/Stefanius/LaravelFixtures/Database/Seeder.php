<?php

namespace Stefanius\LaravelFixtures\Database;

use Illuminate\Console\Command;
use Stefanius\LaravelFixtures\Yaml\Loader;

class Seeder
{
    /**
     * @var Loader
     */
    protected $yamlLoader;

    /**
     * The console command instance.
     *
     * @var \Illuminate\Console\Command
     */
    protected $command;

    /**
     * Seeder constructor.
     *
     * @param Loader  $yamlLoader
     * @param Command $command
     */
    public function __construct(Loader $yamlLoader, Command $command = null)
    {
        $this->yamlLoader = $yamlLoader;
        $this->command    = $command;
    }

    /**
     * Seed the fixtures.
     */
    public function seed($table)
    {
        //$this->truncate($table);

        $data = $this->yamlLoader->loadYmlData($table);

        if ($this->command) {
            $this->command->getOutput()->writeln("<info>Seeded Fixture:</info> $table");
        }

        switch ($data) {
            case (array_key_exists('entity', $data['settings']) && !is_null($data['settings']['entity'])):
                $this->withEntity($data['settings'], $data['items']);
                break;
            case (array_key_exists('factory', $data['settings']) && !is_null($data['settings']['factory'])):
                $this->withFactory($data['settings'], $data['items']);
                break;
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

            $object = $entity::create($this->clean($item));

            $this->seedPivots($object, $item, $settings);
        }
    }

    /**
     * @param $settings
     * @param $items
     */
    protected function withFactory($settings, $items)
    {
        $entity = $settings['factory'];
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

            $object = factory($entity)->create($this->clean($item));

            $this->seedPivots($object, $item, $settings);
        }
    }

    /**
     * @param $object
     */
    protected function seedPivots($object, $item, $settings)
    {
        if (!array_key_exists('pivot', $item)) {
            return;
        }

        foreach ($item['pivot'] as $key => $value) {
            foreach ($value as $related) {
                $exploded = explode('@', $related);
                
                $relatedData = $this->findRelation($related);

                $relatedClass = $settings['pivot'][$key];

                $relatedObject = $relatedClass::find($relatedData['id']);

                if ($relatedObject) {
                    $object->{$exploded[0]}()->save($relatedObject);
                }
            }
        }
    }

    /**
     * Quick and very dirty. Will fix this before 2016-08-01
     *
     * @param $item
     *
     * @return mixed
     */
    protected function clean($item)
    {
        $unset = [
            'pivot'
        ];

        foreach ($unset as $key) {
            unset($item[$key]);
        }

        return $item;
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
