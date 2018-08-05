<?php
namespace Libraries\Cache\Memcached;

use Core\Cache\Adapter\FactoryInterface;
use Core\Structure\Interfaces\LibraryFactoryInterface;

/**
 * Class Factory
 * @package Libraries\Cache\Memcached
 */
class Factory implements LibraryFactoryInterface, FactoryInterface
{

    /**
     * @param $settings
     * @return Adapter|null
     */
    public function buildLibraryInstance($settings)
    {
        $resource = null;
        if (isset($settings['host']) && isset($settings['port'])) {
            $weight = (array_key_exists('weight', $settings)) ? $settings['weight'] : 0;
            $resource = new Adapter($settings['host'], $settings['port'], $weight);
        }

        return $resource;
    }

    /**
     * @param array $settings
     * @return Adapter|null
     */
    public function buildCacheInstance($settings)
    {
        return $this->buildLibraryInstance($settings);
    }
}