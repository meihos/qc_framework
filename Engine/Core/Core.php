<?php

namespace Core;

use Core\AutoLoad\Loader;
use Core\Cache\CacheManager;
use Core\Common\Request;
use Core\Common\Session;
use Core\Common\Storage;
use Core\Common\Tracer;
use Core\Errors\Errors;
use Core\Events\EventManager;
use Core\Guard\Guard;
use Core\Log\LogManager;
use Core\Settings\Configurator;
use Core\Sql\ConnectionManager;
use Core\Structure\Components\LibraryRepository;
use Core\Structure\Components\ModuleRepository;

/**
 * Class Core
 * @package Core
 */
class Core
{
    /** @var Request */
    public $request;
    /** @var Session */
    public $session;
    /** @var Storage */
    public $storage;
    /** @var Tracer */
    public $tracer;
    /** @var EventManager */
    public $eventManager;


    /** @var Errors */
    private $errors;
    /** @var Guard */
    private $guard;
    /** @var ConnectionManager */
    private $connectionManager;
    /** @var Configurator */
    private $settingsManager;
    /** @var LogManager */
    private $logManager;
    /** @var CacheManager */
    private $cacheManager;
    /** @var LibraryRepository */
    private $libraryRepository;
    /** @var ModuleRepository */
    private $moduleRepository;
    /** @var Loader */
    private $classLoader;

    public function __construct(Loader $loader)
    {
        $this->request = new Request();
        $this->session = new Session();
        $this->storage = new Storage();
        $this->tracer = new Tracer();
        $this->eventManager = new EventManager();

        $this->errors = new Errors($this->request);
        $this->guard = new Guard($this->request);

        $this->settingsManager = new Configurator($this->errors);
        $this->logManager = new LogManager($this->errors);
        $this->cacheManager = new CacheManager($this->errors);

        $this->libraryRepository = new LibraryRepository($this);
        $this->moduleRepository = new ModuleRepository($this);
        $this->connectionManager = new ConnectionManager($this->errors);

        $this->classLoader = $loader;
    }

    /**
     * @return Errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return Guard
     */
    public function getGuard()
    {
        return $this->guard;
    }

    /**
     * @return Configurator
     */
    public function getSettingsManager()
    {
        return $this->settingsManager;
    }

    /**
     * @return CacheManager
     */
    public function getCacheManager()
    {
        return $this->cacheManager;
    }

    /**
     * @return LogManager
     */
    public function getLogManager()
    {
        return $this->logManager;
    }

    /**
     * @return Loader
     */
    public function getClassLoader()
    {
        return $this->classLoader;
    }

    /**
     * @return ConnectionManager
     */
    public function getConnectionManager()
    {
        return $this->connectionManager;
    }

    /**
     * Return ModuleRepository, where placed registered modules
     *
     * @return ModuleRepository
     */
    public function modules()
    {
        return $this->moduleRepository;
    }

    /**
     * Return LibraryRepository, where placed registered builders for libraries
     *
     * @return LibraryRepository
     */
    public function libraries()
    {
        return $this->libraryRepository;
    }

    /**
     * @param $libraryStackConfig
     * @param $moduleStackConfig
     * @return bool
     */
    public function initComponents($libraryStackConfig, $moduleStackConfig)
    {
        $this->libraries()->loadConfig($libraryStackConfig);
        $this->modules()->loadConfig($moduleStackConfig);
        $this->getCacheManager()->setFactories($this->libraries()->cache->getFactories());
        $this->getLogManager()->setFactories($this->libraries()->log->getFactories());

        $this->eventManager->trigger('core.init.initComponents', ['core' => $this]);

        return true;
    }
} 