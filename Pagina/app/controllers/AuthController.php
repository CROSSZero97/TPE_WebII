<?php
namespace App\Controllers;
require_once __DIR__ . '/../models/UserModel.php';

use App\Models\UserModel;

class AuthController {
    private $userModel;
    private $config;
    public function __construct($config){
        $this->config = $config;
        $this->userModel = new UserModel($config);
    }

    public function showLogin($request){
        $error = "";
        require __DIR__ . '/../views/LoginView.phtml';
    }

    public function doLogin($request){
        $userInput = isset($_POST['user']) ? trim((string)$_POST['user']) : '';
        $password  = isset($_POST['password']) ? (string)$_POST['password'] : '';

        if ($userInput === '' || $password === '') {
            $error = "Faltan datos obligatorios";
            require __DIR__ . '/../views/LoginView.phtml';
            return;
        }

        $username = filter_var($userInput, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $dbUser = $this->userModel->getByUsername($username);

        if ($dbUser) {
            if (password_verify($password, $dbUser['contrasena'])) {
                $_SESSION['USER_ID']    = $dbUser['id'];
                $_SESSION['USER_NAME']  = $dbUser['usuario'];
                $_SESSION['USER_ADMIN'] = (int)$dbUser['admin'];
                session_regenerate_id(true);
                header('Location: ' . BASE_URL . 'home');
                return;
            } else {
                $error = "Usuario o contraseña incorrecta";
                require __DIR__ . '/../views/LoginView.phtml';
                return;
            }
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $newId = $this->userModel->create($username, $hash, 0);
        $_SESSION['USER_ID']    = $newId;
        $_SESSION['USER_NAME']  = $username;
        $_SESSION['USER_ADMIN'] = 0;
        session_regenerate_id(true);
        header('Location: ' . BASE_URL . 'home');
    }

    public function logout($request){
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL . 'login');
        return;
    }
}