<?php
namespace Libraries\Cache\Redis;

use Core\Cache\Adapter\AdapterInterface;
use Predis\Client as PClient;

/**
 * Class Adapter
 * @package Libraries\Cache\Redis
 */
class Adapter implements AdapterInterface
{

    private $pRedisInstance;
    private $isConnect;

    public function __construct(array $settings, $options = null)
    {
        $this->pRedisInstance = new PClient($settings, $options);
        $this->isConnect = false;
    }

    private function connect()
    {
        if (!$this->isConnect) {
            $this->pRedisInstance->connect();
            $this->isConnect = $this->pRedisInstance->isConnected();
        }
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
        $this->connect();
        $result = null;

        if ($this->isConnect) {
            $isExists = $this->pRedisInstance->exists($key);

            if ($isExists) {
                $data = $this->pRedisInstance->get($key);
                $result = unserialize($data);
            }
        }

        return $result;
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
        $this->connect();

        if ($this->isConnect) {
            return $this->pRedisInstance->set($key, serialize($data), null, $seconds);
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
        $this->connect();

        if ($this->isConnect) {
            return $this->pRedisInstance->exists($key);
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
        $this->connect();

        if ($this->isConnect) {
            return $this->pRedisInstance->del([$key]);
        }

        return false;
    }

} 