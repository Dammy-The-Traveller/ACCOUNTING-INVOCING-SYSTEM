<?php 
namespace Core\middleware;
class Superadmin{
    public function handle(){
        $role = $_SESSION['user']['UserType'] ?? null;
        if(!in_array($role, [1])){
            abort(403);
            exit();
        }
    }
}