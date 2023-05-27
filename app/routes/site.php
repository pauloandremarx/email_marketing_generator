<?php

use app\controllers\Home;
use app\controllers\Login;
use app\controllers\Admin;
use app\controllers\Livros;
use app\controllers\PostsPhotoController;
use app\controllers\Api_produto_manole;

use Slim\Routing\RouteCollectorProxy;
 
$app->group('/', function (RouteCollectorProxy $group) {
    $group->get('', Home::class . ':index')->setName('home'); 
    $group->get('red', Home::class . ':red'); 
    $group->get('gerador_email', Home::class . ':gerador_email'); 
    $group->any('download', Home::class . ':download'); 
    $group->get('json', Home::class . ':json'); 
    $group->get('preco', Home::class . ':preco'); 
    $group->post('gravarImagem', Home::class . ':gravarImagem'); 
    
    
    $group->get('admin', Admin::class . ':index');
    $group->get('login', Login::class . ':index');
    $group->post('login', Login::class . ':store');
    $group->get('logout', Login::class . ':destroy'); 
});

 

$app->group('/api_produto_manole', function (RouteCollectorProxy $group) { 
    $group->any('', Api_produto_manole::class . ':index')->setName('api_produto_manole');
    $group->any('/mostrar', Api_produto_manole::class . ':all_produtos')->setName('all_produtos');
});

$app->get('/upload', PostsPhotoController::class . ':index');
$app->post('/doupload', PostsPhotoController::class . ':update');