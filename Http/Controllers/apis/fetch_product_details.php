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

$warehouseId = trim($_GET['warehouse_id'] ?? '');

$results = $db->query(
    "SELECT id, name, code, price, tax_percent, discount, description
     FROM products
     WHERE (name LIKE :term OR code LIKE :term) AND warehouse_id = :warehouse_id
     LIMIT 10",
    [
        'term' => '%' . $search . '%',
        'warehouse_id' => $warehouseId
    ]
)->get();


echo json_encode($results);
