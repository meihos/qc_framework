<?php

namespace Core\Structure\Components;

use Core\Core;
use Core\Structure\Interfaces\LibraryFactoryInterface;
use Core\Structure\Plugins\Collection;
use Core\Structure\Plugins\Settings;

/**
 * Class LibraryBuilder
 * @package Core\Structure\Components
 */
abstract class AbstractLibraryFactory
{
    use Settings;
    use Collection;

    protected $core;
    protected $collectionName;
    protected $factories;

    public function __construct(Core $core)
    {
        $this->core = $core;
        $this->factories = [];
        $this->configAutoBuilding();
    }

    abstract protected function configAutoBuilding();


    /**
     * @return array
     */
    public function getFactories()
    {
        return $this->factories;
    }

    /**
     * @param $type
     * @param array $settings
     * @return mixed
     */
    public function buildByType($type, array $settings)
    {
        $type = strtolower($type);
        if (!isset($this->factories[$type])) {
            $this->core->getErrors()->error('Incorrect type [' . $type . '] of factory at Abstract factory [' . get_class($this) . ']', 500);
            return null;
        }

        $factoryClass = $this->factories[$type];
        if (!class_exists($factoryClass)) {
            $this->core->getErrors()->error('Invalid factory instance [' . $factoryClass . ']', 500);
            return null;
        }

        $factory = new $factoryClass();
        if (!$factory instanceof LibraryFactoryInterface) {
            $this->core->getErrors()->error('Unsupported factory registered at Library Factory config');
            return null;
        }

        $instance = $factory->buildLibraryInstance($settings);
        unset($factory);

        return $instance;
    }
} 