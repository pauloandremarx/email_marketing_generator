<?php

namespace app\controllers;

use app\classes\Csrf;
use app\classes\Flash;
use app\classes\Validate;
use app\classes\Recover;
use app\database\models\ModelUser;
use Psr\Container\ContainerInterface;

class User extends Base
{
    private $validate;
    private $user; 

    public function __construct(ContainerInterface $container)
    {
        $this->validate = new Validate;
        $this->recover = new Recover;
        $this->user = new ModelUser;
        $this->container = $container; 
    }

    public function index($request, $response)
    {
        $users = $this->user->find(); 
        $message = Flash::get('message'); 

        return $this->getTwig()->render($response, $this->setView('site/user/user_index'), [
            'title' => 'Usuários',
            'users' => $users,
            'message' => $message,
        ]);
    }

    public function create($request, $response, $args)
    {
        $messages = Flash::getAll();
        return $this->getTwig()->render($response, $this->setView('site/user/user_create'), [
            'title' => 'User Create',
            'messages' => $messages
        ]);
    }

    public function edit($request, $response, $args)
    {
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        $user = $this->user->findBy('id', $id);

        if (!$user) {
            Flash::set('message', 'Usuário não existe!', 'danger');
            return redirect($response, $request, $this->container->get('basepath').'/user');
        }

        $messages = Flash::getAll();
        $csrf = $this->container->get('csrf');

        $crossSiteRequestForgery = Csrf::csrf($request, $csrf);

        return $this->getTwig()->render($response, $this->setView('site/user/user_edit'), [
            'title' => 'User edit',
            'user' => $user,
            'messages' => $messages,
            'csrf' => $crossSiteRequestForgery
        ]);
    }

    public function store($request, $response, $args)
    { 
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING); 
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
 
        $this->validate->required(['name', 'email', 'password'])->exist($this->user, 'email', $email)->email($email);
        $errors = $this->validate->getErrors();
        $post_existe = $this->recover->existPost(['name', 'email', 'password']);

        if ($errors) {
            Flash::flashes($errors);
            Flash::flashes($post_existe);
            return redirect($response, $this->container->get('basepath').'/user/create');
        }
        
       
        $created = $this->user->create(['name' => $name, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)]);

        if ($created) {
            Flash::set('message', 'Cadastrado com sucesso');
            return redirect($response, $this->container->get('basepath').'/user');
        }

        Flash::set('message', 'Ocorreu um erro ao cadastrar o user!');
        return redirect($response, $this->container->get('basepath').'/user/create');

    }

    public function update($request, $response, $args)
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING); 
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        $this->validate->required(['name', 'email', 'password']);
        $errors = $this->validate->getErrors();

        if ($errors) {
            Flash::flashes($errors);
            return redirect($response, $this->container->get('basepath') . '/user/edit/' . $id);
        }

        $updated = $this->user->update(['fields' => ['name' => $name, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)],
            'where' => ['id' => $id]]);

        if ($updated) {
            Flash::set('message', 'Atualziado com sucesso!');
            return redirect($response, $this->container->get('basepath').'/user/edit/' . $id);
        }

        Flash::set('message', 'Ocorreu um erro ao atualizar!', 'danger');
        return redirect($response, $this->container->get('basepath').'/user/edit/' . $id);
    }

    public function destroy($request, $response, $args)
    {
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        $user = $this->user->findBy('id', $id);

        if (!$user) {
            Flash::set('message', 'Usuário não existe!', 'danger');
            return redirect($response, $this->container->get('basepath').'/user');
        }

        $deleted = $this->user->delete('id', $id);

        if ($deleted) {
            Flash::set('message', 'Deletado com sucesso!');
            return redirect($response, $this->container->get('basepath').'/user');
        }

        Flash::set('message', 'Ocorreu um erro ao deletar!', 'danger');
        return redirect($response, $this->container->get('basepath').'/user');
    }
}
