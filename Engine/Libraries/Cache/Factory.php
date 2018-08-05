<?php

namespace Libraries\Cache;

use Core\Structure\Components\AbstractLibraryFactory;
use Libraries\Cache\Memcached\Factory as MemcachedFactory;
use Libraries\Cache\Redis\Factory as RedisFactory;

/**
 * Class Factory
 * @package Libraries\Cache
 */
class Factory extends AbstractLibraryFactory
{

    protected function configAutoBuilding()
    {
        $this->factories = [
            'memcached' => MemcachedFactory::class,
            'redis' => RedisFactory::class,
        ];
    }
}