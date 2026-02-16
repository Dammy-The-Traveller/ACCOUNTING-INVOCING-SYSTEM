<?php 
namespace Core\middleware;

class Salesperson {
    public function handle() {
        $role = $_SESSION['user']['UserType'] ?? null;

        if (!in_array($role, [1, 2, 3])) {
            abort(403);
            exit();
        }
    }
}
