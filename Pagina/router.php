<?php
require_once './app/controllers/AuthController.php';
require_once './app/controllers/HomeController.php';
require_once './app/controllers/LocalController.php';

require_once './app/middlewares/SessionMiddleware.php';
require_once './app/middlewares/GuardMiddleware.php';

session_start();

// BASE_URL para redirecciones y base tag (opcional usar)
define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/');

// Acción por defecto
$action = 'home';
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}

// Parseo de la acción para separar acción real de parámetros
$params = explode('/', $action);

// Inicializo request y ejecuto middleware de sesión
$request = new StdClass();
$request = (new SessionMiddleware())->run($request);
$request->params = $params;

// Ruteo
switch ($params[0]) {
    case 'home':
    case 'listar':
    case '':
        $controller = new app\controllers\HomeController(require './config/config.php');
        $controller->index($request);
        break;

    case 'login':
        $controller = new app\controllers\AuthController(require './config/config.php');
        $controller->showLogin($request);
        break;

    case 'do_login':
        $controller = new app\controllers\AuthController(require './config/config.php');
        $controller->doLogin($request);
        break;

    case 'logout':
        $request = (new GuardMiddleware())->run($request);
        $controller = new app\controllers\AuthController(require './config/config.php');
        $controller->logout($request);
        break;

    case 'local_new':
    case 'local/create_form':
        $request = (new GuardMiddleware())->run($request);
        $controller = new app\controllers\LocalController(require './config/config.php');
        $controller->createForm($request);
        break;

    case 'local_create':
    case 'local/create':
        $request = (new GuardMiddleware())->run($request);
        $controller = new app\controllers\LocalController(require './config/config.php');
        $controller->create($request);
        break;

    case 'local_edit':
    case 'local/edit':
        $request = (new GuardMiddleware())->run($request);
        $controller = new app\controllers\LocalController(require './config/config.php');
        $request->id = isset($params[1]) ? intval($params[1]) : 0;
        $controller->editForm($request);
        break;

    case 'local_update':
    case 'local/update':
        $request = (new GuardMiddleware())->run($request);
        $controller = new app\controllers\LocalController(require './config/config.php');
        $controller->update($request);
        break;

    case 'local_delete':
    case 'local/delete':
        $request = (new GuardMiddleware())->run($request);
        $controller = new app\controllers\LocalController(require './config/config.php');
        if (isset($params[1])) {
            $request->id = intval($params[1]);
        } else {
            $request->id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        }
        $controller->delete($request);
        break;

    default:
        http_response_code(404);
        echo "404 Page Not Found";
        break;
}