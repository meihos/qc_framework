<?php
namespace Core\Structure\Components;

use Core\Core;
use Core\Structure\Interfaces\ModuleBuilderInterface;
use Core\Structure\Plugins\Settings;

/**
 * Class AbstractModule
 * @package Core\Structure\Components
 */
abstract class AbstractModule
{
    use Settings;

    protected $configPath;
    protected $configName;
    protected $core;
    protected $builderStack;

    /**
     * @param Core $core
     */
    public function __construct(Core $core)
    {
        $this->core = $core;
        $this->builderStack = [];
        $this->configPath = null;
        $this->configName = null;
    }

    /**
     * @param array $builderStack
     * @return $this
     */
    public function setBuilderStack(array $builderStack)
    {
        $this->builderStack = $builderStack;
        return $this;
    }

    /**
     * @param $configPath
     * @return $this
     */
    public function setConfigPath($configPath)
    {
        $this->configPath = $configPath;
        return $this;
    }

    public function onLoad()
    {
        $this->loadConfig();
        $this->builderStack = $this->_SettingsGetSection('builders');
        foreach ($this->builderStack as $component => $builder) {
            if (!class_exists($builder)) {
                $this->core->getErrors()->error('Builder class not found for component [' . $component . ']');
                continue;
            }

            $builderInstance = new $builder($this->core);
            if (!$builderInstance instanceof ModuleBuilderInterface) {
                $this->core->getErrors()->error('Builder for component [' . $component . '] has incorrect implementation');
                continue;
            }

            $this->$component = $builderInstance->buildModuleComponent($this->_SettingsGetAll());
        }

        $this->buildModule();
    }

    abstract function buildModule();

    /**
     * @return bool
     */
    protected function loadConfig()
    {
        if (empty($this->configPath)) {
            return false;
        }

        $this->configName = strtolower(str_replace('\\', '_', get_class($this)));
        $this->core->getSettingsManager()->readSettingsFile($this->configPath, $this->configName, true);

        $settings = $this->core->getSettingsManager()->getSettings($this->configName, []);
        $this->_SettingsInit($settings);

        unset($settings);
    }

}