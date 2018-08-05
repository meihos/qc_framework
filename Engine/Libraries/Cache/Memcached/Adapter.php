<?php
namespace Libraries\Cache\Memcached;

use Core\Cache\Adapter\AdapterInterface;

/**
 * Class CacheAdapter
 * @package Libraries\Cache\Memcached
 */
class Adapter implements AdapterInterface
{

    private $memcachedInstance;
    private $isConnect;

    public function __construct($host, $port, $weight = 0)
    {
        $this->memcachedInstance = new \Memcached();
        $this->isConnect = $this->memcachedInstance->addServer($host, $port, $weight);
    }

    /**
     * @param $name
     * @param $params
     * @return string
     */
    private function buildName($name, $params)
    {
        $paramsString = serialize($params);
        return md5($name . md5($paramsString));
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return $this->isConnect;
    }

    /**
     * @param $name
     * @param array $params
     * @return mixed
     */
    public function loadCache($name, $params = [])
    {
        $key = $this->buildName($name, $params);

        if ($this->isConnect) {
            $result = $this->memcachedInstance->get($key);
            $resultCode = $this->memcachedInstance->getResultCode();
            if (!in_array($resultCode, [\Memcached::RES_NOTFOUND, \Memcached::RES_NOTSTORED])) {
                return unserialize($result);
            }
        }

        return null;
    }

    /**
     * @param $name
     * @param $data
     * @param array $params
     * @param int $seconds
     * @return bool
     */
    public function toCache($name, $data, $params = [], $seconds = 0)
    {
        $key = $this->buildName($name, $params);

        if ($this->isConnect) {
            return $this->memcachedInstance->set($key, serialize($data), $seconds);
        }

        return false;
    }

    /**
     * @param $name
     * @param array $params
     * @return bool
     */
    public function isCached($name, $params = [])
    {
        $key = $this->buildName($name, $params);

        if ($this->isConnect) {
            $this->memcachedInstance->get($key);
            $resultCode = $this->memcachedInstance->getResultCode();
            if (!in_array($resultCode, [\Memcached::RES_NOTFOUND, \Memcached::RES_NOTSTORED])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $name
     * @param array $params
     * @return bool
     */
    public function cleanCache($name, $params = [])
    {
        $key = $this->buildName($name, $params);

        if ($this->isConnect) {
            return $this->memcachedInstance->delete($key);
        }

        return false;
    }

} 