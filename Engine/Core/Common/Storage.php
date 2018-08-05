<?php
namespace Core\Common;

/**
 * Class Storage
 * @package Core\Common
 * Singleton
 */
class Storage
{
    private $buffer;

    private $defaultSection;

    private $section;
    private $previousSection;


    public function __construct()
    {
        $this->buffer = [];

        $this->defaultSection = '_defaultSection';
        $this->buffer[$this->defaultSection] = [];
        $this->section = $this->defaultSection;
        $this->previousSection = $this->section;
    }

    /**
     * @param bool|false $sectionName
     * @return $this
     */
    public function initLocalMemory($sectionName = false)
    {
        if ($sectionName) {
            $this->previousSection = $this->section;
            $this->section = $sectionName;
            if (!isset($this->buffer[$this->section])) {
                $this->buffer[$this->section] = [];
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function endLocalMemory()
    {
        $this->section = $this->previousSection;
        return $this;
    }

    /**
     * @param $sectionName
     * @param null $default
     * @return null
     */
    public function getLocalMemory($sectionName, $default = null)
    {
        if (isset($this->buffer[$sectionName])) {
            return $this->buffer[$sectionName];
        }

        return $default;
    }

    /**
     * @param $sectionName
     * @return $this
     */
    public function cleanLocalMemory($sectionName)
    {
        $this->buffer[$sectionName] = [];
        return $this;
    }

    /**
     * @return $this
     */
    public function initGlobalMemory()
    {
        $this->section = $this->defaultSection;
        return $this;
    }

    /**
     * @param $name
     * @param bool|false $default
     * @param bool|false $autoClean
     * @return bool | mixed
     */
    public function get($name, $default = false, $autoClean = false)
    {
        $answer = (isset($this->buffer[$this->section][$name])) ? $this->buffer[$this->section][$name]['currentData']
            : $default;

        if ($autoClean) {
            $this->cleanVariables($name);
        }

        return $answer;
    }

    /**
     * @return $this
     */
    public function cleanVariables()
    {
        $list = func_get_args();
        if (!empty($list)) {
            foreach ($list as $name) {
                unset($this->buffer[$this->section][$name]);
            }
        }

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @param bool|false $strict
     * @return $this
     */
    public function set($name, $value, $strict = false)
    {
        if (isset($this->buffer[$this->section][$name])) {
            $prevData = $this->buffer[$this->section][$name];
            $currentTimeKey = microtime(true) . 't';
            if ($strict) {
                $lastData = $prevData['previousData'];
                if (isset($lastData[$currentTimeKey])) {
                    $currentTimeKey .= '_';
                }
                $lastData[$currentTimeKey] = $prevData['currentData'];
                $dataToSave = [
                    'currentData' => $value,
                    'previousData' => $lastData,
                    'notSaved' => $prevData['notSaved']
                ];
                $this->buffer[$this->section][$name] = $dataToSave;
            } else {
                $notSaved = $prevData['notSaved'];
                if (isset($notSaved[$currentTimeKey])) {
                    $currentTimeKey .= '_';
                }
                $notSaved[$currentTimeKey] = $value;

                $dataToSave = [
                    'currentData' => $prevData['currentData'],
                    'previousData' => $prevData['previousData'],
                    'notSaved' => $notSaved
                ];

                $this->buffer[$this->section][$name] = $dataToSave;
            }
        } else {
            $dataToSave = [
                'currentData' => $value,
                'previousData' => [],
                'notSaved' => []
            ];
            $this->buffer[$this->section][$name] = $dataToSave;
        }

        return $this;
    }


    /**
     * @return array
     */
    public function showStorage()
    {
        return $this->buffer;
    }
}