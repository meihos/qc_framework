<?php

namespace Libraries\HashTable\File;

use Core\Structure\Interfaces\HashTableAdapterInterface;

/**
 * Class HashTableAdapter
 * @package Libraries\HashTable\File
 */
class Adapter implements HashTableAdapterInterface
{
    private $cachePath;
    private $source;
    private $data;

    /**
     * @param $cachePath
     */
    public function __construct($cachePath)
    {
        $this->cachePath = $cachePath;
    }

    private function updateSource()
    {
        if (file_exists($this->cachePath)) {
            chmod($this->cachePath, 0777);
        } else {
            mkdir($this->cachePath, 0777, true);
        }

        $path = $this->cachePath . $this->source;
        $information = json_encode($this->data);
        file_put_contents($path, $information);
    }

    public function loadSource($source, $prefix)
    {
        $this->source = ($prefix) ? $prefix : '';
        if (strlen($this->source) > 0) {
            $this->source .= '_';
        }
        $this->source .= $source . '.tmp';
        $source = $this->cachePath . $this->source;

        if (!empty($source)) {
            $this->data = [];
            if (file_exists($source)) {
                $fileData = file_get_contents($source);
                if (!empty($fileData)) {
                    $this->data = json_decode($fileData, true);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $key
     * @param $data
     */
    public function addData($key, $data)
    {
        $this->data[$key] = $data;
        $this->updateSource();
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getData($key)
    {
        if ($this->checkData($key)) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * @param $key
     * @return bool
     */
    public function checkData($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * @param $key
     * @return bool
     */
    public function clearData($key)
    {
        if ($this->checkData($key)) {
            unset($this->data[$key]);
            $this->updateSource();
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function sizeData()
    {
        return count($this->data);
    }

    /**
     * @param $index
     * @return mixed|null
     */
    public function getDataByIndex($index)
    {
        $keys = array_keys($this->data);
        if (isset($keys[$index])) {
            return $this->data[$keys[$index]];
        }

        return null;
    }

}