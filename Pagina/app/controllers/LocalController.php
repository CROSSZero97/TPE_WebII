<?php
namespace App\Controllers;
require_once __DIR__ . '/../models/LocalModel.php';
require_once __DIR__ . '/../models/PizzaModel.php';

use App\Models\LocalModel;
use App\Models\PizzaModel;

class LocalController {
    private $localModel;
    private $pizzaModel;
    private $config;
    public function __construct($config){
        $this->config = $config;
        $this->localModel = new LocalModel($config);
        $this->pizzaModel = new PizzaModel($config);
    }

    private function ensureAdmin($request){
        if (empty($request->user)) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        if (empty($request->user->admin) || $request->user->admin != 1) {
            header('Location: ' . BASE_URL . 'home');
            exit;
        }
    }

    public function createForm($request){
        $this->ensureAdmin($request);
        $local  = null;
        $error  = "";
        $user   = $request->user;
        $pizzas = $this->pizzaModel->all();
        require __DIR__ . '/../views/LocalView.phtml';
    }

    public function create($request){
        $this->ensureAdmin($request);

        $nombre_raw   = isset($_POST['lclnombre']) ? (string)$_POST['lclnombre'] : '';
        $especial_raw = isset($_POST['lclespecial']) ? $_POST['lclespecial'] : null;

        if (trim($nombre_raw) === '') {
            $error = "Nombre requerido";
            $local = null;
            $user = $request->user;
            $pizzas = $this->pizzaModel->all();
            require __DIR__ . '/../views/LocalView.phtml';
            return;
        }

        if ($especial_raw === '' || $especial_raw === null) {
            $error = "Debe seleccionar una pizza especial";
            $local = null;
            $user = $request->user;
            $pizzas = $this->pizzaModel->all();
            require __DIR__ . '/../views/LocalView.phtml';
            return;
        }

        $nombre  = filter_var($nombre_raw, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $especial = is_numeric($especial_raw) ? intval($especial_raw) : null;

        if ($especial === null || !$this->pizzaModel->find($especial)) {
            $error = "Pizza seleccionada inválida";
            $local = null;
            $user = $request->user;
            $pizzas = $this->pizzaModel->all();
            require __DIR__ . '/../views/LocalView.phtml';
            return;
        }

        $this->localModel->insert($nombre, $especial);
        header('Location: ' . BASE_URL . 'home');
    }

    public function editForm($request){
        $this->ensureAdmin($request);
        $id = isset($request->id) ? intval($request->id) : 0;
        $local = $this->localModel->find($id);
        if (!$local) {
            echo "Local no encontrado";
            return;
        }
        $error  = "";
        $user   = $request->user;
        $pizzas = $this->pizzaModel->all();
        require __DIR__ . '/../views/LocalView.phtml';
    }

    public function update($request){
        $this->ensureAdmin($request);

        $id_raw       = isset($_POST['id']) ? $_POST['id'] : 0;
        $nombre_raw   = isset($_POST['lclnombre']) ? (string)$_POST['lclnombre'] : '';
        $espera_raw   = isset($_POST['lclespera']) ? $_POST['lclespera'] : 0;
        $especial_raw = isset($_POST['lclespecial']) ? $_POST['lclespecial'] : null;

        $id = is_numeric($id_raw) ? intval($id_raw) : 0;
        if ($id <= 0) {
            header('Location: ' . BASE_URL . 'home');
            return;
        }

        if (trim($nombre_raw) === '') {
            $error = "Nombre requerido";
            $local = ['id'=>$id,'lclnombre'=>$nombre_raw,'lclespera'=>$espera_raw,'lclespecial'=>$especial_raw];
            $user = $request->user;
            $pizzas = $this->pizzaModel->all();
            require __DIR__ . '/../views/LocalView.phtml';
            return;
        }

        if ($especial_raw === '' || $especial_raw === null) {
            $error = "Debe seleccionar una pizza especial";
            $local = ['id'=>$id,'lclnombre'=>$nombre_raw,'lclespera'=>$espera_raw,'lclespecial'=>$especial_raw];
            $user = $request->user;
            $pizzas = $this->pizzaModel->all();
            require __DIR__ . '/../views/LocalView.phtml';
            return;
        }

        $nombre   = filter_var($nombre_raw, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $espera   = is_numeric($espera_raw) ? intval($espera_raw) : 0;
        $especial = is_numeric($especial_raw) ? intval($especial_raw) : null;

        if ($especial === null || !$this->pizzaModel->find($especial)) {
            $error = "Pizza seleccionada inválida";
            $local = ['id'=>$id,'lclnombre'=>$nombre,'lclespera'=>$espera,'lclespecial'=>$especial];
            $user = $request->user;
            $pizzas = $this->pizzaModel->all();
            require __DIR__ . '/../views/LocalView.phtml';
            return;
        }

        $this->localModel->update($id, $nombre, $espera, $especial);
        header('Location: ' . BASE_URL . 'home');
    }

    public function delete($request){
        $this->ensureAdmin($request);
        $id = isset($request->id) ? intval($request->id) : 0;
        if ($id <= 0) {
            header('Location: ' . BASE_URL . 'home');
            return;
        }
        $this->localModel->delete($id);
        header('Location: ' . BASE_URL . 'home');
    }
}