<?php 
namespace Core\middleware;

class Salesmanager {
    public function handle() {
        $role = $_SESSION['user']['UserType'] ?? null;

        if (!in_array($role, [1, 2, 5])) {
            abort(403);
            exit();
        }
    }
}
