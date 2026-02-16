<?php 
namespace Core\middleware;
class Admin{
    public function handle(){
        
        if($_SESSION['user']['UserType'] == null ?? false){
            abort(403);
            exit();
        }
    }
}