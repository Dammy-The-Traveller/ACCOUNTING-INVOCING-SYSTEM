<?php 
namespace Http\Controllers\Sales\RecurSales;
use Core\App;
use Core\Database;
use Dompdf\Dompdf;
use Exception; 
use DateTime;
use DateInterval;

class RecurringSales {
public function dashboard()
{
    
   views('Sales/RecurSales/dashboard.view.php');
}
public function create()
{
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
    views('Sales/RecurSales/create.view.php', [
    'invoice' => $invoice,
    'customer' => $customer,
    'items' => $items,
    'products' => $products,
    'warehouse'=> $warehouses
]);
exit;
}
 $warehouses = $db->query("SELECT * FROM warehouses WHERE status = 'active' ORDER BY name ASC", [
        ])->get();
   views('Sales/RecurSales/create.view.php',[
    'warehouses'=> $warehouses
   ]);
}
public function store()

    {
      $db = App::resolve(Database::class);

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die("<script>alert('Invalid CSRF token'); window.history.back();</script>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize main invoice fields
    $invoice = [
        'customer_id' => filter_var($_POST['customer_id'], FILTER_SANITIZE_NUMBER_INT),
        'invoice_number' => htmlspecialchars(trim($_POST['invocieno'])),
        'reference' => htmlspecialchars(trim($_POST['refer'])),
        'invoice_date' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['invoicedate']))),
        'due_date'    => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['invocieduedate']))),
        'tax_format' => htmlspecialchars(trim($_POST['taxformat'])),
        'discount_format' => htmlspecialchars(trim($_POST['discountFormat'])),
        'recurring_times'=> filter_var($_POST['recurring_times'], FILTER_SANITIZE_NUMBER_INT),
        'notes' => htmlspecialchars(trim($_POST['notes'])),
        'subtotal' => floatval($_POST['subtotal']),
        'shipping' => floatval($_POST['shipping']),
        'grand_total' => floatval($_POST['total']),
        'currency' => htmlspecialchars(trim($_POST['mcurrency'])),
        'payment_terms' => htmlspecialchars(trim($_POST['pterms'])),
        'created_at' => date('Y-m-d H:i:s'),
        'payment_status'=> 'Pending',
        'recurring_period'=>clean($_POST['reccur']),
        'end_date' => '', // This will be calculated later
        'status'=>clean($_POST['status']),
        'type' => 'recurring',
        'created_by' => $_SESSION['user']['ID'],
    ];


    // Step 1: Dates
$start_date = new DateTime($invoice['invoice_date']);
$due_date   = new DateTime($invoice['due_date']);
$term_interval = $start_date->diff($due_date); // e.g., 5 days

// Step 2: Recurring period + times
$recurring_period = $invoice['recurring_period']; // e.g., '1 month'
$recurring_times = intval($invoice['recurring_times']);

// Step 3: Calculate final occurrence date
$final_invoice_date = clone $start_date;
for ($i = 1; $i < $recurring_times; $i++) {
    $final_invoice_date->add(DateInterval::createFromDateString($recurring_period));
}

// Step 4: Apply payment term to get final due date
$final_due_date = clone $final_invoice_date;
$final_due_date->add($term_interval);

// Assign to $invoice array
$invoice['end_date'] = $final_due_date->format('Y-m-d');


$required_fields = [
    'Customer ID'       => $invoice['customer_id'],
    'Invoice Number'    => $invoice['invoice_number'],
    'Invoice Date'      => $invoice['invoice_date'],
    'Due Date'          => $invoice['due_date'],
    'Subtotal'          => $invoice['subtotal'],
    'Grand Total'       => $invoice['grand_total'],
    'Currency'          => $invoice['currency'],
    'Payment Terms'     => $invoice['payment_terms'],
];

