<?php
namespace App\Models;

class PizzaModel {
    private $db;
    public function __construct($config){
        $c = $config['db'];
        $this->db = new \PDO($c['dsn'],$c['user'],$c['pass'],$c['options']);
    }

    public function all(): array {
        $stmt = $this->db->prepare('SELECT id, pznombre, pzprecio FROM pizza ORDER BY pznombre ASC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id) {
        $stmt = $this->db->prepare('SELECT id, pznombre, pzprecio FROM pizza WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function withLocals(): array {
        $pizzas = $this->all();

        $stmt = $this->db->prepare('SELECT id, lclnombre, lclespera, lclespecial FROM `local` WHERE lclespecial = :pid ORDER BY lclnombre ASC');

        foreach ($pizzas as &$pz) {
            $stmt->execute([':pid' => (int)$pz['id']]);
            $pz['locales'] = $stmt->fetchAll();
        }
        return $pizzas;
    }
}