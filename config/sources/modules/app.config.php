<?php

$config = [
    'config' => [
        'builders' => [
            'resolver' => \Modules\App\Init\ResolverBuilder::class,
        ],
        'applications' => [
            'console' => [
                'name' => 'console',
                'path' => __DIR__ . '/../../build/systems/console',
            ],
        ],
    ],
    'filename' => __DIR__ . '/../../build/modules/app.config',
];

return $config;