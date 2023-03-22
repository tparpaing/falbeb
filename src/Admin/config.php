<?php

use function DI\add;
use function DI\get;
use function DI\autowire;

use App\Admin\AdminModule;

return [
    'routesToAdd' => add([
        [
            'path' => '/admin',
            'name' => 'admin.index',
            'methods' => ['GET']
        ]
    ])
];
