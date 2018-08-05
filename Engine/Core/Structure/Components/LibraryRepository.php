<?php

namespace Core\Structure\Components;

use Core\Core;
use Libraries\Repository;

/**
 * Class LibraryRepository
 * @package Core\Structure\Components
 */
class LibraryRepository
{
    use Repository;

    /**
     * @var Core
     */
    protected $core;

    protected $factories = [];

    /**
     * @param Core $core
     */
    public function __construct(Core $core)
    {
        $this->core = $core;

        $vars = get_class_vars(Repository::class);
        foreach ($vars as $varName => $var) {
            unset($this->$varName);
        }
    }

    /**
     * @param $configPath
     * @return $this
     */
    public function loadConfig($configPath)
    {
        $settingsName = '_loadLibraryStack';
        $libraryFactories = $this->core->getSettingsManager()
            ->readSettingsFile($configPath, $settingsName, true)
            ->getSettings($settingsName, []);

        $this->setAbstractFactories($libraryFactories);
        $this->core->getSettingsManager()->removeSettings($settingsName);

        return $this;
    }

    /**
     * @param $abstractFactoriesClasses
     * @return bool
     */
    public function setAbstractFactories(array $abstractFactoriesClasses)
    {
        if (empty($abstractFactoriesClasses) || !is_array($abstractFactoriesClasses)) {
            return false;
        }

        $this->factories = array_merge($this->factories, $abstractFactoriesClasses);
        foreach ($this->factories as $var => $factoryClass) {
            if ((isset($this->$var)) && ($this->$var instanceof AbstractLibraryFactory)) {
                continue;
            }

            $this->initAbstractFactory($var, $factoryClass);
        }

        return true;
    }

    private function initAbstractFactory($var, $factoryClass)
    {
        if ((isset($this->$var)) && ($this->$var instanceof AbstractLibraryFactory)) {
            return true;
        }

        if (!class_exists($factoryClass)) {
            $this->core->getErrors()->error('Not found Abstract Factory class [' . $factoryClass . ']');
            return false;
        }

        $factory = new $factoryClass($this->core);
        if (!$factory instanceof AbstractLibraryFactory) {
            $this->core->getErrors()->error('Incorrect Abstract Factory class [' . $factoryClass . '] - not instance of AbstractLibraryFactory');
            unset($factory);
            return false;
        }

        $this->$var = $factory;
        return true;
    }

    /**
     * @param $factoryName
     * @return null
     */
    public function __get($factoryName)
    {
        $this->core->getErrors()->error('Try get not initialize library [' . $factoryName . ']');
        return null;
    }


} 