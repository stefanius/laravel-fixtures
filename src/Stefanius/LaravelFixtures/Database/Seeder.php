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
        $data = $this->yamlLoader->loadYmlData($table);

        if ($this->command) {
            $this->command->getOutput()->writeln("<info>Seeded Fixture:</info> $table");
        }

        $this->process($data['settings'], $data['items']);
    }

    /**
     * @param $settings
     * @param $items
     *
     * @throws \Exception
     */
    protected function process($settings, $items)
    {
        $fk = false;

        if (array_key_exists('foreign_key', $settings)) {
            $fk = $settings['foreign_key'];
        }

        foreach ($items as $item) {
            if ($fk && is_array($fk)) {
                foreach ($fk as $foreign => $primary) {
                    if (array_key_exists($foreign, $item)) {
                        $item[$foreign] = $this->findRelation($item[$foreign])[$primary];
                    }
                }
            }

            $item = $this->calculateRelativeDateTime($item, $settings);

            if (array_key_exists('entity', $settings) && !is_null($settings['entity'])) {
                $object = $this->withEntity($settings['entity'], $item);
            } elseif (array_key_exists('factory', $settings) && !is_null($settings['factory'])) {
                $object = $this->withFactory($settings['factory'], $item);
            } else {
                throw new \Exception('YML files should be bound to either a "entity" or an "factory"');
            }

            $this->seedPivots($object, $item, $settings);
        }
    }

    /**
     * @param $entity
     * @param $item
     *
     * @return mixed
     */
    protected function withEntity($entity, array $item)
    {
         return $entity::create($item);
    }

    /**
     * @param $entity
     * @param $item
     *
     * @return mixed
     */
    protected function withFactory($entity, array $item)
    {
        return factory($entity)->create($item);
    }

    /**
     * Seed ManyToMany related objects
     *
     * @param $object
     * @param $item
     * @param $settings
     */
    protected function seedPivots($object, $item, $settings)
    {
        if (!array_key_exists('pivot', $item)) {
            return;
        }

        foreach ($item['pivot'] as $method => $listItems) {
            foreach ($listItems as $listItem) {
                $relatedData = $this->findRelation($listItem);

                $relatedClass = $settings['pivot'][$method];

                $relatedObject = $relatedClass::find($relatedData['id']);

                if ($relatedObject) {
                    $object->{$method}()->save($relatedObject);
                }
            }
        }
    }

    /**
     * @param array $item
     * @param array $settings
     *
     * @return array
     */
    protected function calculateRelativeDateTime(array $item, array $settings)
    {
        if (!array_key_exists('datetime', $settings)) {
            return $item;
        }

        foreach ($settings['datetime'] as $datetime) {
            if (substr($item[$datetime], 0, 1) === '+' || substr($item[$datetime], 0, 1) === '-') {
                $now = new \DateTime('now');

                $item[$datetime] = $now->modify($item[$datetime])->format('Y-m-d');
            }
        }

        return $item;
    }

    /**
     * @param $key
     *
     * @return string
     *
     * @throws \Exception
     * @throws \Stefanius\LaravelFixtures\Exception\FileNotFoundException
     */
    protected function findRelation($key)
    {
        if (strpos($key, '@') === false) {
            throw new \Exception(sprintf("The key to the relation has to formed like 'ymlfile@itemkey', the value '%s' is incorrect.", $key));
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
