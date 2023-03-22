<?php
require 'public/index.php';

return
    [
        'paths' => [
            'migrations' => __DIR__ . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'migrations',
            'seeds' => __DIR__ . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'seeds'
        ],
        'environments' => [
            'default_migration_table' => 'phinxlog',
            'default_environment' => 'development',
            'production' => [
                'adapter' => 'mysql',
                'host' => $app->getContainer()->get('database.host'),
                'name' => $app->getContainer()->get('database.name'),
                'user' => $app->getContainer()->get('database.username'),
                'pass' => $app->getContainer()->get('database.password')
            ],
            'development' => [
                'adapter' => 'mysql',
                'host' => $app->getContainer()->get('database.host'),
                'name' => $app->getContainer()->get('database.name'),
                'user' => $app->getContainer()->get('database.username'),
                'pass' => $app->getContainer()->get('database.password')
            ]
        ]
    ];
