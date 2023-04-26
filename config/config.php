<?php

use function DI\factory;

use Framework\PDO\PDOFactory;
use Mezzio\Router\FastRouteRouter;
use Framework\Router\RouterFactory;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\PHPRendererFactory;

return [
    'database.host' => 'localhost',
    'database.username' => 'phpmyadmin',
    'database.password' => 'root',
    'database.name' => 'falbeb',
    'views.path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views',
    'routesToAdd' => [
        [
            'path' => '/',
            'name' => 'index',
            'methods' => ['GET']
        ],
        [
            'path' => '/inc/styles',
            'name' => 'styles',
            'methods' => ['GET'],
        ],
        [
            'path' => '/inc/scripts',
            'name' => 'scripts',
            'methods' => ['GET'],
        ],
        [
            'path' => '/cs',
            'name' => 'comingsoon',
            'methods' => ['GET'],
            'levenstein' => [
                'enabled' => true,
                'action_text' => 'aller sur le page Coming Soon'
            ]
        ]
    ],
    PDO::class => factory(PDOFactory::class),
    RendererInterface::class => factory(PHPRendererFactory::class),
    FastRouteRouter::class => factory(RouterFactory::class)
];
