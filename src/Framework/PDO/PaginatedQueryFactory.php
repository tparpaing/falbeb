<?php

namespace Framework\PDO;

use PDO;
use Psr\Container\ContainerInterface;

class PaginatedQueryFactory
{

    public function __invoke(ContainerInterface $container): PaginatedQuery
    {
        return new PaginatedQuery(
            $container->get(PDO::class),
            $container->get('query'),
            $container->get('countQuery')
        );
    }
}
