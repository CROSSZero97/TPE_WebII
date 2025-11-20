<?php
class GuardMiddleware {
    public function run($request){
        if(empty($request->user)){
            header('Location: /WebII_TPE/login');
            exit;
        }
        if(strpos($_SERVER['REQUEST_URI'],'/local') !== false || strpos($_GET['action'] ?? '', 'local_') === 0){
            if(empty($request->user->admin) || $request->user->admin != 1){
                header('Location: /WebII_TPE/home');
                exit;
            }
        }
        return $request;
    }
}