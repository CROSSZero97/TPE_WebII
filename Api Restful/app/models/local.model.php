<?php
class LocalModel {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=localhost;dbname=webii_tpe;charset=utf8', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAll($orderBy = 'lclnombre', $direction = 'ASC', $filters = [], $limit = 100, $offset = 0) {
        $allowed = [
            'lclnombre' => 'l.lclnombre',
            'lclespera' => 'l.lclespera',
            'especial_precio' => 'p.pzprecio',
            'especial_nombre' => 'p.pznombre',
            'id' => 'l.id'
        ];
        if (!isset($allowed[$orderBy])) $orderBy = 'lclnombre';
        $orderExpr = $allowed[$orderBy];
        $dir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        $where = [];
        $params = [];

        if (!empty($filters['id'])) {
            $where[] = 'l.id = :id';
            $params[':id'] = (int)$filters['id'];
        }
        if (isset($filters['lclespera']) && $filters['lclespera'] !== '') {
            $where[] = 'l.lclespera = :lclespera';
            $params[':lclespera'] = (int)$filters['lclespera'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(l.lclnombre LIKE :q OR p.pznombre LIKE :q)';
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $limit = max(1, min(1000, (int)$limit));
        $offset = max(0, (int)$offset);

        $sql = "
            SELECT l.id, l.lclnombre, l.lclespera, l.lclespecial,
                   p.pznombre AS especial_nombre, p.pzprecio AS especial_precio
            FROM `local` l
            LEFT JOIN pizza p ON l.lclespecial = p.id
            $whereSql
            ORDER BY $orderExpr $dir
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get($id) {
        $stmt = $this->db->prepare("
            SELECT l.id, l.lclnombre, l.lclespera, l.lclespecial,
                   p.pznombre AS especial_nombre, p.pzprecio AS especial_precio
            FROM `local` l
            LEFT JOIN pizza p ON l.lclespecial = p.id
            WHERE l.id = :id
        ");
        $stmt->execute([':id' => (int)$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function insert($nombre, $especial = null, $espera = 0) {
        $stmt = $this->db->prepare('INSERT INTO `local` (lclnombre, lclespera, lclespecial) VALUES (:n, :es, :sp)');
        $stmt->execute([':n' => $nombre, ':es' => $espera, ':sp' => $especial]);
        return (int)$this->db->lastInsertId();
    }

    public function update($id, $nombre, $espera, $especial = null) {
        $stmt = $this->db->prepare('UPDATE `local` SET lclnombre = :n, lclespera = :es, lclespecial = :sp WHERE id = :id');
        return $stmt->execute([':n' => $nombre, ':es' => $espera, ':sp' => $especial, ':id' => (int)$id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM `local` WHERE id = :id');
        return $stmt->execute([':id' => (int)$id]);
    }
}