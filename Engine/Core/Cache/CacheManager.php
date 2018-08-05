<?php

namespace Core\Cache;

use Core\Cache\Adapter\AdapterInterface;
use Core\Cache\Adapter\FactoryInterface;
use Core\Errors\Errors;
use Core\Structure\Components\ManagerTrait;
use Core\Structure\Plugins\Collection;

/**
 * Class CacheManager
 * @package Core\Cache
 */
class CacheManager
{
    use Collection;
    use ManagerTrait;

    /** @var Errors */
    protected $errors;
    /** @var string */
    private $cacheCollectionName;
    /** @var string */
    private $factoryCollectionName;

    /**
     * @param Errors $errors
     */
    public function __construct(Errors $errors)
    {
        $this->errors = $errors;
        $this->factories = [];

        $this->cacheCollectionName = 'cacheManager_cacheCollection';
        $this->factoryCollectionName = 'cacheManager_factoryCollection';
    }

    /**
     * @param $name
     * @return bool
     */
    private function buildCacheInstance($name)
    {
        $objectSettings = $this->_SettingsGetSection($name);
        if (!isset($objectSettings['settings']) || !isset($objectSettings['type'])) {
            $this->errors->error('The settings is not defined for Cache Instance [' . $name . ']', 500);
            return false;
        }

        $factory = $this->getCacheInstanceFactory($objectSettings['type']);
        if (is_null($factory)) {
            return false;
        }

        $cacheInstance = $factory->buildCacheInstance($objectSettings['settings']);
        if (!$cacheInstance instanceof AdapterInterface) {
            $this->errors->error('Could not create Adapter Instance [' . $name . ']', 500);
            return false;
        }

        $this->_CollectionAddResource($name, $cacheInstance, $this->cacheCollectionName);
        return true;
    }

    /**
     * @param $type
     * @return FactoryInterface|null
     */
    private function getCacheInstanceFactory($type)
    {
        if ($this->_CollectionCheckResource($type, $this->factoryCollectionName)) {
            return $this->_CollectionGetResource($type, $this->factoryCollectionName);
        }

        if (!is_string($type) || !isset($this->factories[$type])) {
            $this->errors->error('Unsupported factory type for new Cache Adapter [' . $type . ']', 500);
            return null;
        }

        $factoryClass = $this->factories[$type];
        if (!class_exists($factoryClass)) {
            $this->errors->error('Invalid factory instance for new Cache Adapter [' . $factoryClass . ']', 500);
            return null;
        }

        $factory = new $factoryClass();
        if (!$factory instanceof FactoryInterface) {
            $this->errors->error('Incorrect factory instance for new Cache Adapter [' . $factoryClass . ']', 500);
            return null;
        }

        $this->_CollectionAddResource($type, $factory, $this->factoryCollectionName);
        return $factory;
    }

    /**
     * @param $name
     * @return AdapterInterface|null
     */
    public function getInstance($name)
    {
        if (!$this->_CollectionCheckResource($name, $this->cacheCollectionName)) {
            $this->buildCacheInstance($name);
        }

        $cacheInstance = $this->_CollectionGetResource($name, $this->cacheCollectionName);
        if (!$cacheInstance instanceof AdapterInterface) {
            $this->errors->error('Try get not undefined Adapter Instance [' . $name . ']', 500);
            return null;
        }

        return $cacheInstance;
    }

    /**
     * @param $name
     * @return $this
     */
    public function cleanInstance($name)
    {
        if ($this->_CollectionCheckResource($name, $this->cacheCollectionName)) {
            $this->_CollectionRemoveResource($name, $this->cacheCollectionName);
        }

        return $this;
    }
}