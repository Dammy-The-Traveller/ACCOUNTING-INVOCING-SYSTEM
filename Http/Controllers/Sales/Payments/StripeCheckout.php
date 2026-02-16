<?php
use Core\App;
use Core\Database;
use Stripe\Stripe;
use Stripe\Checkout\Session;
// Initialize Stripe
Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

// Fetch invoice and customer
$db = App::resolve(Database::class);
$invoiceId = $_POST['invoice_id'] ?? null;
$token = $_POST['token'] ?? null;

$invoice = $db->query("SELECT * FROM invoices WHERE id = :id AND public_token = :token", [
    'id' => $invoiceId,
    'token' => $token
])->find();

if (!$invoice) {
    die('Invalid Invoice');
}

$amount = $invoice['grand_total'] * 100; // Convert to cents
       switch ($invoice['currency']) {
    case 1:
        $currency = 'gbp';
        break;
    case 2:
        $currency = 'eur';
        break;
    default:
        $currency = 'usd';
        break;
}

                
$session = Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' =>  $currency,
            'product_data' => [
                'name' => 'Invoice #' . $invoice['invoice_number'],
            ],
            'unit_amount' => round($amount * 1.03), // Including 3% fee
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'http://localhost/AIS/stripe-success?id='. $invoiceId . '&token=' . $invoice['public_token'],
    'cancel_url' => 'http://localhost/AIS/invoice-view?id='. $invoiceId . '&token=' . $invoice['public_token'],
]);

$session_id = $session->id;

// Optional: save to `transactions` or create a `stripe_sessions` table
$db->query("INSERT INTO stripe_sessions (invoice_id, session_id, created_at) VALUES (:invoice_id, :session_id, :created_at)", [
    'invoice_id' => $invoiceId,
    'session_id' => $session_id,
    'created_at' => date('Y-m-d H:i:s')
]);

header("Location: " . $session->url);
exit;
