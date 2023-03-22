<?php

namespace Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TrailingSlashMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === "/" && $uri !== "/") {
            return $response
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        return $response;
    }
}
