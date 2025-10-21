<?php
namespace App\Controllers;
require_once __DIR__ . '/../models/LocalModel.php';

use App\Models\LocalModel;

class HomeController {
    private $localModel;
    private $config;
    public function __construct($config){
        $this->config = $config;
        $this->localModel = new LocalModel($config);
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
    }

    public function index($request){
        $alias = $request->params[1] ?? 'nombre';

        // Mapeo de alias a columnas reales
        $map = [
            'nombre' => 'lclnombre',
            'espera' => 'lclespera',
            'precios' => 'pizza.precio'
        ];
        $order = $map[$alias] ?? 'lclnombre';

        $locals = $this->localModel->all($order);
        $user = $request->user;

        // Pasamos el alias a la vista para que el <select> sepa qué opción marcar
        require __DIR__ . '/../views/ListView.phtml';
    }
}