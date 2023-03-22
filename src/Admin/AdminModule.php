<?php

namespace App\Admin;

use Framework\Module;
use Mezzio\Router\Route;
use Mezzio\Router\FastRouteRouter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Framework\Renderer\RendererInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Router\Middleware\RouteMiddleware;

class AdminModule extends Module
{

    const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @param FastRouteRouter $router
     * @param RendererInterface $renderer
     */
    public function __construct(FastRouteRouter $router, RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath('admin', __DIR__ . DIRECTORY_SEPARATOR . 'views');
    }

    public function index(array $params, RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}
