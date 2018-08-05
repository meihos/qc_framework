<?php
namespace Core\Structure\Components;

use Core\Core;
use Modules\Repository;

/**
 * Class ModuleRepository
 * @package Core\Structure\Components
 */
class ModuleRepository
{
    use Repository;

    /**
     * @var Core
     */
    protected $core;

    protected $links;

    protected $readyModules;

    /**
     * @param Core $core
     */
    public function __construct(Core $core)
    {
        $this->core = $core;
        $this->readyModules = [];
        $this->links = [];

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
        $settingsName = '_loadModuleStack';
        $moduleBuilders = $this->core->getSettingsManager()
            ->readSettingsFile($configPath, $settingsName, true)
            ->getSettings($settingsName, []);

        $this->setBuilders($moduleBuilders);
        $this->core->getSettingsManager()->removeSettings($settingsName);

        return $this;
    }

    /**
     * @param $buildersLinks
     * @return bool
     */
    public function setBuilders($buildersLinks)
    {
        if (empty($buildersLinks)) {
            return false;
        }

        $this->links = array_merge($this->links, $buildersLinks);
        foreach ($this->links as $var => $moduleClass) {
            if ((isset($this->$var)) && ($this->$var instanceof AbstractModule)) {
                continue;
            }

            $this->initModule($var, $moduleClass);
        }

        return true;
    }

    private function initModule($var, $moduleClass)
    {
        if ((isset($this->$var)) && ($this->$var instanceof AbstractModule)) {
            return true;
        }

        if (!class_exists($moduleClass)) {
            $this->core->getErrors()->error('Not found Module class [' . $moduleClass . ']');
            return false;
        }

        $moduleEntity = new $moduleClass($this->core);
        if (!$moduleEntity instanceof AbstractModule) {
            $this->core->getErrors()->error('Incorrect Module class [' . $moduleClass . '] - not instance of AbstractModule');
            unset($moduleEntity);
            return false;
        }

        $this->$var = $moduleEntity;
        $this->readyModules[] = $var;

        return true;
    }


    /**
     * @param $moduleName
     * @return AbstractModule|null
     */
    public function __get($moduleName)
    {
        $this->core->getErrors()->error('Try get not initialize module [' . $moduleName . ']');
        return null;
    }


    public function loadModules()
    {
        foreach ($this->readyModules as $moduleName) {
            if (!$this->$moduleName instanceof AbstractModule) {
                continue;
            }

            $this->$moduleName->onLoad();
        }
    }
} 