<?php
namespace Libraries\Cache\Redis;

use Core\Cache\Adapter\FactoryInterface;
use Core\Structure\Interfaces\LibraryFactoryInterface;

/**
 * Class Factory
 * @package Libraries\Cache\Redis
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
        if (isset($settings['connection']) && isset($settings['options'])) {
            $resource = new Adapter($settings['connection'], $settings['options']);
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