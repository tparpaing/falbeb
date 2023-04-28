<?php

namespace App\Profile;

//use App\Profile\Table\ProfileTable;
use Framework\App;
use Framework\Module;
use Mezzio\Router\FastRouteRouter;
use Psr\Http\Message\ResponseInterface;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

class ProfileModule extends Module
{

    const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var FastRouteRouter
     */
    private $router;

    public function __construct(FastRouteRouter $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->renderer->addPath('profile', __DIR__ . DIRECTORY_SEPARATOR . 'views');
    }

    public function index(array $params, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $this->renderer->addGlobal('auth', $_SESSION['auth']);

        $response = $handler->handle($request);
        return $response;
    }

    public function show(array $params, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        

        $response = $handler->handle($request);
        return $response;
    }
}