foreach ($required_fields as $key => $value) {
    if (empty($value) && $value !== '0') {
        die("<script>alert('Error: $key is required. Please fill in all required fields.'); window.history.back();</script>");
    }
}


    // Insert into `invoices`
    $db->query("INSERT INTO invoices (
        customer_id, invoice_number, reference, invoice_date, due_date,
        tax_format, discount_format, recurring_times, notes, subtotal,
        shipping, grand_total, currency, payment_terms,created_at,payment_status, recurring_period, end_date, status, type, created_by
    ) VALUES (
        :customer_id, :invoice_number, :reference, :invoice_date, :due_date,
        :tax_format, :discount_format, :recurring_times, :notes, :subtotal,
        :shipping, :grand_total,:currency, :payment_terms, :created_at, :payment_status, :recurring_period, :end_date, :status,  :type, :created_by
    )", $invoice);

    $invoiceId = $db->lastInsertId();


    // Loop through invoice items
    foreach ($_POST['product_name'] as $index => $name) {
        $item = [
            'invoice_id' => $invoiceId,
            'product_name' => htmlspecialchars(trim($name)),
            'quantity' => floatval($_POST['product_qty'][$index]),
            'tax_percent' => floatval($_POST['product_tax'][$index]),
            'tax_amount' => floatval($_POST['taxa'][$index]),
            'discount' => floatval($_POST['product_discount'][$index]),
            'discount_amount' => floatval($_POST['disca'][$index]),
            'subtotal' => floatval($_POST['product_subtotal'][$index]),
            'product_description' => htmlspecialchars(trim($_POST['product_description'][$index])),
            'product_id' => intval($_POST['pid'][$index]),
            'type' => 'recurring',
            'price' => (float) $_POST['product_price'][$index],
             'created_at' => $now = date('Y-m-d H:i:s')
        ];

   
        // Insert into `invoice_items`
        $db->query("INSERT INTO invoice_items (
            invoice_id, product_name, quantity, tax_percent,
            tax_amount, discount,  subtotal, product_description, created_at, product_id, type, price, discount_amount
        ) VALUES (
            :invoice_id, :product_name, :quantity, :tax_percent,
            :tax_amount, :discount, :subtotal, :product_description, :created_at, :product_id, :type, :price, :discount_amount
        )", $item);
    }

  
      $token = sha1($invoiceId . uniqid('', true));

    // Save token if needed (optional)
    $db->query("UPDATE invoices SET public_token = :token WHERE id = :id", [
        'token' => $token,
        'id' => $invoiceId
    ]);

    // Success message (displayed on frontend)
    $successMessage = '
      <strong>Success</strong>: Invoice has been created successfully!
      <a href="/AIS/recurring-views?id=' . $invoiceId.'" class="btn btn-info btn-lg" target="_blank">
        <span class="icon-file-text2" aria-hidden="true"></span> View
      </a> &nbsp;&nbsp;';

    $_SESSION['success'] = $successMessage;
    redirect('/AIS/recur-create'); // back to invoice form or list
}

    }

    public function show()
    /**
     * Handles the logic for the invoice functionality within the Sales public invoice controller.
     *
     * This section of code is responsible for managing invoice-related operations,
     * such as creating, viewing, or processing invoices in the public invoice context.
     *
     * @package AIS\Http\Controllers\Sales\publicinvoice
     */
    {
        $db = App::resolve(Database::class);
        $invoiceId = $_GET['id'] ?? 0;
        if (!$invoiceId) {
            die("<script>alert('Invalid Token'); window.history.back();</script>");
        }

        // Fetch the invoice with token verification
        $invoice = $db->query("SELECT * FROM invoices WHERE id = :id AND type = :type", [
            /**
             * The unique identifier for the invoice.
             *
             * @var int $invoiceId The ID of the invoice.
             */
            'id' => $invoiceId,
            'type' => 'recurring'
        ])->find();

        if (!$invoice) {
            die("<h3>Invoice not found or token is invalid.</h3>");
        }

        // Fetch customer info
        $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $invoice['customer_id']
        ])->find();

        // Fetch invoice items
        $items = $db->query("SELECT * FROM invoice_items WHERE invoice_id = :invoice_id AND type=:type", [
            'invoice_id' => $invoiceId,
            'type' => 'recurring'
        ])->get();

                       // Calculate totals for verification
$calculatedSubtotal = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $items));
$calculatedTax = array_sum(array_column($items, 'tax_amount'));
$calculatedDiscount = array_sum(array_column($items, 'discount_amount'));
$calculatedGrandTotal = $calculatedSubtotal + $calculatedTax - $calculatedDiscount + ($invoice['shipping'] ?? 0);

