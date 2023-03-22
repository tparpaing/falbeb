<?php

declare(strict_types=1);

namespace Mezzio;

use Laminas\HttpHandlerRunner\RequestHandlerRunnerInterface;
use Laminas\Stratigility\MiddlewarePipeInterface;
use Mezzio\Router\RouteCollectorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Laminas\Stratigility\path;

/** @psalm-import-type MiddlewareParam from MiddlewareFactory */
class Application implements MiddlewareInterface, RequestHandlerInterface
{
    public function __construct(
        private MiddlewareFactory $factory,
        private MiddlewarePipeInterface $pipeline,
        private RouteCollectorInterface $routes,
        private RequestHandlerRunnerInterface $runner
    ) {
    }

    /**
     * Proxies to composed pipeline to handle.
     * {@inheritDocs}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->pipeline->handle($request);
    }

    /**
     * Proxies to composed pipeline to process.
     * {@inheritDocs}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->pipeline->process($request, $handler);
    }

    /**
     * Run the application.
     *
     * Proxies to the RequestHandlerRunner::run() method.
     */
    public function run(): void
    {
        $this->runner->run();
    }

    /**
     * Pipe middleware to the pipeline.
     *
     * If two arguments are present, they are passed to pipe(), after first
     * passing the second argument to the factory's prepare() method.
     *
     * If only one argument is presented, it is passed to the factory prepare()
     * method.
     *
     * The resulting middleware, in both cases, is piped to the pipeline.
     *
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middlewareOrPath
     *     Either the middleware to pipe, or the path to segregate the $middleware
     *     by, via a PathMiddlewareDecorator.
     * @param null|string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     If present, middleware or request handler to segregate by the path
     *     specified in $middlewareOrPath.
     * @psalm-param string|MiddlewareParam $middlewareOrPath
     * @psalm-param null|MiddlewareParam $middleware
     */
    public function pipe($middlewareOrPath, $middleware = null): void
    {
        $middleware = $middleware ?: $middlewareOrPath;
        $path       = $middleware === $middlewareOrPath ? '/' : $middlewareOrPath;

        $middleware = $path !== '/'
            ? path($path, $this->factory->prepare($middleware))
            : $this->factory->prepare($middleware);

        $this->pipeline->pipe($middleware);
    }

    /**
     * Add a route for the route middleware to match.
     *
     * @param non-empty-string $path
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     * @param null|list<string> $methods HTTP method to accept; null indicates any.
     * @param null|non-empty-string $name The name of the route.
     */
    public function route(string $path, $middleware, ?array $methods = null, ?string $name = null): Router\Route
    {
        return $this->routes->route(
            $path,
            $this->factory->prepare($middleware),
            $methods,
            $name
        );
    }

    /**
     * @param non-empty-string $path
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     * @param null|non-empty-string $name The name of the route.
     */
    public function get(string $path, $middleware, ?string $name = null): Router\Route
    {
        return $this->route($path, $middleware, ['GET'], $name);
    }

    /**
     * @param non-empty-string $path
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     * @param null|non-empty-string $name The name of the route.
     */
    public function post(string $path, $middleware, $name = null): Router\Route
    {
        return $this->route($path, $middleware, ['POST'], $name);
    }

    /**
     * @param non-empty-string $path
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     * @param null|non-empty-string $name The name of the route.
     */
    public function put(string $path, $middleware, ?string $name = null): Router\Route
    {
        return $this->route($path, $middleware, ['PUT'], $name);
    }

    /**
     * @param non-empty-string $path
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     * @param null|non-empty-string $name The name of the route.
     */
    public function patch(string $path, $middleware, ?string $name = null): Router\Route
    {
        return $this->route($path, $middleware, ['PATCH'], $name);
    }

    /**
     * @param non-empty-string $path
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     * @param null|non-empty-string $name The name of the route.
     */
    public function delete(string $path, $middleware, ?string $name = null): Router\Route
    {
        return $this->route($path, $middleware, ['DELETE'], $name);
    }

    /**
     * @param non-empty-string $path
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *     Middleware or request handler (or service name resolving to one of
     *     those types) to associate with route.
     * @param null|non-empty-string $name The name of the route.
     */
    public function any(string $path, $middleware, ?string $name = null): Router\Route
    {
        return $this->route($path, $middleware, null, $name);
    }

    /**
     * Retrieve all directly registered routes with the application.
     *
     * @return list<Router\Route>
     */
    public function getRoutes(): array
    {
        return $this->routes->getRoutes();
    }
}
