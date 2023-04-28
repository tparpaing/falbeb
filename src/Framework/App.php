<?php

namespace Framework;

use Exception;
use PDO;
use Mezzio\Router\Route;
use Mezzio\Router\FastRouteRouter;
use Framework\Middleware\Dispatcher;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Framework\Renderer\RendererInterface;
use Framework\Middleware\RendererMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Router\Middleware\RouteMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;

class App
{


    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array $modules
     */
    private $modules = [];

    /**
     * App constructor
     * @param ContainerInterface $container Le Container d'injection de dépendences
     * @param string[] $modules Liste des modules à charger
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;

        foreach ($modules as $k => $m) {
            if (is_array($m)) {
                $constraints = $m['constraints'] ?? [];
                if ($this->verifyConstraints($constraints)) {
                    $this->modules[$k] = [
                        'src' => $this->container->get($m['src']),
                        'baseRoute' => $m['baseRoute'] ?? 'index',
                        'displayName' => $m['displayName'] ?? substr($k, 1),
                        'args' => $m['args'] ?? []
                    ];
                }
            } else {
                $this->modules[$k] = [
                    'src' => $this->container->get($m),
                    'baseRoute' => 'index',
                    'displayName' => substr($k, 1),
                    'args' => $m['args'] ?? []
                ];
            }
        }
    }

    /**
     * Fonction de démarrage de l'application
     * @param ServerRequestInterface $request La requête au format du PSR7
     *
     * @return ResponseInterface La réponse au format du PSR7
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var FastRouteRouter
         */
        $router = $this->container->get(FastRouteRouter::class);

        /**
         * @var RendererInterface
         */
        $renderer = $this->container->get(RendererInterface::class);

        /**
         * @var PDO
         */
        $pdo = $this->container->get(PDO::class);

        foreach ($this->container->get('routesToAdd') as $r) {
            $router->addRoute(new Route($r['path'], (new RouteMiddleware($router)), $r['methods'], $r['name']));
        }

        $dispatcher = new Dispatcher();
        $dispatcher->pipe(new TrailingSlashMiddleware());
        $dispatcher->pipe(new RendererMiddleware($renderer, $router, $pdo, $this->modules, $this->container->get('routesToAdd')));

        return $dispatcher->handle($request);
    }

    /**
     * @param mixed $var La variable à debugger
     * @param int $mode Le mode à utiliser (0 = Continue l'affichage de la page, 1 = Stop l'affichage de la page)
     * @param string $title Affiche un titre avant le debug
     * @param int $n Numéro de répétition du debug (pratique dans une boucle)
     */
    public static function debug($var, int $mode = 0, string $title = '', int $n = null): void
    {
        echo '<pre>';
        echo "================================ NEW DEBUG ================================\n";
        if ($title !== '') {
            echo $title;
            if ($n !== null) {
                echo " #" . $n;
            }
            echo "\n===========================================================================\n";
        }
        var_dump($var, true);
        echo "---------------------------------------------------------------------------\n";
        echo '</pre>';
        if ($mode == 1) {
            die();
        }
    }

    /**
     * Get Container
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Verifies constraints applied to a module
     * 
     * @return bool
     */
    public static function verifyConstraints(array $constraints): bool
    {
        $acc = true;
        foreach ($constraints as $k => $c) {
            if ($k === 'login' && $c) {
                $acc = self::loggedIn();
                continue;
            }
            
            if ($k === 'perms') {
                if (self::loggedIn()) {
                    $acc = (self::getLoggedUserPerm() === $c);
                }
                continue;
            }
        }
        return $acc;
    }

    /**
     * Tells if the session have a user logged in right now
     * 
     * @return bool
     */
    public static function loggedIn(): bool
    {
        if ($_SESSION['auth'] !== null) {
            return true;
        }
        return false;
    }

    /**
     * Returns the id of a logged-in user
     */
    public static function getLoggedUserId(int $mode = 0): int
    {
        if (self::loggedIn()) {
            if ($mode == 0) {
                return $_SESSION['auth']->id;
            } else if ($mode = 1) {
                return $_SESSION['auth']->fk_membre;
            } else {
                throw new Exception("Tried to use an unauthorized mode for getLoggedUserId() function");
            }
        } else {
            throw new Exception("Tried to use getLoggedUserId() function while no user was logged-in.");
        }
    }

    /**
     * Returns the permission role of a logged-in user
     */
    public static function getLoggedUserPerm(): string
    {
        if (self::loggedIn()) {
            return $_SESSION['auth']->role;
        } else {
            throw new Exception("Tried to use getLoggedUserPerm() function while no user was logged-in.");
        }
    }
}
