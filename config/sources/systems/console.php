<?php

$config = [
    'config' => [
        'application' => [
            'host' => 'console',
            'schema' => 'cgi',
            'basePath' => null,
            'path' => __DIR__ . '/../../../Applications/Console',
            'namespace' => '\\Applications\\Console',
            'system' => 'console',
        ],
        'log' => [
            'simpleLog' => [
                'type' => 'monolog',
                'settings' => [
                    'path' => __DIR__ . '/../../../data/logs/console/simpleLog',
                    'level' => 'DEBUG',
                ],
            ]
        ]
    ],
    'filename' => __DIR__ . '/../../build/systems/console',
];

return $config;