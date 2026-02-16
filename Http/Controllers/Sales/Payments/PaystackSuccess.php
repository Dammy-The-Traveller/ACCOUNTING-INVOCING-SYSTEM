<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

// Get reference from URL
$reference = $_GET['reference'] ?? null;

if (!$reference) {
    die("Invalid request. No reference provided.");
}

// Verify transaction with Paystack
$paystackSecretKey = $_ENV['PAYSTACK_SECRET_KEY'] ?? 'sk_test_0520fdb73d0f2164c6d37bbc3a229cc7887a4447'; 
$verifyUrl = "https://api.paystack.co/transaction/verify/" . urlencode($reference);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $verifyUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $paystackSecretKey",
    "Content-Type: application/json",
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curlError) {
    die("cURL Error: " . $curlError);
}

$result = json_decode($response, true);

if (!$result || !$result['status']) {
    die("Verification failed. Could not verify payment.");
}

// Extract invoice info from metadata
$metadata = $result['data']['metadata'];
$invoiceId = $metadata['invoice_id'] ?? null;
$token = $metadata['token'] ?? null;

if (!$invoiceId || !$token) {
    die("Invalid metadata.");
}

// Fetch invoice
$invoice = $db->query("SELECT * FROM invoices WHERE id = :id AND public_token = :token", [
    'id' => $invoiceId,
    'token' => $token
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

// Insert into payments table
$db->query("INSERT INTO payments (invoice_id, payment_method, amount, status, currency, transaction_id, paid_at) VALUES (
    :invoice_id, :payment_method, :amount, :status, :currency, :transaction_id, :paid_at
)", [
    'invoice_id' => $invoiceId,
    'payment_method' => 'Paystack',
    'amount' => $invoice['grand_total'],
    'status' => 'completed',
    'currency' => $invoice['currency'],
    'transaction_id' => $reference,
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
     'payment_method'=>'Paystack',
]);

// Mark invoice as paid
$db->query("UPDATE invoices SET payment_status = 'Paid', payment_method = 'Paystack' WHERE id = :id", [
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

// Show success message
$successMessage = '
  <h2>✅ Payment successful via Paystack</h2>
  <strong>Success</strong>: Invoice #' . $invoice['invoice_number'] . ' has been marked as paid.
  <a href="/AIS/invoice-views?id=' . $invoiceId . '&token=' . $invoice['public_token'] . '" class="btn btn-info btn-lg" target="_blank">
    <span class="icon-file-text2" aria-hidden="true"></span> View
  </a> &nbsp;&nbsp;
  <a href="/AIS/invoice-view?id=' . $invoiceId . '&token=' . $invoice['public_token'] . '" class="btn btn-orange btn-lg" target="_blank">
    <span class="icon-earth" aria-hidden="true"></span> Public View
  </a>';

$_SESSION['success'] = $successMessage;

redirect('/AIS/invoice-view?id=' . $invoiceId . '&token=' . $token);
exit;
