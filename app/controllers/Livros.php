<?php

namespace app\controllers;

use app\classes\Csrf;
use app\classes\Flash;
use app\classes\Recover;
use app\classes\Validate;
use app\database\models\ModelLivros;
use Psr\Container\ContainerInterface;
use app\classes\Image;

class Livros extends Base
{
    private $validate;
    private $user;
    private $livro; 
    private $container;
 
    public function __construct(ContainerInterface $container)
    {
        $this->validate = new Validate;
        $this->livro = new ModelLivros;
        $this->recover = new Recover;  
        $this->container = $container;
    }


    public function index($request, $response)
    { 
        $livros = $this->livro->find();

        $message = Flash::get('message'); 

        return $this->getTwig()->render($response, $this->setView('site/livro/index'), [
            'title' => 'Mostrar Livros', 
            'livros' => $livros,
            'message' => $message,
        ]);
    }

 

    public function create($request, $response, $args)
    {
        $messages = Flash::getAll(); 

        return $this->getTwig()->render($response, $this->setView('site/livro/form'), [
            'title' => 'Adicionar livro',
            'messages' => $messages
        ]);
    }

    public function edit($request, $response, $args)
    {
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        $livro = $this->livro->findBy('id', $id);

        if (!$livro) {
            Flash::set('message', 'Usuário não existe', 'danger');
            return redirect($response, BASE_URL.'/livro');
        }

        $messages = Flash::getAll();

        return $this->getTwig()->render($response, $this->setView('site/livro/form'), [
            'title' => 'Editar livro',
            'livro' => $livro,
            'messages' => $messages
        ]);
    }

    public function store($request, $response, $args)
    {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $genero = filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_STRING);
        $capa = filter_input(INPUT_POST, 'capa', FILTER_SANITIZE_STRING);
        $preco = filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_STRING);

        $this->validate->requiredFile(['capa'])->required(['nome', 'genero', 'preco'])->exist($this->livro, 'nome', $nome);
     
        $errors = $this->validate->getErrors();
        $post_existe = $this->recover->existPost(['nome', 'genero', 'capa', 'preco']);

        if ($errors) {
            Flash::flashes($errors);
            Flash::flashes($post_existe);
            return redirect($response, BASE_URL.'/livro/create');
        }else{
            $image = new Image('capa');
            $image->size('post')->upload(); 
            $created = $this->livro->create(['nome' => $nome, 'genero' => $genero, 'preco' => $preco, 'capa' => "/assets/uploads/{$image->getName()}"]);
    
            if ($created) {
                Flash::set('message', 'Cadastrado com sucesso');
                return redirect($response, BASE_URL.'/livro');
            }
    
            Flash::set('message', 'Ocorreu um erro ao cadastrar o user');
            return redirect($response, BASE_URL.'/livro/create');
    
            return $response;
        }

     
    }

    public function update($request, $response, $args)
    {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $genero = filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_STRING); 
        $preco = filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_STRING);
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        $this->validate->required(['nome', 'genero', 'preco']);
        $errors = $this->validate->getErrors();

        if( $_FILES["capa"]['error'] == UPLOAD_ERR_OK )
        {
            if ($errors) {
                Flash::flashes($errors);
                Flash::set('message', "Não foi possivel atualizar");
                return redirect($response, BASE_URL.'/livro/edit/' . $id);
            }

            $image = new Image('capa'); 
            $livro = new ModelLivros;
            $livroEncontrado = $livro->findBy('id', $args['id']); 
            $image->delete($livroEncontrado->capa);
            
            $image->size('post')->upload(); 
        
            $updated = $this->livro->update([ 'fields' => ['nome' => $nome, 'genero' => $genero, 'preco' => $preco, 'capa' => "assets/uploads/{$image->getName()}"], 'where' => ['id' => $id]]);

            if ($updated) {
                Flash::set('message', "atualizado com sucesso");
                return redirect($response, BASE_URL.'/livro'); 
            }
        }else{
            if ($errors) {
                Flash::flashes($errors);
                Flash::set('message', "Não foi possivel atualizar");
                return redirect($response, BASE_URL.'/livro/edit/' . $id);
            }
            $updated = $this->livro->update([ 'fields' => ['nome' => $nome, 'genero' => $genero, 'preco' => $preco], 'where' => ['id' => $id]]);
            if ($updated) {
                Flash::set('message', "atualizado com sucesso");
                return redirect($response, BASE_URL.'/livro'); 
            }
        } 

            

    }

    public function destroy($request, $response, $args)
    {
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        $livro = $this->livro->findBy('id', $id); 

        if (!$livro) {
            Flash::set('message', 'Usuário não existe', 'danger');
            return redirect($response, BASE_URL.'/livro');
        }

        function path() {
			$vendorDir = dirname(dirname(__FILE__));
			return dirname($vendorDir);
		}

		unlink(path() . '/public/' . $livro->capa); 

        $deleted = $this->livro->delete('id', $id);

        if ($deleted) {
            Flash::set('message', 'Deletado com sucesso');
            return redirect($response,  BASE_URL.'/livro');
        }

        Flash::set('message', 'Ocorreu um erro ao deletar', 'danger');
        return redirect($response,  BASE_URL.'/livro');
    }
}
