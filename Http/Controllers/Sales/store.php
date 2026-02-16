<?php

use Core\App;
use Core\Database;
use Core\Validator;

$db = App::resolve(Database::class);

// CSRF token validation
if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    $_SESSION['error'] = 'Invalid CSRF token. Please try again.';
    redirect('/AIS/create');
}

// Ensure user is authenticated
if (!isset($_SESSION['user']['ID'])) {
    $_SESSION['error'] = 'You must be logged in to create an invoice.';
    redirect('/AIS/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate main invoice fields
    $invoice = [
        'customer_id' => filter_var($_POST['customer_id'] ?? '', FILTER_SANITIZE_NUMBER_INT),
        'invoice_number' => htmlspecialchars(trim($_POST['invocieno'] ?? '')),
        'reference' => htmlspecialchars(trim($_POST['refer'] ?? '')),
        'invoice_date' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['invoicedate'] ?? ''))),
        'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['invocieduedate'] ?? ''))),
        'tax_format' => htmlspecialchars(trim($_POST['taxformat'] ?? 'off')),
        'discount_format' => htmlspecialchars(trim($_POST['discountFormat'] ?? 'off')),
        'notes' => htmlspecialchars(trim($_POST['notes'] ?? '')),
        'subtotal' => floatval($_POST['subtotal'] ?? 0),
        'shipping' => floatval($_POST['shipping'] ?? 0),
        'grand_total' => floatval($_POST['total'] ?? 0),
        'currency' => htmlspecialchars(trim($_POST['mcurrency'] ?? 'USD')),
        'payment_terms' => htmlspecialchars(trim($_POST['pterms'] ?? '1')),
        'created_at' => date('Y-m-d H:i:s'),
        'payment_status' => 'Pending',
        'status' => 'active',
        'type' => 'invoices',
        'created_by' => (int) $_SESSION['user']['ID'],
        'updated_at' => null,
    ];

    // Validate required fields
    $required_fields = [
        'Customer ID' => $invoice['customer_id'],
        'Invoice Number' => $invoice['invoice_number'],
        'Invoice Date' => $invoice['invoice_date'],
        'Due Date' => $invoice['due_date'],
        'Subtotal' => $invoice['subtotal'],
        'Grand Total' => $invoice['grand_total'],
        'Currency' => $invoice['currency'],
        'Payment Terms' => $invoice['payment_terms'],
    ];

    foreach ($required_fields as $key => $value) {
        if (!Validator::string($value, 1) && $value !== 0) {
            $_SESSION['error'] = "Error: $key is required.";
            redirect('/AIS/create');
        }
    }

    // Additional validations
    if (!Validator::number($invoice['customer_id'], 1)) {
        $_SESSION['error'] = 'Invalid customer ID.';
        redirect('/AIS/create');
    }

    // Verify customer exists
    $customer = $db->query('SELECT id FROM customers WHERE id = :id', ['id' => $invoice['customer_id']])->find();
    if (!$customer) {
        $_SESSION['error'] = 'Selected customer does not exist.';
        redirect('/AIS/create');
    }

    if (!Validator::date($invoice['invoice_date']) || !Validator::date($invoice['due_date'])) {
        $_SESSION['error'] = 'Invalid invoice or due date format.';
        redirect('/AIS/create');
    }

    if ($invoice['subtotal'] < 0 || $invoice['grand_total'] < 0 || $invoice['shipping'] < 0) {
        $_SESSION['error'] = 'Subtotal, grand total, and shipping cannot be negative.';
        redirect('/AIS/create');
    }

    // Validate currency and payment terms
    $valid_currencies = [1, 2, 3];
    if (!in_array($invoice['currency'], $valid_currencies)) {
        $_SESSION['error'] = 'Invalid currency selected.';
        redirect('/AIS/create');
    }

    $valid_payment_terms = ['1', '2', '3'];
    if (!in_array($invoice['payment_terms'], $valid_payment_terms)) {
        $_SESSION['error'] = 'Invalid payment terms selected.';
        redirect('/AIS/create');
    }

    // Validate invoice items arrays
    $item_arrays = [
        'product_name' => $_POST['product_name'] ?? [],
        'product_qty' => $_POST['product_qty'] ?? [],
        'product_tax' => $_POST['product_tax'] ?? [],
        'taxa' => $_POST['taxa'] ?? [],
        'product_discount' => $_POST['product_discount'] ?? [],
        'product_subtotal' => $_POST['product_subtotal'] ?? [],
        'product_description' => $_POST['product_description'] ?? [],
        'pid' => $_POST['pid'] ?? [],
        'product_price' => $_POST['product_price'] ?? [],
        'disca' => $_POST['disca'] ?? [],
    ];

    $array_lengths = array_map('count', $item_arrays);
    if (count(array_unique($array_lengths)) !== 1 || empty($item_arrays['product_name'])) {
        $_SESSION['error'] = 'Invalid or incomplete invoice items data.';
        redirect('/AIS/create');
    }

    try {
        // Start database transaction
        $db->beginTransaction();

        // Insert into `invoices`
        $db->query("INSERT INTO invoices (
            customer_id, invoice_number, reference, invoice_date, due_date,
            tax_format, discount_format, notes, subtotal, shipping, grand_total,
            currency, payment_terms, created_at, payment_status, status, type,
            created_by, updated_at
        ) VALUES (
            :customer_id, :invoice_number, :reference, :invoice_date, :due_date,
            :tax_format, :discount_format, :notes, :subtotal, :shipping, :grand_total,
            :currency, :payment_terms, :created_at, :payment_status, :status, :type,
            :created_by, :updated_at
        )", $invoice);

        $invoiceId = $db->lastInsertId();

        // Insert invoice items
        foreach ($item_arrays['product_name'] as $index => $name) {
            $item = [
                'invoice_id' => $invoiceId,
                'product_name' => htmlspecialchars(trim($name)),
                'quantity' => floatval($item_arrays['product_qty'][$index]),
                'tax_percent' => floatval($item_arrays['product_tax'][$index]),
                'tax_amount' => floatval($item_arrays['taxa'][$index]),
                'discount' => floatval($item_arrays['product_discount'][$index]),
                'subtotal' => floatval($item_arrays['product_subtotal'][$index]),
                'product_description' => htmlspecialchars(trim($item_arrays['product_description'][$index])),
                'created_at' => date('Y-m-d H:i:s'),
                'product_id' => intval($item_arrays['pid'][$index]),
                'type' => 'invoices',
                'price' => floatval($item_arrays['product_price'][$index]),
                'discount_amount' => floatval($item_arrays['disca'][$index]),
            ];

            // Validate item
            if (!Validator::string($item['product_name'], 1) ||
                !Validator::number($item['quantity'], 0) ||
                !Validator::number($item['tax_percent'], 0) ||
                !Validator::number($item['tax_amount'], 0) ||
                !Validator::number($item['discount'], 0) ||
                !Validator::number($item['subtotal'], 0) ||
                !Validator::number($item['price'], 0) ||
                !Validator::number($item['discount_amount'], 0)) {
                throw new Exception('Invalid item data at index ' . $index);
            }

            // Insert into `invoice_items`
            $db->query("INSERT INTO invoice_items (
                invoice_id, product_name, quantity, tax_percent, tax_amount, discount,
                subtotal, product_description, created_at, product_id, type, price, discount_amount
            ) VALUES (
                :invoice_id, :product_name, :quantity, :tax_percent, :tax_amount, :discount,
                :subtotal, :product_description, :created_at, :product_id, :type, :price, :discount_amount
            )", $item);
        }

        // Generate and save public token
        $token = sha1($invoiceId . uniqid('', true));
        $db->query("UPDATE invoices SET public_token = :token WHERE id = :id", [
            'token' => $token,
            'id' => $invoiceId
        ]);

        // Commit transaction
        $db->commit();

        // Success message
        $_SESSION['success'] = '
            <strong>Success</strong>: Invoice has been created successfully!
            <a href="/AIS/invoice-views?id=' . htmlspecialchars($invoiceId) . '&token=' . htmlspecialchars($token) . '" class="btn btn-info btn-lg" target="_blank">
                <span class="icon-file-text2" aria-hidden="true"></span> View
            </a> &nbsp;&nbsp;
            <a href="/AIS/invoice-view?id=' . htmlspecialchars($invoiceId) . '&token=' . htmlspecialchars($token) . '" class="btn btn-orange btn-lg" target="_blank">
                <span class="icon-earth" aria-hidden="true"></span> Public View
            </a>';

        redirect('/AIS/create');
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        $_SESSION['error'] = 'Failed to create invoice: ' . htmlspecialchars($e->getMessage());
        redirect('/AIS/create');
    }
} else {
    $_SESSION['error'] = 'Invalid request method.';
    redirect('/AIS/create');
}