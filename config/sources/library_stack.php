<?php
$config = [
    'config' => [
        'cache' => \Libraries\Cache\Factory::class,
        'log' => \Libraries\Log\Factory::class,
        'hash' => \Libraries\HashTable\Factory::class,
    ],
    'filename' => __DIR__ . '/../build/library_stack',
];

return $config;