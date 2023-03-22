<?php

namespace App\Auth;

use Framework\App;
use Framework\Module;
use GuzzleHttp\Psr7\Utils;
use App\Auth\Table\AuthTable;
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
        if ($request->getMethod() === "POST") {
            $post = $request->getParsedBody();
            $errors = [];

            if ($this->validateEmail($post['email'])) {
                $email = htmlentities($post['email']);
            } else {
                $errors['email'] = 'L\'adresse e-mail renseignée est invalide';
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
                $user = $this->authTable->find($email, $password);
                if ($user != null) {
                    App::debug($user, 1, "User");
                    $success = true;
                }
            }
        }

        $this->renderer->addGlobal('success', $success);
        $this->renderer->addGlobal('user', $user);
        $this->renderer->addGlobal('errors', $errors ?? []);
        if (!isset($email) || empty($errors)) {
            $this->renderer->addGlobal('email', '');
        } else {
            $this->renderer->addGlobal('email', $email);
        }

        $response = $handler->handle($request);
        return $response;
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

    /**
     * @param string $email
     * @return bool
     */
    private function validateEmail($email): bool
    {
        if (isset($email) && !empty($email)) {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            return (filter_var($email, FILTER_VALIDATE_EMAIL));
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
