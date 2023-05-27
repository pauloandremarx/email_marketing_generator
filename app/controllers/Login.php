<?php

namespace app\controllers;

use app\classes\Flash;
use app\classes\Login as UserLogin;
use app\classes\Validate;
use Psr\Container\ContainerInterface;

class Login extends Base
{
    private $login;

    public function __construct(ContainerInterface $container)
    {
        $this->login = new UserLogin;
        $this->container = $container;
    }

    public function index($request, $response)
    {
        $messages = Flash::getAll();
        return $this->getTwig()->render($response, $this->setView('site/login'), [
            'title' => 'Login',
            'messages' => $messages
        ]);
    }

    public function store($request, $response)
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        $validate = new Validate;
        $validate->required(['email', 'password'])->email($email);
        $errors = $validate->getErrors();
         
       

        if ($errors) {
            Flash::flashes($errors);
            return redirect($response, $this->container->get('basepath').'/login');
        }

     
        $logged = $this->login->login($email, $password);

        if ($logged) {
            return redirect($response, $this->container->get('basepath'));
        }
        Flash::set('message', 'Ocorreu um erro ao logar, tente novamente em alguns segundos');
        return redirect($response, $this->container->get('basepath').'/login');
    }

    public function destroy($request, $response)
    {
       

        $this->login->logout();

        return redirect($response, $this->container->get('basepath').'/login');
    }
}
