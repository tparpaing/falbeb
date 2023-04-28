<?php

use function DI\add;
use function DI\get;
use function DI\autowire;

return [
    'routesToAdd' => add([
        [
            'path' => '/profile',
            'name' => 'profile.index',
            'methods' => ['GET'],
            'levenstein' => [
                'enabled' => true,
                'action_text' => 'consulter votre profil'
            ]
        ],
        [
            'path' => '/profile/{id:\d+}',
            'name' => 'profile.show',
            'methods' => ['GET']
        ]
    ])
];
