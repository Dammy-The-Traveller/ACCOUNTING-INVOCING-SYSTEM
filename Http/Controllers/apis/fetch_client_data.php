<?php 
use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

header('Content-Type: application/json');

$search = trim($_GET['query'] ?? '');

if ($search === '') {
    echo json_encode([]);
    exit;
}

$results = $db->query(" SELECT id, customer_code, name, phone, email, address
    FROM customers
    WHERE name LIKE :term OR phone LIKE :term
    LIMIT 10
", [
    'term' => '%' . $search . '%'
])->get();

echo json_encode($results);
