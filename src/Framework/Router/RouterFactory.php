<?php

namespace Framework\Router;

use Mezzio\Router\FastRouteRouter;

class RouterFactory
{

    public function __invoke(): FastRouteRouter
    {
        return new FastRouteRouter();
    }
}
