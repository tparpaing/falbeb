<?php

namespace Framework\Renderer;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FilesystemLoader
     */
    private $loader;

    /**
     * @var array
     */
    private $globals = [];

    public function __construct(string $path)
    {
        $this->loader = new FilesystemLoader($path);
        $this->twig = new Environment($this->loader, [
            'cache' => false
        ]);
    }

    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }

    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    public function getGlobal(string $key)
    {
        return $this->globals[$key] ?? null;
    }

    public function getGlobals(): array
    {
        return $this->globals;
    }
}
