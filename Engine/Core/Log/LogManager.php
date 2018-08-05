<?php

namespace Core\Log;

use Core\Errors\Errors;
use Core\Log\Adapter\AdapterInterface;
use Core\Log\Adapter\FactoryInterface;
use Core\Structure\Components\ManagerTrait;
use Core\Structure\Plugins\Collection;


/**
 * Class Logger
 * @package Core\Log
 */
class LogManager
{

    use Collection;
    use ManagerTrait;

    /**
     * @var Errors
     */
    protected $errors;


    /**
     * @var string
     */
    private $loggerCollectionName;
    /**
     * @var string
     */
    private $factoryCollectionName;

    /**
     * @param Errors $errors
     */
    public function __construct(Errors $errors)
    {
        $this->errors = $errors;
        $this->factories = [];

        $this->loggerCollectionName = 'loggerManager_loggerCollection';
        $this->factoryCollectionName = 'loggerManager_factoryCollection';
    }

    /**
     * @param $name
     * @return bool
     */
    private function buildLoggerInstance($name)
    {
        $objectSettings = $this->_SettingsGetSection($name);
        if (!isset($objectSettings['settings']) || !isset($objectSettings['type'])) {
            $this->errors->error('The settings is not defined for Logger Instance [' . $name . ']', 500);
            return false;
        }

        $factory = $this->getLoggerInstanceFactory($objectSettings['type']);
        if (is_null($factory)) {
            return false;
        }

        $cacheInstance = $factory->buildLoggerInstance($objectSettings['settings']);
        if (!$cacheInstance instanceof AdapterInterface) {
            $this->errors->error('Could not create Adapter Instance [' . $name . ']', 500);
            return false;
        }

        $this->_CollectionAddResource($name, $cacheInstance, $this->loggerCollectionName);
        return true;
    }

    /**
     * @param $type
     * @return FactoryInterface|null
     */
    private function getLoggerInstanceFactory($type)
    {
        if ($this->_CollectionCheckResource($type, $this->factoryCollectionName)) {
            return $this->_CollectionGetResource($type, $this->factoryCollectionName);
        }

        if (!is_string($type) || !isset($this->factories[$type])) {
            $this->errors->error('Unsupported factory type for new Logger Adapter [' . $type . ']', 500);
            return null;
        }

        $factoryClass = $this->factories[$type];
        if (!class_exists($factoryClass)) {
            $this->errors->error('Invalid factory instance for new Logger Adapter [' . $factoryClass . ']', 500);
            return null;
        }

        $factory = new $factoryClass();
        if (!$factory instanceof FactoryInterface) {
            $this->errors->error('Incorrect factory instance for new Logger Adapter [' . $factoryClass . ']', 500);
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
        if (!$this->_CollectionCheckResource($name, $this->loggerCollectionName)) {
            $this->buildLoggerInstance($name);
        }

        $loggerInstance = $this->_CollectionGetResource($name, $this->loggerCollectionName);
        if (!$loggerInstance instanceof AdapterInterface) {
            $this->errors->error('Try get not undefined LogAdapter [' . $name . ']', 500);
            return null;
        }

        return $loggerInstance;
    }

    /**
     * @param $name
     * @return $this
     */
    public function cleanInstance($name)
    {
        if ($this->_CollectionCheckResource($name, $this->loggerCollectionName)) {
            $this->_CollectionRemoveResource($name, $this->loggerCollectionName);
        }

        return $this;
    }


}