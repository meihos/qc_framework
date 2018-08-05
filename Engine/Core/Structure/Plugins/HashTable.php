<?php
namespace Core\Structure\Plugins;

use Core\Structure\Interfaces\HashTableAdapterInterface;

/**
 * Class HashTable
 * @package Core\Structure\Plugins
 */
trait HashTable
{
    /**
     * @var HashTableAdapterInterface
     */
    protected $__hashTableAdapter;

    public function setAdapter(HashTableAdapterInterface $adapter)
    {
        $this->_HashTableSetAdapter($adapter);
        return $this;
    }

    /**
     * @return bool
     */
    private function __checkHashTableAdapter()
    {
        if (empty($this->__hashTableAdapter) || !$this->__hashTableAdapter instanceof HashTableAdapterInterface) {
            return false;
        }

        return true;
    }

    /**
     * @param $source
     * @param $adapter
     * @param $prefix
     * @return bool
     */
    protected function _HashTableLoad($source, $adapter, $prefix = null)
    {
        if ((!is_object($adapter)) || (!$adapter instanceof HashTableAdapterInterface)) {
            return false;
        }

        $adapter->loadSource($source, $prefix);
        $this->_HashTableSetAdapter($adapter);

        return false;
    }

    /**
     * @param       $key
     * @param mixed $default
     *
     * @return mixed
     */
    protected function _HashTableGetRecord($key, $default = false)
    {
        if (!$this->__checkHashTableAdapter()) {
            return $default;
        }

        if ($this->__hashTableAdapter->checkData($key)) {
            return $this->__hashTableAdapter->getData($key);
        }

        return $default;
    }

    /**
     * @param $index
     * @return mixed|null
     */
    protected function _HashTableGetRecordByIndex($index)
    {
        if (!$this->__checkHashTableAdapter()) {
            return null;
        }

        return $this->__hashTableAdapter->getDataByIndex($index);
    }

    /**
     * @param $key
     * @return bool
     */
    protected function _HashTableCheckRecord($key)
    {
        if (!$this->__checkHashTableAdapter()) {
            return false;
        }

        return $this->__hashTableAdapter->checkData($key);
    }

    /**
     * @param $key
     * @return bool
     */
    protected function _HashTableRemoveRecord($key)
    {
        if ($this->__checkHashTableAdapter()) {
            $this->__hashTableAdapter->clearData($key);
            return true;
        }

        return false;
    }

    /**
     * @param $key
     * @param $data
     * @param bool $strict
     * @return bool
     */
    protected function _HashTableAddRecord($key, $data, $strict = true)
    {
        if (!$this->__checkHashTableAdapter()) {
            return false;
        }

        if ($this->__hashTableAdapter->checkData($key)) {
            if ($strict) {
                return $this->__hashTableAdapter->addData($key, $data);
            }
        } else {
            return $this->__hashTableAdapter->addData($key, $data);
        }
    }

    /**
     * @return int
     */
    protected function _HashTableGetSize()
    {
        if (!$this->__checkHashTableAdapter()) {
            $this->__hashTableAdapter->sizeData();
        }

        return null;
    }

    /**
     * @param HashTableAdapterInterface $adapter
     * @return $this
     */
    protected function _HashTableSetAdapter(HashTableAdapterInterface $adapter)
    {
        $this->__hashTableAdapter = $adapter;
        return $this;
    }

    /**
     * @return bool
     */
    protected function _HashTableIsInitAdapter()
    {
        return $this->__checkHashTableAdapter();
    }
}