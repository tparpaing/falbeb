<?php

namespace App\Auth;

use Framework\App;
use Framework\Module;
use GuzzleHttp\Psr7\Utils;
use App\Auth\Table\AuthTable;
use Exception;
use Mezzio\Router\FastRouteRouter;
use Psr\Http\Message\ResponseInterface;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthModule extends Module
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

    /**
     * @var AuthTable
     */
    private $authTable;

    public function __construct(FastRouteRouter $router, RendererInterface $renderer, AuthTable $authTable)
    {
        $this->authTable = $authTable;
        $this->router = $router;
        $this->renderer = $renderer;
        $this->renderer->addPath('auth', __DIR__ . DIRECTORY_SEPARATOR . 'views');
    }

    public function login(array $params, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $success = false;
        $user = null;                
        if ($request->getMethod() === "POST" && !App::loggedIn()) {
            $post = $request->getParsedBody();
            $errors = [];

            if ($this->validateUsername($post['username'])) {
                $username = htmlentities($post['username']);
            } else {
                $errors['username'] = 'L\'identifiant renseigné est invalide';
            }

            $valPwd = $this->validatePassword($post['password']);
            if ($valPwd['global']) {
                $password = htmlentities($post['password']);
            } else {
                $errPwd = [
                    $valPwd['length'] ? '' : ' 8 caractères',
                    $valPwd['number'] ? '' : ' un nombre',
                    $valPwd['uppercase'] ? '' : ' une lettre majuscule',
                    $valPwd['lowercase'] ? '' : ' une lettre minuscule',
                    $valPwd['specialChars'] ? '' : ' un caractère spécial'
                ];
                $errors['password'] = substr(array_reduce($errPwd, function ($acc, $item) {
                    if (!empty($item)) {
                        $acc .= ($item . ',');
                    }
                    return $acc;
                }, 'Le mot de passe doit contenir au moins'), 0, -1);
            }


            if (empty($errors)) {
                $user = $this->authTable->find($username, $password);
                if ($user != null) {
                    $_SESSION['auth'] = $user;
                    $success = true;
                }
            }
        }

        $this->renderer->addGlobal('user', $user);
        $this->renderer->addGlobal('success', $success);
        $this->renderer->addGlobal('errors', $errors ?? []);
        if (!isset($username) || empty($errors)) {
            $this->renderer->addGlobal('username', '');
        } else {
            $this->renderer->addGlobal('username', $username);
        }

        $response = $handler->handle($request);
        
        if ($success) {
            return $response
                ->withHeader('Location', $this->router->generateUri('auth.redirect', ['route' => 'profile-show-index']));
        }

        return $response;
    }

    public function redirect(array $params, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = str_replace('-', '.', substr($request->getServerParams()['REQUEST_URI'],10));

        $response = $handler->handle($request);

        return $response
            ->withStatus(303)
            ->withHeader('Location', $this->router->generateUri($route));
    }

    public function register(array $params, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        return $response;
    }

    public function errors(array $params, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($this->renderer->getGlobal('errors') !== null) {
            return $response
                ->withBody(Utils::streamFor((string)$this->renderer->getGlobal('errors')));
        }
        return $response;
    }

    public function logout(array $params, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (App::loggedIn()) {
            $_SESSION['auth'] = null;
        }

        $response = $handler->handle($request);
        return $response
            ->withStatus(303)
            ->withHeader('Location', $this->router->generateUri('auth.login'));;
    }

    /**
     * @param string $email
     * @return bool
     */
    private function validateUsername($username): bool
    {
        if (isset($username) && !empty($username)) {
            $matches = [];
            preg_match('/[a-z].[a-z]+/', $username, $matches);
            return (!empty($matches));
        }
        return false;
    }

    private function validatePassword(string $password): array
    {
        if (!isset($password) || empty($password)) {
            return [
                'global' => false,
                'length' => false,
                'number' => false,
                'uppercase' => false,
                'lowercase' => false,
                'specialChars' => false,
                'message' => null
            ];
        }

        return [
            'global' => ((strlen($password) >= 8) && preg_match('@[0-9]@', $password) && preg_match('@[A-Z]@', $password) && preg_match('@[a-z]@', $password) && preg_match('@[^\w]@', $password)),
            'length' => (strlen($password) >= 8),
            'number' => preg_match('@[0-9]@', $password),
            'uppercase' => preg_match('@[A-Z]@', $password),
            'lowercase' => preg_match('@[a-z]@', $password),
            'specialChars' => preg_match('@[^\w]@', $password),
            'message' => 'Le mot de passe est invalide'
        ];
    }
}
