<?php

namespace App\Paillardes;

use App\Paillardes\Table\PaillardesTable;
use Framework\App;
use Framework\Module;
use Mezzio\Router\FastRouteRouter;
use Psr\Http\Message\ResponseInterface;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

class PaillardesModule extends Module
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
     * @var PaillardesTable
     */
    private $paillardesTable;

    public function __construct(FastRouteRouter $router, RendererInterface $renderer, PaillardesTable $paillardesTable)
    {
        $this->paillardesTable = $paillardesTable;
        $this->router = $router;
        $this->renderer = $renderer;
        $this->renderer->addPath('paillardes', __DIR__ . DIRECTORY_SEPARATOR . 'views');
    }

    public function index(array $params, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $paillardes = $this->paillardesTable->getAll();
        $authors = [];
        $originals = [];

        foreach ($paillardes as $p) {
            $a = "";
            foreach ($this->paillardesTable->getAuthors($p->pk_id) as $au) {
                $a .= $au->surnom . ', ';
            }
            $authors[$p->pk_id] = substr($a, 0, -2);
            $originals[$p->pk_id] = $this->paillardesTable->getOriginal($p);
        }

        $this->renderer->addGlobal('paillardes', $paillardes);
        $this->renderer->addGlobal('authors', $authors);
        $this->renderer->addGlobal('originals', $originals);

        $response = $handler->handle($request);
        return $response;
    }

    public function show(array $params, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $p = $params['id'];
        $paillarde = $this->paillardesTable->find($p);

        $a = "";
        foreach ($this->paillardesTable->getAuthors($p) as $au) {
            $a .= $au->surnom . ', ';
        }
        $authors = substr($a, 0, -2);

        $this->renderer->addGlobal('paillarde', $paillarde);
        $this->renderer->addGlobal('authors', $authors);
        $this->renderer->addGlobal('original', $this->paillardesTable->getOriginal($paillarde));
        $this->renderer->addGlobal('paroles', $this->getHtmlLyrics($paillarde));

        $response = $handler->handle($request);
        return $response;
    }

    /**
     * @param stdClass $paillarde
     * @return string
     */
    private function getHtmlLyrics(stdClass $p): string
    {
        $lyrics = $p->paroles;
        $result = $lyrics;

        // Mise en italique
        $pattern = '/\*([^*]+)\*/m';
        $replacement = '<span class="italic">$1</span>';
        $result = preg_replace($pattern, $replacement, $result);

        // Mise en gras
        $pattern = '/\*\*([^*]+)\*\*/';
        $replacement = '<span class="bold">$1</span>';
        $result = preg_replace($pattern, $replacement, $result);

        // Mise en exposant
        $pattern = '/\^([^^]+)\^/';
        $replacement = '<span class="exp">$1</span>';
        $result = preg_replace($pattern, $replacement, $result);

        // Mise en souligné
        $pattern = '/\_([^_]+)\_/';
        $replacement = '<span class="underlined">$1</span>';
        $result = preg_replace($pattern, $replacement, $result);

        // Detection du refrain
        $pattern = '/\[([A-Z]+)\]\{\{([^\}]*)\}\}/';
        $results = [];
        preg_match($pattern, $result, $results);
        if (isset($results[1])) {
            $pattern = '/\[([A-Z]+)\]\{\{([^\}]*)\}\}/';
            $replacement = '<span class="bold">' . $results[2] . '</span>';
            $result = preg_replace($pattern, $replacement, $result);

            $pattern = '/\{\{' . $results[1] . '\}\}/';
            $replacement = '<span class="bold">' . $results[2] . '</span>';
            $result = preg_replace($pattern, $replacement, $result);
        }

        return $result;
    }
}
