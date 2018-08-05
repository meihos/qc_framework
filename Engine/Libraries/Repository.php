<?php

namespace Libraries;

use Libraries\Cache\Factory as CacheFactory;
use Libraries\HashTable\Factory as HashTableBuilder;
use Libraries\Log\Factory as LogFactory;

/**
 * Class Repository
 * @package Libraries
 */
trait Repository
{
    /**
     * @var CacheFactory
     */
    public $cache;

    /**
     * @var LogFactory
     */
    public $log;

    /**
     * @var HashTableBuilder
     */
    public $hashTable;
} 