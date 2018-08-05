<?php
namespace Core\Cache\Adapter;

/**
 * Interface CacheInterface
 * @package Core\Cache\Interfaces
 */
interface AdapterInterface
{
    /**
     * @return bool
     */
    public function isEnable();

    /**
     * @param $name
     * @param array $params
     * @return mixed
     */
    public function loadCache($name, $params = []);

    /**
     * @param $name
     * @param $data
     * @param array $params
     * @param int $seconds
     * @return bool
     */
    public function toCache($name, $data, $params = [], $seconds = 0);

    /**
     * @param $name
     * @param array $params
     * @return bool
     */
    public function isCached($name, $params = []);

    /**
     * @param $name
     * @param array $params
     * @return bool
     */
    public function cleanCache($name, $params = []);
}