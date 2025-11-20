<?php
class Request {
    public $body = null;      // parsed JSON body
    public $params = null;    // route params (object)
    public $query = null;     // query string (object)
    public $user = null;      // user payload from JWT (object)
    public $authorization = null; // raw Authorization header

    public function __construct() {
        try {
            $this->body = json_decode(file_get_contents('php://input'));
        } catch (Exception $e) {
            $this->body = null;
        }
        $this->authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
        $this->query = (object) $_GET;
        $this->params = null;
        $this->user = null;
    }
}