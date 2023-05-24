<?php

use App\Auth\AuthModule;

use function DI\add;
use function DI\get;
use function DI\autowire;

return [
    'query' => 'SELECT * FROM users',
    'countQuery' => 'SELECT count(id) FROM users',
    'routesToAdd' => add([
        [
            'path' => '/login',
            'name' => 'auth.login',
            'methods' => ['GET'],
            'levenstein' => [
                'enabled' => true,
                'action_text' => 'vous connecter'
            ]
        ],
        [
            'path' => '/login',
            'name' => 'auth.login.post',
            'methods' => ['POST']
        ],
        [
            'path' => '/logout',
            'name' => 'auth.logout',
            'methods' => ['GET']
        ],
        [
            'path' => '/redirect/{route:[a-z0-9-]+}',
            'name' => 'auth.redirect',
            'methods' => ['GET']
        ],
        [
            'path' => '/register',
            'name' => 'auth.register',
            'methods' => ['GET'],
            'levenstein' => [
                'enabled' => true,
                'action_text' => 'vous inscrire'
            ]
        ],
        [
            'path' => '/form/errors/{token:[a-z0-9A-Z\-]+}',
            'name' => 'auth.formerrors',
            'methods' => ['GET']
        ]
    ])
];
