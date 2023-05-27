<?php

namespace app\controllers;

use app\classes\Flash;
use app\database\models\ModelUser;
use app\database\models\ModelLivros;
use Psr\Container\ContainerInterface;

class Admin extends Base
{
    private $user;
    protected  $container;
    private $livro;

    public function __construct(ContainerInterface $container)
    {
        $this->user = new ModelUser;
        $this->case = new ModelLivros;
        $this->container = $container;
    }

    public function index($request, $response)
    {
        $users = $this->user->find();

        $livros = $this->livro->find();

        $message = Flash::get('message');

     
        /*$cache = $this->container->get('cache');
          $response = $cache->withEtag($response, md5(time()));
          $response = $cache->withExpires($response, '+50 seconds');
          $response = $cache->withLastModified($response, '-50 seconds');
        */

        return $this->getTwig()->render($response, $this->setView('site/admin'), [
            'title' => 'Administrador',
            'users' => $users,
            'livros' => $livros,
            'message' => $message,
        ]);
    }
}
