<?php

use function DI\add;
use function DI\get;
use function DI\autowire;

return [
    'routesToAdd' => add([
        [
            'path' => '/profiles',
            'name' => 'profile.index',
            'methods' => ['GET']
        ],
        [
            'path' => '/profile',
            'name' => 'profile.show.index',
            'methods' => ['GET'],
            'levenstein' => [
                'enabled' => true,
                'action_text' => 'consulter votre profil'
            ]
        ],
        [
            'path' => '/profiles/{id:\d+}',
            'name' => 'profile.show',
            'methods' => ['GET']
        ]
    ])
];