// Verify calculations
if ($calculatedSubtotal != $invoice['subtotal'] || $calculatedGrandTotal != $invoice['grand_total']) {
    // Handle discrepancy (e.g., log error, update invoice)
    error_log("Calculation mismatch: Subtotal $calculatedSubtotal vs {$invoice['subtotal']}, Grand Total $calculatedGrandTotal vs {$invoice['grand_total']}");
}
        // Pass to view
        return views('Sales/Recursales/invoice.view.php', [
            'invoice' => $invoice,
            'invoiceId' => $invoiceId,
            'token' => $invoice['public_token'] ?? null,
            'customer' => $customer,
            'items' => $items,
            'calculatedSubtotal' => $calculatedSubtotal,
            'calculatedTax' => $calculatedTax,
            'calculatedDiscount' => $calculatedDiscount,
            'calculatedGrandTotal' => $calculatedGrandTotal
        ]);
    }


   public function update_status(){
    $status = $_POST['status'] ?? null;
    $invoiceId = $_POST['tid'] ?? null;

    if (!$status || !$invoiceId) {
        abort(400);
        die("<script>alert('Invoice does not exist. Try again.'); window.history.back();</script>");
      
    }

     $db = App::resolve(Database::class);
    try {
        $db->beginTransaction();

        // Update invoices table
     $affecteds =   $db->query("UPDATE invoices SET payment_status = ? WHERE id = ? LIMIT 1", [$status, $invoiceId]);

           if ($affecteds->rowCount() === 0) {
    $db->rollBack();
    die("<script>alert('Invoice does not exist. Try again.'); window.history.back();</script>");
}

        // Update payments table if entry exists
     $affected =   $db->query("UPDATE payments SET status = ? WHERE invoice_id = ? LIMIT 1", [$status, $invoiceId]);

        if ($affected->rowCount() === 0) {
        $db->commit();
            $successMessage = '
      <strong>Error</strong>: No payment has been recorded for this invoice.!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/recurring-views?id='.$invoiceId.''); 
      exit;
}
        $db->commit();
            $successMessage = '
      <strong>Success</strong>: Details updated successfully!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/recurring-views?id='.$invoiceId.''); 
      exit;
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update status']);
    }
}

   public function update_recur_status()
{
   
    $status = $_POST['status'] ?? null;
    $invoiceId = $_POST['id'] ?? null;

    if (!$status || !$invoiceId) {
        abort(400);
        die("<script>alert('Invoice does not exist. Try again.'); window.history.back();</script>");
      
    }

     $db = App::resolve(Database::class);
    try {
        $db->beginTransaction();

        // Update invoices table
     $affecteds =   $db->query("UPDATE invoices SET status = ? WHERE id = ? LIMIT 1", [$status, $invoiceId]);

           if ($affecteds->rowCount() === 0) {
    $db->rollBack();
    die("<script>alert('Invoice does not exist. Try again.'); window.history.back();</script>");
}

   
        $db->commit();
            $successMessage = '
      <strong>Success</strong>: Details updated successfully!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/recurring-views?id='.$invoiceId.''); 
      exit;
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update status']);
    }
}

public function update()
{
   
   
$db = App::resolve(Database::class);

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die("<script>alert('Invalid CSRF token'); window.history.back();</script>");
}

// var_dump($_POST);
// exit;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get existing invoice ID
    $invoiceId = intval($_POST['id'] ?? 0);
    if ($invoiceId <= 0) {
        die("<script>alert('Invalid Invoice ID'); window.history.back();</script>");
    }

    // Sanitize main invoice fields
    $invoice = [
        'invoice_id'=> $invoiceId,
        'customer_id' => filter_var($_POST['customer_id'], FILTER_SANITIZE_NUMBER_INT),
        'invoice_number' => htmlspecialchars(trim($_POST['invocieno'])),
        'reference' => htmlspecialchars(trim($_POST['refer'])),
        'invoice_date' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['invoicedate']))),
        'due_date'    => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['invocieduedate']))),
        'tax_format' => htmlspecialchars(trim($_POST['taxformat'])),
        'discount_format' => htmlspecialchars(trim($_POST['discountFormat'])),
        'recurring_times'=> filter_var($_POST['recurring_times'], FILTER_SANITIZE_NUMBER_INT),
        'notes' => htmlspecialchars(trim($_POST['notes'])),
        'subtotal' => floatval($_POST['subtotal']),
        'shipping' => floatval($_POST['shipping']),
        'grand_total' => floatval($_POST['total']),
        'currency' => htmlspecialchars(trim($_POST['mcurrency'])),
        'payment_terms' => htmlspecialchars(trim($_POST['pterms'])),
        'payment_status'=> 'Pending',
        'recurring_period'=>clean($_POST['reccur']),
        'end_date' => '', // This will be calculated later
        'status'=>clean($_POST['status']),
        'type' => 'recurring',
        'updated_at' => date('Y-m-d H:i:s'),
        'updated_by' => $_SESSION['user']['ID'],
    ];


    // Step 1: Dates
$start_date = new DateTime($invoice['invoice_date']);
$due_date   = new DateTime($invoice['due_date']);
$term_interval = $start_date->diff($due_date); // e.g., 5 days

