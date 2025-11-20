<?php
require_once __DIR__ . '/../models/local.model.php';

class LocalApiController {
    private $model;

    public function __construct() {
        $this->model = new LocalModel();
    }

    public function getLocales($req, $res) {
        $order = $req->query['order'] ?? ($req->params['order'] ?? 'lclnombre');
        $dir = $req->query['dir'] ?? ($req->params['dir'] ?? 'asc');

        $filters = [
            'id' => isset($req->query['id']) ? (int)$req->query['id'] : ($req->params['id'] ?? null),
            'q' => $req->query['q'] ?? null,
            'lclespera' => isset($req->query['lclespera']) ? (int)$req->query['lclespera'] : null
        ];

        $limit = isset($req->query['limit']) ? (int)$req->query['limit'] : 100;
        $offset = isset($req->query['offset']) ? (int)$req->query['offset'] : 0;

        try {
            $data = $this->model->getAll($order, $dir, $filters, $limit, $offset);
            return $res->json($data, 200);
        } catch (Exception $e) {
            return $res->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getLocal($req, $res) {
        $id = isset($req->params['id']) ? (int)$req->params['id'] : null;
        if (!$id) return $res->json(['message' => 'ID inválido'], 400);
        $local = $this->model->get($id);
        if (!$local) return $res->json(['message' => 'Local no encontrado'], 404);
        return $res->json($local, 200);
    }

    public function createLocal($req, $res) {
        $body = $req->body ?? [];
        if (empty($body['lclnombre'])) return $res->json(['message' => 'Faltan datos'], 400);

        $nombre = $body['lclnombre'];
        $espera = isset($body['lclespera']) ? (int)$body['lclespera'] : 0;
        $especial = isset($body['lclespecial']) ? (int)$body['lclespecial'] : null;

        try {
            $id = $this->model->insert($nombre, $especial, $espera);
            $created = $this->model->get($id);
            return $res->json($created, 201);
        } catch (Exception $e) {
            return $res->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateLocal($req, $res) {
        $id = isset($req->params['id']) ? (int)$req->params['id'] : null;
        if (!$id) return $res->json(['message' => 'ID inválido'], 400);

        $body = $req->body ?? [];
        if (empty($body['lclnombre'])) return $res->json(['message' => 'Faltan datos'], 400);

        try {
            $ok = $this->model->update($id, $body['lclnombre'], (int)$body['lclespera'], isset($body['lclespecial']) ? (int)$body['lclespecial'] : null);
            if (!$ok) return $res->json(['message' => 'Local no encontrado'], 404);
            $updated = $this->model->get($id);
            return $res->json($updated, 200);
        } catch (Exception $e) {
            return $res->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteLocal($req, $res) {
        $id = isset($req->params['id']) ? (int)$req->params['id'] : null;
        if (!$id) return $res->json(['message' => 'ID inválido'], 400);
        try {
            $ok = $this->model->delete($id);
            if (!$ok) return $res->json(['message' => 'Local no encontrado'], 404);
            return $res->json(['message' => "Local con id={$id} eliminado"], 200);
        } catch (Exception $e) {
            return $res->json(['error' => $e->getMessage()], 500);
        }
    }
}