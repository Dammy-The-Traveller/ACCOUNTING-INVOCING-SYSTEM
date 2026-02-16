<?php

use Core\App;
use Core\Database;
$db = App::resolve(Database::class);
$invoiceId = $_POST['invoice_id'] ?? null;
$token = $_POST['token'] ?? null;

if (!$invoiceId || !$token) {
    die("Invalid request");
}

// Fetch invoice
$invoice = $db->query("SELECT * FROM invoices WHERE id = :id AND public_token = :token", [
    'id' => $invoiceId,
    'token' => $token
])->find();

if (!$invoice) {
    die("Invoice not found");
}

$customerId = $invoice['customer_id'] ?? null;
if (!$customerId) {
    die("Customer ID is missing in the invoice");
}

$customer = $db->query("SELECT email FROM customers WHERE id = :id", [
    'id' => $customerId,
])->find();

// Prepare Paystack request
$amountInKobo = intval(($invoice['grand_total'] * 1.03) * 100); // Including 3% fee
$email = $customer['email'] ?? 'info@dtt.com'; // You must have this in DB

$callbackUrl = 'http://localhost/AIS/paystack-success'; // Replace with your live/public URL if needed

$data = [
    'email' => $email,
    'amount' => $amountInKobo,
    'metadata' => [
        'invoice_id' => $invoice['id'],
        'token' => $invoice['public_token'],
    ],
    'callback_url' => $callbackUrl
];

// Send init request to Paystack
$paystackSecretKey = $_ENV['PAYSTACK_SECRET_KEY'] ?? 'sk_test_0520fdb73d0f2164c6d37bbc3a229cc7887a4447';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/initialize");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $paystackSecretKey",
    "Content-Type: application/json",
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if ($httpCode === 200 && $result['status']) {
    $authorizationUrl = $result['data']['authorization_url'];
    header('Location: ' . $authorizationUrl);
    exit;
} else {
    echo "Payment initialization failed.";
    // echo "<pre>";
    // print_r($result);
    // echo "</pre>";
}
