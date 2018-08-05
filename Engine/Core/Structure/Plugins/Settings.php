<?php
namespace Core\Structure\Plugins;

/**
 * Class Settings
 * @package Core\Structure\Plugins
 */
trait Settings
{
    protected $__settingsForThisClass;

    /**
     * @param $settings
     * @return $this
     */
    protected function _SettingsInit($settings)
    {
        $this->__settingsForThisClass = (is_array($settings)) ? $settings : [];
        return $this;
    }

    protected function _SettingsAddSection($name, $settings)
    {

        if (!is_array($this->__settingsForThisClass)) {
            $this->__settingsForThisClass = [];
        }

        $this->__settingsForThisClass[$name] = (is_array($settings)) ? $settings : [];
        return $this;
    }

    /**
     * @param $sectionName
     * @return array
     */
    protected function _SettingsGetSection($sectionName)
    {
        if ((isset($this->__settingsForThisClass[$sectionName])) && (is_array($this->__settingsForThisClass[$sectionName]))) {
            return $this->__settingsForThisClass[$sectionName];
        }

        return [];
    }

    /**
     * @param $name
     * @param bool $default
     * @return mixed
     */
    protected function  _SettingsGetRow($name, $default = false)
    {
        if (isset($this->__settingsForThisClass[$name])) {
            $default = $this->__settingsForThisClass[$name];
        }

        return new $default;
    }

    /**
     * @param $name
     * @param $value
     */
    protected function _SettingsSetRow($name, $value)
    {
        $this->__settingsForThisClass[$name] = $value;
    }

    /**
     * @return mixed
     */
    protected function _SettingsGetAll()
    {
        return $this->__settingsForThisClass;
    }

    /**
     * @return $this
     */
    protected function _SettingsClear()
    {
        $this->__settingsForThisClass = [];
        return $this;
    }
}