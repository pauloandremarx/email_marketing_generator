<?php

session_start();

require '../vendor/autoload.php';

 
use DI\Container;
use Slim\Csrf\Guard;
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Views\TwigMiddleware;
use Slim\Views\Twig;
// Create Container
$container = new Container();
AppFactory::setContainer($container);

$handler = new \Whoops\Handler\PrettyPageHandler;
$handler->setEditor("vscode");
$whoops = new \Whoops\Run;
$whoops->prependHandler($handler);
$whoops->register();

 

$container->set('cache', function () {
    return new \Slim\HttpCache\CacheProvider();
});
 
// Create App
$app = AppFactory::create();

$basepath = (function () {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $uri = (string) parse_url('http://a' . $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if (stripos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
        return $_SERVER['SCRIPT_NAME'];
    }
    if ($scriptDir !== '/' && stripos($uri, $scriptDir) === 0) {
        return $scriptDir;
    }
    return '';
})();

$app->setBasePath($basepath);

 
// Add Twig-View Middleware


$container->set('csrf', function () use ($app) {
    $responseFactory = $app->getResponseFactory();
    return new Guard($responseFactory);
});

$container->set('basepath', function () use ($app) { 
    $basepath = $app->getBasePath();
    return $basepath;
});



require '../app/routes/site.php'; 
require '../app/routes/user.php';




$methodOverrideMiddleware = new MethodOverrideMiddleware();
$app->add($methodOverrideMiddleware);

$twig = Twig::create(DIR_VIEWS); 
$app->add(TwigMiddleware::create($app, $twig));


$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    $response->getBody()->write('Something wrong');
    return $response;
});


  
$app->run();

