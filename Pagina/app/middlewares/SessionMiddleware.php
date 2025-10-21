<?php
class SessionMiddleware {
    public function run($request){
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
        if(!empty($_SESSION['USER_ID'])){
            $request->user = new stdClass();
            $request->user->id = $_SESSION['USER_ID'];
            $request->user->username = $_SESSION['USER_NAME'];
            $request->user->admin = $_SESSION['USER_ADMIN'] ?? 0;
        } else {
            $request->user = null;
        }
        return $request;
    }
}