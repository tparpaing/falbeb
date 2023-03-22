<?php

use function DI\add;
use function DI\get;
use function DI\autowire;

return [
    'routesToAdd' => add([
        [
            'path' => '/tree',
            'name' => 'tree.index',
            'methods' => ['GET'],
            'levenstein' => [
                'enabled' => true,
                'action_text' => 'consulter l\'arbre'
            ]
        ]
    ])
];
