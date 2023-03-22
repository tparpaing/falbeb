<?php

namespace Framework\Middleware;

use PDO;
use Framework\App;
use GuzzleHttp\Psr7\Utils;
use Mezzio\Router\FastRouteRouter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Oefenweb\DamerauLevenshtein\DamerauLevenshtein;

class RendererMiddleware implements MiddlewareInterface
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
     * @var array
     */
    private $modules = [];

    /**
     * @var array
     */
    private $routes = [];

    /**
     * @param RendererInterface $renderer
     * @param FastRouteRouter $router
     * @param PDO $pdo
     */
    public function __construct(RendererInterface $renderer, FastRouteRouter $router, PDO $pdo, array $modules, array $routes)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->modules = $modules;
        $this->routes = $routes;

        $this->renderer->addGlobal('router', $this->router);
        $this->renderer->addGlobal('pdo', $pdo);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = $this->router->match($request);

        // Inject the actual route result, as well as individual matched parameters.
        $request = $request
            ->withAttribute(RouteResult::class, $result);

        $params = $result->getMatchedParams();

        $view = $result->getMatchedRouteName();

        // On récupère le nom de la route et on le décompose en deux pour avoir le nom
        if (strpos($view, '.') === false) {
            $response = $handler->handle($request);
        } else {
            $matches = explode('.', $view, 3);
            $viewNamespace = '@' . $matches[0];
            $viewName = $matches[1];
            $view = $viewNamespace . '/' . $viewName;

            $response = ($this->modules[$viewNamespace]['src'])->$viewName($params, $request, $handler);
        }

        if ($result->isSuccess() && !$request->getAttribute('NotFound', false)) {
            $this->renderer->addGlobal('navLinks', $this->getNavLinks());
            foreach ($params as $param => $value) {
                $request = $request->withAttribute($param, $value);
            }
        }

        $routeResult = $request->getAttribute(RouteResult::class);

        if (!$routeResult->isSuccess() || $request->getAttribute('NotFound', false)) {
            $nearestPage = $this->findNearestPageFor404($request->getUri()->getPath());

            $addParam = ['nearestPage' => $nearestPage];

            return $response
                ->withStatus(404)
                ->withBody(Utils::streamFor($this->renderer->render('404', $addParam)));
        }

        return $response->withBody(Utils::streamFor($this->renderer->render($view, $params)));
    }

    private function getNavLinks(): array
    {
        $acc = [];
        foreach ($this->modules as $k => $v) {
            $acc[] = [
                'name' => substr($k, 1),
                'baseRoute' => $v['baseRoute'],
                'displayName' => $v['displayName']
            ];
        }
        return $acc;
    }

    private function findNearestPageFor404(string $string): ?array
    {
        $nearestPage = null;
        foreach ($this->routes as $r) {
            if ($r['levenstein']['enabled'] ?? false) {
                $pattern = $r['path'];
                $damerauLevenshtein = new DamerauLevenshtein($pattern, $string);
                $similarity = $damerauLevenshtein->getSimilarity();
                if ($nearestPage === null) {
                    $nearestPage = [
                        'path' => $r['path'],
                        'path_name' => $r['name'],
                        'display_action_text' => $r['levenstein']['action_text'] ?? $r['path'],
                        'similarity' => $similarity
                    ];
                } else {
                    if ($similarity < $nearestPage['similarity']) {
                        $nearestPage = [
                            'path' => $r['path'],
                            'path_name' => $r['name'],
                            'display_action_text' => $r['levenstein']['action_text'] ?? $r['path'],
                            'similarity' => $similarity
                        ];
                    }
                }
            } else {
                continue;
            }
        }

        if ($nearestPage !== null && $nearestPage['similarity'] <= 2) {
            return [
                'path' => $nearestPage['path_name'],
                'title' => $nearestPage['display_action_text']
            ];
        }
        return null;
    }
}
