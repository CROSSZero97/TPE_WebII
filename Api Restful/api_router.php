<?php

$basePath = '/WebII_TPE/api';

class Response {
    public function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}
$response = new Response();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = substr($uri, strlen($basePath));
$path = trim($path, '/');
$segments = $path === '' ? [] : explode('/', $path);

$req = new stdClass();
$req->params = [];
$req->body = json_decode(file_get_contents('php://input'), true) ?? [];
$req->query = $_GET;
$method = $_SERVER['REQUEST_METHOD'];

function findSegmentValue($segments, $key) {
    $pos = array_search($key, $segments);
    if ($pos === false) return null;
    return $segments[$pos + 1] ?? null;
}

if (isset($segments[0]) && $segments[0] === 'locales') {
    require_once __DIR__ . '/app/controllers/local-api.controller.php';
    $ctrl = new LocalApiController();

    if ($method === 'GET' && isset($segments[1]) && is_numeric($segments[1])) {
        $req->params['id'] = (int)$segments[1];
        $ctrl->getLocal($req, $response);
        exit;
    }

    if ($method === 'PUT' && isset($segments[1]) && is_numeric($segments[1])) {
        $req->params['id'] = (int)$segments[1];
        $ctrl->updateLocal($req, $response);
        exit;
    }

    if ($method === 'DELETE' && isset($segments[1]) && is_numeric($segments[1])) {
        $req->params['id'] = (int)$segments[1];
        $ctrl->deleteLocal($req, $response);
        exit;
    }

    if ($method === 'POST' && count($segments) === 1) {
        $ctrl->createLocal($req, $response);
        exit;
    }

    if ($method === 'GET' && (count($segments) >= 1)) {

        if (isset($segments[1]) && $segments[1] === 'order') {
            $req->params['order'] = $segments[2] ?? null;
            $req->params['dir'] = $segments[3] ?? 'asc';
        }

        if (isset($segments[1]) && $segments[1] === 'search') {
            $req->params['q'] = $segments[2] ?? null;
            if (isset($segments[3]) && $segments[3] === 'order') {
                $req->params['order'] = $segments[4] ?? null;
                $req->params['dir'] = $segments[5] ?? 'asc';
            }
            $req->params['limit'] = findSegmentValue($segments, 'limit') ?? 100;
            $req->params['offset'] = findSegmentValue($segments, 'offset') ?? 0;
        }

        if (isset($segments[1]) && $segments[1] === 'filter' && isset($segments[2]) && $segments[2] === 'espera') {
            $req->params['lclespera'] = isset($segments[3]) ? (int)$segments[3] : null;
            if (in_array('order', $segments)) {
                $req->params['order'] = findSegmentValue($segments, 'order') ?? null;
                $req->params['dir'] = ($segments[array_search('order', $segments) + 2] ?? 'asc');
            }
            $req->params['limit'] = findSegmentValue($segments, 'limit') ?? 100;
            $req->params['offset'] = findSegmentValue($segments, 'offset') ?? 0;
        }

        if (empty($req->params['order'])) $req->params['order'] = 'lclnombre';
        if (empty($req->params['dir'])) $req->params['dir'] = 'asc';
        if (!isset($req->params['limit'])) $req->params['limit'] = 100;
        if (!isset($req->params['offset'])) $req->params['offset'] = 0;

        $ctrl->getLocales($req, $response);
        exit;
    }
}

http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['message' => 'Endpoint no encontrado']);