<?php

namespace App\Profile;

//use App\Profile\Table\ProfileTable;

use App\Profile\Table\ProfileTable;
use Framework\App;
use Framework\Module;
use Mezzio\Router\FastRouteRouter;
use Psr\Http\Message\ResponseInterface;
use Framework\Renderer\RendererInterface;
use GuzzleHttp\Psr7\Utils;
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

    /**
     * @var ProfileTable
     */
    private $profileTable;

    public function __construct(FastRouteRouter $router, RendererInterface $renderer, ProfileTable $profileTable)
    {
        $this->profileTable = $profileTable;
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

        $m = $params['id'] ?? App::getLoggedUserId(1);
        
        $member = $this->profileTable->find($m);
        $vowels = ['A','E','I','O','U','Y','a','e','i','o','u','y'];
    
        $this->renderer->addGlobal('status', $this->getStatus($m));
        $this->renderer->addGlobal('pms', $this->profileTable->getPM($m));
        $this->renderer->addGlobal('fillots', $this->profileTable->getFillots($m));
        $this->renderer->addGlobal('adelphes', $this->profileTable->getAdelphes($m));
        $this->renderer->addGlobal('paillarde', $this->profileTable->getPaillarde($m));
        $this->renderer->addGlobal('legend', $this->profileTable->getLegend($m));
        $this->renderer->addGlobal('bapt', $this->profileTable->getBapt($m));
        $this->renderer->addGlobal('member', $member);
        $this->renderer->addGlobal('VOWELS', $vowels);

        return $response;
    }

    /**
     * @param int $p L'id de l'utilisateur
     * 
     * @return null|string Le statut Faluchard
     */
    private function getStatus(int $p): ?string
    {
        $tva = $this->profileTable->find($p)->tva;
        if (!is_null($this->profileTable->getBapt($p))) {
            $date = $this->profileTable->getBapt($p)['timestamp'];
            $origin = date_create(date('Y-m-d', $date));
            $target = date_create();
            $interval = date_diff($origin, $target);
            
            $i = $interval->days;
            $y = 365.25;

            if ($i > 2*$y) {
                if ($tva) {
                    return "Très Vénérable Ancien-ne Faluchard-e (TVA)";
                } 
                return "Vénérable Ancien-ne Faluchard-e";
            } else if ($i > 1*$y) {
                return "Ancien-ne Faluchard-e";
            } else {
                return "Jeune Faluchard-e";
            }
        }
        return null;
    }
}
