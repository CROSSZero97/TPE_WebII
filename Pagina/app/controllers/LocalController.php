<?php
namespace App\Controllers;
require_once __DIR__ . '/../models/LocalModel.php';

use App\Models\LocalModel;

class LocalController {
    private $localModel;
    private $config;
    public function __construct($config){
        $this->config = $config;
        $this->localModel = new LocalModel($config);
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
    }

    private function ensureAdmin($request){
        if(empty($request->user) || empty($request->user->admin) || $request->user->admin != 1){
            header('Location: /WebII_TPE/home');
            exit;
        }
    }

    public function createForm($request){
        $this->ensureAdmin($request);
        $local = null;
        $error = "";
        $user = $request->user;
        require __DIR__ . '/../views/LocalView.phtml';
    }

    public function create($request){
        $this->ensureAdmin($request);
        $nombre = trim($_POST['lclnombre'] ?? '');
        $especial = $_POST['lclespecial'] ?? null;

        if($nombre === ''){
            $error = "Nombre requerido";
            $local = null;
            $user = $request->user;
            require __DIR__ . '/../views/LocalView.phtml';
            return;
        }

        $nombre = filter_var($nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $especial = $especial !== '' ? intval($especial) : null;

        $this->localModel->insert($nombre, $especial);
        header('Location: /WebII_TPE/home');
    }

    public function editForm($request){
        $this->ensureAdmin($request);
        $id = intval($request->id ?? 0);
        $local = $this->localModel->find($id);
        if(!$local){
            echo "Local no encontrado";
            return;
        }
        $error = "";
        $user = $request->user;
        require __DIR__ . '/../views/LocalView.phtml';
    }

    public function update($request){
        $this->ensureAdmin($request);
        $id = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['lclnombre'] ?? '');
        $espera = intval($_POST['lclespera'] ?? 0);
        $especial = $_POST['lclespecial'] ?? null;

        if($nombre === ''){
            $error = "Nombre requerido";
            $local = ['id'=>$id,'lclnombre'=>$nombre,'lclespera'=>$espera,'lclespecial'=>$especial];
            $user = $request->user;
            require __DIR__ . '/../views/LocalView.phtml';
            return;
        }

        $nombre = filter_var($nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $especial = $especial !== '' ? intval($especial) : null;

        $this->localModel->update($id, $nombre, $espera, $especial);
        header('Location: /WebII_TPE/home');
    }

    public function delete($request){
        $this->ensureAdmin($request);
        $id = intval($request->id ?? 0);
        $this->localModel->delete($id);
        header('Location: /WebII_TPE/home');
    }
}