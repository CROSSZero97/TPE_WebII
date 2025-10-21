<?php
namespace App\Models;

class UserModel {
    private $db;
    public function __construct($config){
        $c = $config['db'];
        $this->db = new \PDO($c['dsn'],$c['user'],$c['pass'],$c['options']);
    }

    public function getByUsername(string $username){
        $stmt = $this->db->prepare('SELECT id, usuario, contrasena, admin FROM usuarios WHERE usuario = :u');
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getById(int $id){
        $stmt = $this->db->prepare('SELECT id, usuario, admin FROM usuarios WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $username, string $passwordHash, int $admin = 0){
        $stmt = $this->db->prepare('INSERT INTO usuarios (usuario, contrasena, admin) VALUES (:u,:p,:a)');
        $stmt->execute([':u'=>$username, ':p'=>$passwordHash, ':a'=>$admin]);
        return (int)$this->db->lastInsertId();
    }
}