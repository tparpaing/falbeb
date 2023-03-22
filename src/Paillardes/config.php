<?php

use function DI\add;
use function DI\get;
use function DI\autowire;

return [
    'routesToAdd' => add([
        [
            'path' => '/paillardes',
            'name' => 'paillardes.index',
            'methods' => ['GET'],
            'levenstein' => [
                'enabled' => true,
                'action_text' => 'voir les paillardes'
            ]
        ],
        [
            'path' => '/paillardes/page/{page:\d+}',
            'name' => 'paillardes.index.page',
            'methods' => ['GET']
        ],
        [
            'path' => '/paillardes/{id:\d+}',
            'name' => 'paillardes.show',
            'methods' => ['GET']
        ]
    ])
];
