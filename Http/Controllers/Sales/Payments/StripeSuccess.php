<?php
use Core\App;
use Core\Database;

$db = App::resolve(Database::class);
$invoiceId = $_GET['id'] ?? null;

if (!$invoiceId) {
    die("<h3>Invalid request. Invoice ID missing.</h3>");
}

// Fetch the invoice
$invoice = $db->query("SELECT * FROM invoices WHERE id = :id", [
    'id' => $invoiceId
])->find();

$invoices = $db->query("SELECT * FROM invoice_items WHERE invoice_id = :id", [
    'id' => $invoiceId
])->find();

$product = $db->query("SELECT * FROM products WHERE id = :id", [
    'id' => $invoices['product_id']
])->find();

$customer = $db->query("SELECT customer_code FROM customers WHERE id = :id", [
    'id' => $invoices['customer_id']
])->find();

if (!$invoice && !$invoices && !$product && !$customer) {
    die("Invoice not found or token mismatch.");
}


$amount = $invoice['grand_total'];
$currency = $invoice['currency'];
$token = $invoice['public_token'];

// Insert into payments table
$db->query("INSERT INTO payments (invoice_id, payment_method, amount, status, currency, transaction_id, paid_at) VALUES (
    :invoice_id, :payment_method, :amount, :status, :currency, :transaction_id, :paid_at
)", [
    'invoice_id' => $invoiceId,
    'payment_method' => 'Stripe',
    'amount' => $amount,
    'status' => 'completed',
    'currency' => $currency,
    'transaction_id' => 'manual-' . uniqid(),
    'paid_at' => date('Y-m-d H:i:s'),
]);

$paymentId = $db->lastInsertId();

// Insert into transactions table
$db->query("INSERT INTO transactions (payment_id, account_id, type, amount, payer_id, category_id, description,payment_method) VALUES (
    :payment_id, :account_id, :type, :amount, :payer_id, :category_id,  :description,:payment_method
)", [
    'payment_id' => $paymentId,
    'account_id' => 1,
    'type' => 'credit',
    'amount' => $invoice['grand_total'],
    'payer_id'=> $customer['customer_code'],
    'category_id'=> $product['category_id'],
    'description' => 'Paystack payment for invoice #' . $invoice['invoice_number'],
    'payment_method'=>'Stripe',
]);


// Mark invoice as paid 
  $db->query("UPDATE invoices SET payment_status = 'Paid', payment_method = 'Stripe' WHERE id = :id", [
        'id' => $invoiceId
    ]);

    $db->query(
    "UPDATE accounts 
     SET current_balance = current_balance + :amount 
     WHERE id = :id",
    [
        'id' => 1,
        'amount' => $invoice['grand_total']
    ]
);

         $successMessage = '
      <h2>✅ Payment successful</h2>
 <strong>Success</strong>: Invoice #'.$invoice['invoice_number'].' has been marked as paid.
      <a href="/AIS/invoice-views?id=' . $invoiceId . '&token=' . $invoice['public_token'] . '" class="btn btn-info btn-lg" target="_blank">
        <span class="icon-file-text2" aria-hidden="true"></span> View
      </a> &nbsp;&nbsp;
      <a href="/AIS/invoice-view?id=' . $invoiceId . '&token=' . $invoice['public_token'] . '" class="btn btn-orange btn-lg" target="_blank">
        <span class="icon-earth" aria-hidden="true"></span> Public View
      </a>';

 $_SESSION['success'] = $successMessage;
   redirect('/AIS/invoice-view?id=' . $invoiceId . '&token=' . $token);
    exit;