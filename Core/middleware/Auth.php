<?php 
namespace Core\middleware;
class Auth{
    public function handle(){
        
        if(!$_SESSION['user'] ?? false){
            header('location:/AIS/');
            exit();
        }
    }
}