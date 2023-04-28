<?php

namespace Framework;

use Exception;
use Mezzio\Router\FastRouteRouter;

class ViewParams
{

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var array
     */
    private $defaultKeys = [];

    /**
     * @var FastRouteRouter
     */
    private $router;

    public function __construct(array $params, FastRouteRouter $router, array $defaultKeys = [])
    {
        foreach ($params as $k => $v) {
            $this->params[$k] = $v;
        }
        if (empty($defaultKeys)) {
            $this->defaultKeys = [
                'no_nav' => false,
                'no_footer' => false,
                'no_container' => false,
                'no_about' => true,
                'scripts' => null,
                'styles' => null,
                'custom_style' => '',
                'style_strict' => false,
                'script_strict' => false
            ];
        } else {
            $this->defaultKeys = $defaultKeys;
        }
        $this->router = $router;
    }

    public function setDefaultKeys(array $newDefaultKeys): ViewParams
    {
        $this->defaultKeys = $newDefaultKeys;

        return $this;
    }

    public function setParamsDefaultVal(?array $keys = null): ViewParams
    {
        if ($keys === null) {
            $keys = $this->defaultKeys;
        }
        if (empty($keys)) {
            throw new Exception('Error: You need to select default keys');
        }

        foreach ($keys as $k => $dv) {
            if (!isset($this->params[$k]) || empty($this->params[$k])) {
                $this->params[$k] = $dv;
            }
        }
        return $this;
    }

    public function getStylesHTML(): string
    {
        if (!isset($this->params['styles']) || empty($this->params['styles']) || $this->params['styles'] === null) {
            return '';
        }
        $styles = '';
        foreach ($this->params['styles'] as $style) {
            $styles .= '<link rel="stylesheet" href="' . $this->router->generateUri('styles') . '/' . $style . '"/>';
        }
        $styles .= '<style>' . $this->getParam('custom_style') . '</style>';
        return $styles;
    }

    public function getScriptsHTML(): string
    {
        if (!isset($this->params['scripts']) || empty($this->params['scripts']) || $this->params['scripts'] === null) {
            return '';
        }
        $scripts = '';
        foreach ($this->params['scripts'] as $script) {
            if (substr($script, 0, 5) === 'https') {
                $scripts .= '<script src="' . $script . '" defer></script>';
            } else {
                $scripts .= '<script src="' . $this->router->generateUri('scripts') . '/' . $script . '" defer></script>';
            }
        }
        return $scripts;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $key)
    {
        if (!isset($this->params[$key])) {
            throw new Exception('Error: Key "' . $key . '" does not exist in parameters');
        } else {
            return $this->params[$key];
        }
    }
}
