<?php

use function DI\add;
use function DI\get;
use function DI\autowire;

return [
    'routesToAdd' => add([
        [
            'path' => '/contact',
            'name' => 'contact.index',
            'methods' => ['GET'],
            'levenstein' => [
                'enabled' => true,
                'action_text' => 'nous contacter'
            ]
        ]
    ])
];
