<?php
use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

// Check if we’re editing (i.e., an ID is passed)
$invoice = null;
$items = [];

if (isset($_GET['id'])) {
    $invoiceId = $_GET['id'];

         if (!$invoiceId) {
            die("<script>alert('Invalid Token'); window.history.back();</script>");
        }
    // Fetch invoice
    $invoice = $db->query("SELECT * FROM invoices WHERE id = :id", [
        'id' => $invoiceId
    ])->find();

     if (!$invoice) {
        die("<script>alert('Invoice not found.'); window.history.back();</script>");
     
    }
       $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $invoice['customer_id']
        ])->find();
    // Fetch items
    $items = $db->query("SELECT * FROM invoice_items WHERE invoice_id = :id", [
        'id' => $invoiceId
    ])->get();


      $products = $db->query("SELECT * FROM products WHERE id = :id", [
        'id' => $items[0]['product_id']
    ])->find();
   
    $warehouses = $db->query("SELECT * FROM warehouses WHERE status = 'active' ORDER BY name ASC", [
        ])->get();
    views('Sales/new_invoice.view.php', [
    'invoice' => $invoice,
    'customer' => $customer,
    'items' => $items,
    'products' => $products,
    'warehouses' => $warehouses
]);
exit;
}
// var_dump($invoice);
// exit;

// Now load the view and pass the invoice and items if available
$warehouses = $db->query("SELECT * FROM warehouses WHERE status = 'active' ORDER BY name ASC", [
    ])->get();  
views('Sales/new_invoice.view.php', [
    'warehouses' => $warehouses
]);
