<?php

namespace Core\Structure\Plugins;

/**
 * Class Collection
 * @package Core\Structure\Plugins
 */
trait Collection
{

    private $__collectionStorage;
    private $defaultName = 'collection';

    private function __prepareInstanceIndex($name)
    {
        return strtolower(str_replace('\\', '_', $name));
    }

    private function __prepareCollectionName($collectionName)
    {
        return (is_null($collectionName)) ? $this->defaultName : $collectionName;
    }

    protected function _CollectionNew($name)
    {
        $this->__collectionStorage[$name] = [];
    }

    /**
     * @param $list
     * @param null $collectionName
     */
    protected function _CollectionInit($list, $collectionName = null)
    {
        if ((!empty($list)) && (is_array($list))) {
            $collectionName = $this->__prepareCollectionName($collectionName);
            $this->__collectionStorage[$collectionName] = $list;
        }
    }

    /**
     * @param $index
     * @param $resource
     * @param null $collectionName
     * @return $this
     */
    protected function _CollectionAddResource($index, $resource, $collectionName = null)
    {
        $index = $this->__prepareInstanceIndex($index);
        $cName = $this->__prepareCollectionName($collectionName);

        if (!isset($this->__collectionStorage[$cName]) || !is_array($this->__collectionStorage[$cName])) {
            $this->__collectionStorage[$cName] = [];
        }

        $this->__collectionStorage[$cName][$index] = $resource;
        return $this;
    }

    /**
     * @param $index
     * @param null $collectionName
     * @return null
     */
    protected function _CollectionGetResource($index, $collectionName = null)
    {
        $index = $this->__prepareInstanceIndex($index);
        $collectionName = $this->__prepareCollectionName($collectionName);

        if ($this->_CollectionCheckResource($index, $collectionName)) {
            return $this->__collectionStorage[$collectionName][$index];
        }

        return null;
    }

    /**
     * @param $index
     * @param null $collectionName
     * @return bool
     */
    protected function _CollectionCheckResource($index, $collectionName = null)
    {
        $index = $this->__prepareInstanceIndex($index);
        $collectionName = $this->__prepareCollectionName($collectionName);

        if (isset($this->__collectionStorage[$collectionName][$index])) {
            return true;
        }

        return false;
    }

    /**
     * @param $index
     * @param null $collectionName
     * @return $this
     */
    protected function _CollectionRemoveResource($index, $collectionName = null)
    {
        $index = $this->__prepareInstanceIndex($index);
        $collectionName = $this->__prepareCollectionName($collectionName);

        if ($this->_CollectionCheckResource($index, $collectionName)) {
            unset($this->__collectionStorage[$collectionName][$index]);
        }

        return $this;
    }

    /**
     * @param null $collectionName
     * @return array
     */
    protected function _CollectionGetAllResources($collectionName = null)
    {
        $collectionName = $this->__prepareCollectionName($collectionName);

        if (isset($this->__collectionStorage[$collectionName])) {
            return $this->__collectionStorage[$collectionName];
        }

        return [];
    }

}