// Step 2: Recurring period + times
$recurring_period = $invoice['recurring_period']; // e.g., '1 month'
$recurring_times = intval($invoice['recurring_times']);

// Step 3: Calculate final occurrence date
$final_invoice_date = clone $start_date;
for ($i = 1; $i < $recurring_times; $i++) {
    $final_invoice_date->add(DateInterval::createFromDateString($recurring_period));
}

// Step 4: Apply payment term to get final due date
$final_due_date = clone $final_invoice_date;
$final_due_date->add($term_interval);

// Assign to $invoice array
$invoice['end_date'] = $final_due_date->format('Y-m-d');

    // Validate required fields
    $required_fields = [
        'Customer ID'       => $invoice['customer_id'],
        'Invoice Number'    => $invoice['invoice_number'],
        'Invoice Date'      => $invoice['invoice_date'],
        'Due Date'          => $invoice['due_date'],
        'Subtotal'          => $invoice['subtotal'],
        'Grand Total'       => $invoice['grand_total'],
        'Currency'          => $invoice['currency'],
        'Payment Terms'     => $invoice['payment_terms'],
    ];

    foreach ($required_fields as $key => $value) {
        if (empty($value) && $value !== '0') {
            die("<script>alert('Error: $key is required. Please fill in all required fields.'); window.history.back();</script>");
        }
    }

    // Update `invoices` table
$db->query("UPDATE invoices SET
    customer_id = :customer_id,
    invoice_number = :invoice_number,
    reference = :reference,
    invoice_date = :invoice_date,
    due_date = :due_date,
    tax_format = :tax_format,
    discount_format = :discount_format,
    recurring_times = :recurring_times,
    notes = :notes,
    subtotal = :subtotal,
    shipping = :shipping,
    grand_total = :grand_total,
    currency = :currency,
    payment_terms = :payment_terms,
    payment_status = :payment_status,
    recurring_period = :recurring_period,
    end_date = :end_date,
    status = :status,
    type = :type,
    updated_at = :updated_at,
    updated_by = :updated_by
WHERE id = :invoice_id
", $invoice);




    // Remove old invoice_items first (optional, or handle updates if needed)
    $db->query("DELETE FROM invoice_items WHERE invoice_id = ?", [$invoiceId]);

    // Insert updated invoice items
    foreach ($_POST['product_name'] as $index => $name) {
        $item = [
            'invoice_id' => $invoiceId,
            'product_name' => htmlspecialchars(trim($name)),
            'quantity' => floatval($_POST['product_qty'][$index]),
            'tax_percent' => floatval($_POST['product_tax'][$index]),
            'tax_amount' => floatval($_POST['taxa'][$index]),
            'discount' => floatval($_POST['product_discount'][$index]),
            'discount_amount' => floatval($_POST['disca'][$index]),
            'subtotal' => floatval($_POST['product_subtotal'][$index]),
            'product_description' => htmlspecialchars(trim($_POST['product_description'][$index])),
            'product_id' => intval($_POST['pid'][$index]),
            'type' => 'recurring',
            'price' => (float) $_POST['product_price'][$index],
             'created_at' => $now = date('Y-m-d H:i:s')
        ];

      $db->query("INSERT INTO invoice_items (
            invoice_id, product_name, quantity, tax_percent,
            tax_amount, discount,  subtotal, product_description, created_at, product_id, type, price, discount_amount
        ) VALUES (
            :invoice_id, :product_name, :quantity, :tax_percent,
            :tax_amount, :discount, :subtotal, :product_description, :created_at, :product_id, :type, :price, :discount_amount
        )", $item);
    }

    // Optionally regenerate the public token
    $token = sha1($invoiceId . uniqid('', true));
    $db->query("UPDATE invoices SET public_token = :token WHERE id = :id", [
        'token' => $token,
        'id' => $invoiceId
    ]);

    // Success message
    $successMessage = '
      <strong>Success</strong>: Invoice has been updated successfully!
      <a href="/AIS/recurring-views?id=' . $invoiceId . '" class="btn btn-info btn-lg" target="_blank">
        <span class="icon-file-text2" aria-hidden="true"></span> View
      </a> &nbsp;&nbsp;';

    $_SESSION['success'] = $successMessage;
    redirect('/AIS/recur-create');
}
}

