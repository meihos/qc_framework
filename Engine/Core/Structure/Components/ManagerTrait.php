<?php

namespace Core\Structure\Components;

use Core\Structure\Plugins\Settings;

/**
 * Trait ManagerTrait
 * @package Core\Structure\Components
 */
trait ManagerTrait
{
    use Settings;

    /** @var array */
    protected $factories;

    /**
     * @param $factories
     * @return bool
     */
    public function setFactories($factories)
    {
        if (is_array($factories) || !empty($factories)) {
            $this->factories = array_merge($this->factories, $factories);
            return true;
        }

        return false;
    }

    /**
     * @param $settings
     * @return $this
     */
    public function setSettings($settings)
    {
        $this->_SettingsInit($settings);
        return $this;
    }

    /**
     * @param $name
     * @param array $settings
     * @return $this
     */
    public function addSettings($name, array $settings)
    {
        $this->_SettingsAddSection($name, $settings);
        return $this;
    }
}