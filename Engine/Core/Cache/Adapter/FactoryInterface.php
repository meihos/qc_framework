<?php
namespace Core\Cache\Adapter;

/**
 * Interface BuilderInterface
 * @package Core\Cache\Adapter
 */
interface FactoryInterface
{
    /**
     * Return Cache instance, what implements with CacheInterface
     *
     * @param $settings
     * @return AdapterInterface|null
     */
    public function buildCacheInstance($settings);
}