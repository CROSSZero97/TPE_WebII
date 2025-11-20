<?php
namespace App\Controllers;
require_once __DIR__ . '/../models/LocalModel.php';
require_once __DIR__ . '/../models/PizzaModel.php';

use App\Models\LocalModel;
use App\Models\PizzaModel;

class HomeController {
    private $localModel;
    private $pizzaModel;
    private $config;
    public function __construct($config){
        $this->config = $config;
        $this->localModel = new LocalModel($config);
        $this->pizzaModel = new PizzaModel($config);
    }

    public function index($request){
        $alias = isset($request->params[1]) ? (string)$request->params[1] : 'nombre';

        $map = [
            'nombre'  => 'lclnombre',
            'espera'  => 'lclespera',
            'precios' => 'pizza.pzprecio'
        ];
        $order = isset($map[$alias]) ? $map[$alias] : 'lclnombre';

        $locals = $this->localModel->all($order);
        $user = isset($request->user) ? $request->user : null;

        $pizzasWithLocals = $this->pizzaModel->withLocals();

        require __DIR__ . '/../views/ListView.phtml';
    }
}