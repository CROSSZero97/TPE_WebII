<?php
namespace App\Models;

class LocalModel {
    private $db;
    public function __construct($config){
        $c = $config['db'];
        $this->db = new \PDO($c['dsn'],$c['user'],$c['pass'],$c['options']);
    }

    public function all(string $orderBy = 'lclnombre'){
        $allowed = ['lclnombre','lclespera','pizza.pzprecio'];
        $col = in_array($orderBy,$allowed) ? $orderBy : 'lclnombre';

        $stmt = $this->db->prepare("SELECT `local`.id, `local`.lclnombre, `local`.lclespera, `local`.lclespecial, pizza.pznombre AS especial_nombre, pizza.pzprecio AS especial_precio FROM `local` LEFT JOIN pizza ON `local`.lclespecial = pizza.id ORDER BY $col ASC ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id){
        $stmt = $this->db->prepare('SELECT * FROM `local` WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch() ?: null;
    }

    public function insert(string $nombre, ?int $especial){
        $stmt = $this->db->prepare('INSERT INTO `local` (lclnombre, lclespera, lclespecial) VALUES (:n, 0, :e)');
        $stmt->execute([':n'=>$nombre, ':e'=>$especial]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $nombre, int $espera, ?int $especial){
        $stmt = $this->db->prepare('UPDATE `local` SET lclnombre = :n, lclespera = :es, lclespecial = :sp WHERE id = :id');
        return $stmt->execute([':n'=>$nombre, ':es'=>$espera, ':sp'=>$especial, ':id'=>$id]);
    }

    public function delete(int $id){
        $stmt = $this->db->prepare('DELETE FROM `local` WHERE id = :id');
        return $stmt->execute([':id'=>$id]);
    }
}