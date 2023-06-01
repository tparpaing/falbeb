<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Whoops\Run;
use Framework\App;
use DI\ContainerBuilder;
use App\Admin\AdminModule;
use App\Auth\AuthModule;
use App\Contact\ContactModule;
use App\Paillardes\PaillardesModule;
use App\Profile\ProfileModule;
use App\Tree\TreeModule;

use function \Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;
use Whoops\Handler\PrettyPageHandler;

$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['auth'])) {
    $_SESSION['auth'] = null;
}

$modules = [
    '@admin' => [
        'src' => AdminModule::class,
        'constraints' => [
            'perms' => 'admin',
            'login' => true
        ],
        'displayName' => 'Administration'
    ],
    '@tree' => [
        'src' => TreeModule::class,
        'constraints' => [
            'login' => true
        ],
        'displayName' => 'Arbre'
    ],
    '@paillardes' => [
        'src' => PaillardesModule::class,
        'constraints' => [
            'login' => true
        ],
        'displayName' => 'Paillardes'
    ],
    '@contact' => ContactModule::class,
    '@auth' => [
        'src' => AuthModule::class,
        'baseRoute' => 'login',
        'displayName' => 'Se connecter'
    ],
    '@profile' => [
        'src' => ProfileModule::class,
        'baseRoute' => 'show.index',
        'constraints' => [
            'login' => true
        ],
        'displayName' => 'Mon profil'
    ]
];

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

foreach ($modules as $m) {
    if (is_array($m)) {
        $constraints = $m['constraints'] ?? [];
        $m = $m['src'];
    } else {
        $constraints = [];
    }
    
    if (App::verifyConstraints($constraints)) {
        
        if (!is_null($m::DEFINITIONS)) {
            $builder->addDefinitions($m::DEFINITIONS);
        }
    }
}

$container = $builder->build();

$app = new App($container, $modules);

if (php_sapi_name() !== 'cli') {
    try {
        send($app->run(ServerRequest::fromGlobals()));
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
