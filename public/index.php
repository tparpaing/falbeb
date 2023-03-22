<?php
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Whoops\Run;
use Framework\App;
use DI\ContainerBuilder;
use App\Admin\AdminModule;
use App\Auth\AuthModule;
use App\Contact\ContactModule;
use App\Paillardes\PaillardesModule;
use App\Tree\TreeModule;

use function \Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;
use Whoops\Handler\PrettyPageHandler;

$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

$modules = [
    '@admin' => [
        'src' => AdminModule::class,
        'displayName' => 'Administration'
    ],
    '@tree' => [
        'src' => TreeModule::class,
        'displayName' => 'Arbre'
    ],
    '@paillardes' => [
        'src' => PaillardesModule::class,
        'displayName' => 'Paillardes'
    ],
    '@contact' => ContactModule::class,
    '@auth' => [
        'src' => AuthModule::class,
        'baseRoute' => 'login',
        'displayName' => 'Se connecter'
    ]
];

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

foreach ($modules as $m) {
    if (is_array($m)) {
        $m = $m['src'];
    }
    if (!is_null($m::DEFINITIONS)) {
        $builder->addDefinitions($m::DEFINITIONS);
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