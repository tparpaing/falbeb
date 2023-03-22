<?php

namespace Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Dispatcher implements RequestHandlerInterface
{

    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares = [];

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var ResponseInterface
     */
    private $response;

    public function pipe(MiddlewareInterface $middleware)
    {
        $this->middlewares[] = $middleware;
        $this->response = new Response();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        $this->index++;
        if (is_null($middleware)) {
            return $this->response;
        } else {
            return $middleware->process($request, $this);
        }
    }

    private function getMiddleware(): ?MiddlewareInterface
    {
        if (isset($this->middlewares[$this->index])) {
            return $this->middlewares[$this->index];
        } else {
            return null;
        }
    }
}
