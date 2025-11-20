<?php
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/HomeController.php';
require_once __DIR__ . '/app/controllers/LocalController.php';

require_once __DIR__ . '/app/middlewares/SessionMiddleware.php';
require_once __DIR__ . '/app/middlewares/GuardMiddleware.php';

session_start();

define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/');

$action = 'home';
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}

$params = explode('/', $action);

$request = new StdClass();
$request = (new SessionMiddleware())->run($request);
$request->params = $params;

// Ruteo
switch ($params[0]) {
    case 'home':
    case 'listar':
    case '':
        $controller = new \App\Controllers\HomeController(require __DIR__ . '/config/config.php');
        $controller->index($request);
        break;

    case 'login':
        $controller = new \App\Controllers\AuthController(require __DIR__ . '/config/config.php');
        $controller->showLogin($request);
        break;

    case 'do_login':
        $controller = new \App\Controllers\AuthController(require __DIR__ . '/config/config.php');
        $controller->doLogin($request);
        break;

    case 'logout':
        $request = (new GuardMiddleware())->run($request);
        $controller = new \App\Controllers\AuthController(require __DIR__ . '/config/config.php');
        $controller->logout($request);
        break;

    case 'local_new':
    case 'local/create_form':
        $request = (new GuardMiddleware())->run($request);
        $controller = new \App\Controllers\LocalController(require __DIR__ . '/config/config.php');
        $controller->createForm($request);
        break;

    case 'local_create':
    case 'local/create':
        $request = (new GuardMiddleware())->run($request);
        $controller = new \App\Controllers\LocalController(require __DIR__ . '/config/config.php');
        $controller->create($request);
        break;

    case 'local_edit':
    case 'local/edit':
        $request = (new GuardMiddleware())->run($request);
        $controller = new \App\Controllers\LocalController(require __DIR__ . '/config/config.php');
        $request->id = isset($params[1]) ? intval($params[1]) : 0;
        $controller->editForm($request);
        break;

    case 'local_update':
    case 'local/update':
        $request = (new GuardMiddleware())->run($request);
        $controller = new \App\Controllers\LocalController(require __DIR__ . '/config/config.php');
        $controller->update($request);
        break;

    case 'local_delete':
    case 'local/delete':
        $request = (new GuardMiddleware())->run($request);
        $controller = new \App\Controllers\LocalController(require __DIR__ . '/config/config.php');
        if (isset($params[1]) && is_numeric($params[1])) {
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