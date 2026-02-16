<?php 
namespace Core\middleware;

class Accountant {
    public function handle() {
        $role = $_SESSION['user']['UserType'] ?? null;

        if (!in_array($role, [1, 4, 5])) {
            abort(403);
            exit();
        }
    }
}