public function manage() {
    $db = App::resolve(Database::class);

    $Totalresult = $db->query("SELECT COUNT(*) AS total_recurring FROM invoices WHERE type = 'recurring'")->find();
  $totalRecurring = $Totalresult ? $Totalresult['total_recurring'] : 0;

 $result = $db->query("SELECT COUNT(*) AS total_stopped FROM invoices WHERE type = 'recurring' AND status = 'paused' || status = 'cancelled'")->find();
  $totalStopped = $result ? $result['total_stopped'] : 0;

   $recurring = $db->query("SELECT COUNT(*) AS recurring FROM invoices WHERE type = 'recurring' AND status = 'active' ")->find();
  $Recurring = $recurring ? $recurring['recurring'] : 0;
    // Pass it as a variable to the view
    views('Sales/RecurSales/manage.view.php', [
        'totalRecurring' => $totalRecurring,
        'totalStopped' => $totalStopped,
        'Recurring' => $Recurring
    ]);
}

public function ajaxList()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    // Datatables GET params
    $draw = $_GET['draw'] ?? 1;
    $start = $_GET['start'] ?? 0;
    $length = $_GET['length'] ?? 10;
    $searchValue = $_GET['search']['value'] ?? '';

    // Count total records
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM invoices";
    $totalRecords = $db->query($totalRecordsQuery)->find()['total'];

    // Main query
    $baseQuery = "FROM invoices
                  LEFT JOIN customers ON customers.id = invoices.customer_id";

    // Apply search filter if present
    $where = "";
    $params = [];
    if (!empty($searchValue)) {
        $where = " WHERE 
            invoices.invoice_number LIKE :search 
            OR customers.name LIKE :search 
            OR invoices.payment_status LIKE :search";

        $params['search'] = '%' . $searchValue . '%';
    }

    // Count filtered
    $filteredQuery = "SELECT COUNT(*) as total " . $baseQuery . $where;
    $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];

    // Fetch paginated and filtered data
 $dataQuery = "SELECT 
                invoices.invoice_number, 
                invoices.due_date, 
                invoices.status, 
                invoices.grand_total, 
                invoices.payment_status, 
                invoices.id, 
                invoices.public_token, 
                customers.name as customer_name
              " . $baseQuery . " 
              WHERE invoices.type = 'recurring' " . $where . " 
              ORDER BY invoices.id DESC 
              LIMIT $start, $length";


    $data = $db->query($dataQuery, $params)->get();

    $output = [];
    $counter = $start + 1;

    foreach ($data as $row) {
      
    switch (strtolower($row['status'])) {
        case 'active':
            $status = 'Active';
            $statusClass = 'st-paid';
            break;
        case 'paused':
            $status = 'Paused';
            $statusClass = 'st-partial';
            break;
        case 'cancelled':
            $status = 'cancelled';
            $statusClass = 'st-canceled';
            break;
        default:
            $status = 'Active';
            $statusClass = 'st-paid';
            break;
    }
    switch (strtolower($row['payment_status'])) {
        case 'paid':
            $paymentClass = 'st-paid';
            break;
        case 'partial':
            $paymentClass = 'st-partial';
            break;
        case 'cancelled':
            $paymentClass = 'st-canceled';
            break;
        default:
            $paymentClass = 'st-due';
            break;
    }
        $output[] = [
            
            $counter++,
            $row['invoice_number'],
            $row['customer_name'],
            date('Y-m-d', strtotime($row['due_date'])),
              '<span class="' . $statusClass . '">' . ucfirst($status) . '</span>',
            number_format($row['grand_total'], 2),
           '<span class="' . $paymentClass . '">' . ucfirst($row['payment_status']) . '</span>',
            '<a href="/AIS/recurring-views?id='.$row['id'].'" class="btn btn-success btn-xs"><i class="icon-file-text"></i> View</a> &nbsp; <a href="/AIS/invoice-download?id='.$row['id'].'&token='.$row['public_token'].'" class="btn btn-info btn-xs" title="Download"><span class="icon-download"></span></a>&nbsp; &nbsp;<a href="#" data-object-id="'.$row['id'].'" class="btn btn-danger btn-xs delete-objects"><span class="icon-trash"></span></a>'
        ];
    }

    // Return JSON response in DataTables format
    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalFiltered,
        "data" => $output
    ]);
    exit;
}

public function delete()
{
    $db = App::resolve(Database::class);
 
    $id = $_GET['id'] ?? null;

   
    // Optionally validate the ID
    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid ID"]);
        exit;
    }

    try {
        $db->query("DELETE FROM invoices WHERE id = :id AND type=:type", ['id' => $id, 'type' => 'recurring']);

        // Optionally delete invoice items too
        $db->query("DELETE FROM invoice_items WHERE invoice_id = :id AND type=:type", ['id' => $id, 'type' => 'recurring']);

        echo json_encode(["message" => "Invoice deleted successfully"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Error deleting invoice"]);
    }
    exit;
}
}