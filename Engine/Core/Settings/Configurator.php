<?php

namespace Core\Settings;

use Core\Errors\Errors;

/**
 * Class Configurator
 * @package Core\Settings
 */
class Configurator
{
    private $errors;
    private $settingsPull;
    private $paths;

    public function __construct(Errors $errors)
    {
        $this->errors = $errors;
        $this->settingsPull = [];
        $this->paths = [];
    }

    /**
     * @param $folder
     * @return $this
     */
    public function readSettingsFolder($folder)
    {
        $folder = $this->slashedPath($folder);
        if ((!empty($folder)) && (is_dir($folder))) {
            $handle = opendir($folder);
            while (false !== ($name = readdir($handle))) {
                if (($name != '.') && ($name != '..')) {
                    $fullPath = $folder . $name;
                    $info = pathinfo($fullPath);
                    if ($info['extension'] == 'cfg') {
                        $configFile = $folder . $info['filename'];
                        $this->paths[$info['filename']] = $configFile;
                        $this->settingsPull[$info['filename']] = $this->loadConfigFile($configFile);
                    }
                }
            }
            closedir($handle);
        }

        return $this;
    }

    /**
     * @param $path
     * @param $name
     * @param bool $rewrite
     * @return $this
     */
    public function readSettingsFile($path, $name, $rewrite = true)
    {
        if ((!empty($name)) && (!empty($path))) {
            if (((isset($this->settingsPull[$name])) && ($rewrite)) || (!isset($this->settingsPull[$name]))) {
                $this->paths[$name] = $path;
                $this->settingsPull[$name] = $this->loadConfigFile($this->paths[$name]);
            }
        }

        return $this;
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function getSettings($name, $default = null)
    {
        if (isset($this->settingsPull[$name])) {
            return $this->settingsPull[$name];
        }

        return $default;
    }

    /**
     * @param $name
     * @param $settings
     * @return bool
     */
    public function setSettings($name, $settings)
    {
        if (!empty($name)) {
            $this->settingsPull[$name] = $settings;
            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @return bool
     */
    public function removeSettings($name)
    {
        if (!empty($name) && isset($this->settingsPull[$name])) {
            unset($this->settingsPull[$name]);
            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @param $path
     * @return bool
     */
    public function saveSettings($name, $path)
    {
        if ((!empty($path)) && (!empty($name)) && (isset($this->settingsPull[$name]))) {
            $this->saveConfigFile($path, $this->settingsPull[$name]);
            return true;
        }

        return false;
    }

    private function slashedPath($path)
    {
        $char = substr($path, -1);
        if ((strlen($path) > 0) && ($char != '/')) {
            $path .= '/';
        }

        return $path;
    }


    /**
     * @param $path
     * @return bool|mixed
     */
    private function loadConfigFile($path)
    {
        $data = null;
        $dataFilePath = $path . '.cfg';

        if (file_exists($dataFilePath)) {
            $fileData = file_get_contents($dataFilePath);
            if (!empty($fileData)) {
                $data = unserialize($fileData);
            }

        } else {
            $this->errors->error('Not found file [' . $dataFilePath . ']', 500);
        }

        return $data;
    }

    /**
     * @param $path
     * @param $data
     * @return bool
     */
    public function saveConfigFile($path, $data)
    {
        $path .= '.cfg';
        $directory = dirname($path);
        $this->initFullAccessFolders($directory);
        $information = serialize($data);

        $permissions = $this->getPermissions($directory, 3);
        if (($permissions == '666') || ($permissions == '777')) {
            file_put_contents($path, $information);
            chmod($path, 0777);

            return true;
        } else {
            $this->errors->error('Can not write config file [' . $path . '] . Wrong permissions for parent directory ' . $permissions, 502);
        }

        return false;
    }

    private function initFullAccessFolders()
    {
        $dirs = func_get_args();
        foreach ($dirs as $dir) {
            $pathName = $dir;
            $permissions = $this->getPermissions(dirname($pathName), 3, true);

            if (($permissions == '666') || ($permissions == '777')) {

                if (!file_exists($pathName)) {
                    $u = umask(0);
                    mkdir($pathName, 0777, true);
                    umask($u);
                }

                chmod($pathName, 0777);
            } else {
                $this->errors->error('Can not create a folder access denied [' . $pathName . '] . Parent directory permissions ' . $permissions, 502);
            }

        }
    }

    /**
     * @param $path
     * @param int $length
     * @param bool|false $recursive
     * @return string
     */
    private function getPermissions($path, $length = 3, $recursive = false)
    {
        $length = 0 - $length;
        if (!file_exists($path)) {

            if (!$recursive) {
                return '';
            }

            $cycle = true;
            do {
                $info = pathinfo($path);
                $path = $info['dirname'];

                if (file_exists($path)) {
                    $cycle = false;
                }

            } while ($cycle);

        }

        return substr(sprintf('%o', fileperms($path)), $length);
    }
}