<?php

namespace Framework\Renderer;

use Framework\App;

class PHPRenderer implements RendererInterface
{

    const DEFAULT_NAMESPACE = '__MAIN';

    /**
     * @var string[]
     */
    private $paths = [];

    /**
     * @var array
     */
    private $globals = [];

    /**
     * @param string|null $defaultPath
     */
    public function __construct(?string $defaultPath = null)
    {
        if (!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
    }

    /**
     * Ajoute le chemin dans le tableau
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
    }

    /**
     * @param string $view
     * @param string[] $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {

        if (!isset($params['title']) || empty($params['title'])) {
            $params['title'] = '';
            $params['displayTitle'] = "Faluchologie de la <span>Bite en Bois</span>";
        } else {
            $params['displayTitle'] = $this->getDisplayTitle($params['title']);
            $params['title'] = $this->getTitle($params['title']) . ' | ';
        }

        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        ob_start();
        $renderer = $this;
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();
    }

    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    public function getGlobals(): array
    {
        return $this->globals;
    }

    public function getGlobal(string $key)
    {
        return $this->globals[$key] ?? null;
    }

    /**
     * @param string $view
     * @return bool
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    /**
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    /**
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }

    /**
     * Retourne le titre de la page avec le dernier mot entouré d'une span
     * 
     * @param string $title Le titre
     * @return string Le titre modifié
     */
    private function getDisplayTitle(string $title): string
    {
        $matches = [];

        preg_match('/(.+){(.+)}/', $title, $matches);

        if (!empty($matches)) {
            return $matches[1] . ' <span>' . ucfirst(trim($matches[2])) . '</span>';
        } else {
            $words = explode(' ', $title);
            $last_word = array_pop($words);
            $first_words = implode(' ', $words);
            return $first_words . ' <span>' . ucfirst(trim($last_word)) . '</span>';
        }
    }

    /**
     * Retourne le titre de la page
     * 
     * @param string $title Le titre
     * @return string Le titre modifié
     */
    private function getTitle(string $title): string
    {
        $matches = [];

        preg_match('/(.+){(.+)}/', $title, $matches);

        if (!empty($matches)) {
            return $matches[1] . ucfirst(trim($matches[2]));
        } else {
            return $title;
        }
    }
}
