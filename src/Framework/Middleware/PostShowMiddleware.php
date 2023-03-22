<?php

namespace Framework\Middleware;

use PDO;
use Exception;
use Mezzio\Router\FastRouteRouter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PostShowMiddleware implements MiddlewareInterface
{

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var FastRouteRouter
     */
    private $router;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @param RendererInterface $renderer
     * @param FastRouteRouter $router
     * @param PDO $pdo
     */
    public function __construct(RendererInterface $renderer, FastRouteRouter $router, PDO $pdo)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->pdo = $pdo;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = $this->router->match($request);

        if (!isset($response)) {
            $response = $handler->handle($request);
        }

        return $response;
    }
}
