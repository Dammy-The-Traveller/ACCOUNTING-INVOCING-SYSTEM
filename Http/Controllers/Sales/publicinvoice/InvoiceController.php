<?php 
namespace Http\Controllers\Sales\publicinvoice;
use Core\App;
use Core\Database;
use Dompdf\Dompdf;
use Exception; 
class InvoiceController {
public function generatePDF()
    {
    $db = App::resolve(Database::class);
        $invoiceId = $_GET['id'] ?? null;
        $token = $_GET['token'] ?? null;

        if (!$invoiceId || !$token) {
            die("<h3>Invalid Token.</h3>");
        }

        // Fetch the invoice with token verification
        $invoice = $db->query("SELECT * FROM invoices WHERE id = :id AND public_token = :token", [
            'id' => $invoiceId,
            'token' => $token
        ])->find();

        if (!$invoice) {
            die("<h3>Invoice not found or token is invalid.</h3>");
        }

        // Fetch customer info
        $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $invoice['customer_id']
        ])->find();

        // Fetch invoice items
        $items = $db->query("SELECT * FROM invoice_items WHERE invoice_id = :invoice_id", [
            'invoice_id' => $invoiceId
        ])->get();

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
        ob_start();
        include realpath(__DIR__ . '/../../../..') . "/views/Sales/public/pdf_template.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'Landscape');
        $dompdf->render();

        // Save PDF file to disk
        $pdfOutput = $dompdf->output();
       $pdfPath = realpath(__DIR__ . '/../../../..') . "/Public/invoices/invoice_{$invoiceId}_{$token}.pdf";
        file_put_contents($pdfPath, $pdfOutput);

        // Redirect to viewer
        header("Location: /AIS/invoice-viewer?id={$invoiceId}&token={$token}");
        exit;

        
}


public function showViewer()
{
    $invoiceId = $_GET['id'] ?? 18;
    $token = $_GET['token'] ?? null;
    include realpath(__DIR__ . '/../../../..') . "/views/Sales/public/pdf_viewer.php";
   
}

public function downloadPDF()
{
    $invoiceId = $_GET['id'] ?? null;
    $token = $_GET['token'] ?? null;

    if (!$invoiceId || !$token) {
        die('Invalid request.');
    }

    // Define PDF path
    $filePath = realpath(__DIR__ . '/../../../..') . "/Public/invoices/invoice_{$invoiceId}_{$token}.pdf";

    // If file doesn't exist, regenerate it
    if (!file_exists($filePath)) {
               $db = App::resolve(Database::class);

        // Fetch invoice
        $invoice = $db->query("SELECT * FROM invoices WHERE id = :id AND public_token = :token", [
            'id' => $invoiceId,
            'token' => $token
        ])->find();

        if (!$invoice) {
             die("<script>alert('Invoice does not exist. Try again.'); window.history.back();</script>");
        }

        // Fetch customer
        $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $invoice['customer_id']
        ])->find();

        // Fetch invoice items
        $items = $db->query("SELECT * FROM invoice_items WHERE invoice_id = :invoice_id", [
            'invoice_id' => $invoiceId
        ])->get();

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
        // Load HTML template (e.g., invoice-view.php) and pass invoice data
        ob_start();
        include realpath(__DIR__ . '/../../../..') . "/views/Sales/public/pdf_template.php";
        $html = ob_get_clean();

        // Generate PDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'Landscape');
        $dompdf->render();

        // Save PDF
        file_put_contents($filePath, $dompdf->output());
    }

    // Force download
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}


public function update_status()
{
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

        // Update payments table if entry exists
     $affected =   $db->query("UPDATE payments SET status = ? WHERE invoice_id = ? LIMIT 1", [$status, $invoiceId]);

        if ($affected->rowCount() === 0) {
    $db->rollBack();
    die("<script>alert('Invoice does not exist. Try again.'); window.history.back();</script>");
}
        $db->commit();
            $successMessage = '
      <strong>Success</strong>: Details updated successfully!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/invoice-views?id='.$invoiceId.''); 
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update status']);
    }
}

public function status()
{
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

       $db->query("UPDATE invoices SET status = ? WHERE id = ? LIMIT 1", [$status, $invoiceId]);
        // Update payments table if entry exists
     $affected =   $db->query("UPDATE payments SET status = ? WHERE invoice_id = ? LIMIT 1", [$status, $invoiceId]);

        if ($affected->rowCount() === 0) {
    $db->rollBack();
    die("<script>alert('Invoice does not exist. Try again.'); window.history.back();</script>");
}
        $db->commit();
            $successMessage = '
      <strong>Success</strong>: Details updated successfully!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/invoice-views?id='.$invoiceId.''); 
    } catch (Exception $e) {
        $db->rollBack();
        $successMessage = '
      <strong>Error</strong>: Details update failed!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/invoice-views?id='.$invoiceId.''); 
    }
}

public function cancel_status()
{
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

        // Update payments table if entry exists
     $affected =   $db->query("UPDATE payments SET status = ? WHERE invoice_id = ? LIMIT 1", [$status, $invoiceId]);

        if ($affected->rowCount() === 0) {
    $db->rollBack();
    die("<script>alert('Invoice does not exist. Try again.'); window.history.back();</script>");
}
        $db->commit();
      redirect('/AIS/invoice-views?id='.$invoiceId.''); 
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update status']);
    }
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
    $dataQuery = "SELECT invoices.invoice_number, invoices.created_at, invoices.grand_total, invoices.payment_status, invoices.id, invoices.public_token, customers.name as customer_name
                  " . $baseQuery . " 
              WHERE invoices.type = 'invoices' " . $where . " 
                  ORDER BY invoices.id DESC 
                  LIMIT $start, $length";

    $data = $db->query($dataQuery, $params)->get();

    $output = [];
    $counter = $start + 1;

    foreach ($data as $row) {
        switch (strtolower($row['payment_status'])) {
        case 'paid':
            $paymentClass = 'st-paid';
            break;
        case 'partial':
            $paymentClass = 'st-partial';
            break;
        case 'cancelled':
            $paymentClass = 'st-cancelled';
            break;
        default:
            $paymentClass = 'st-due';
            break;
    }
        $output[] = [
            $counter++,
            $row['invoice_number'],
            $row['customer_name'],
            date('Y-m-d', strtotime($row['created_at'])),
            number_format($row['grand_total'], 2),
            '<span class="' . $paymentClass . '">' . ucfirst($row['payment_status']) . '</span>',
            '<a href="/AIS/invoice-views?id='.$row['id'].'" class="btn btn-success btn-xs"><i class="icon-file-text"></i> View</a> &nbsp; <a href="/AIS/invoice-download?id='.$row['id'].'&token='.$row['public_token'].'" class="btn btn-info btn-xs" title="Download"><span class="icon-download"></span></a>&nbsp; &nbsp;<a href="#" data-object-id="'.$row['id'].'" class="btn btn-danger btn-xs delete-objects"><span class="icon-trash"></span></a>'
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
        $db->query("DELETE FROM invoices WHERE id = :id", ['id' => $id]);

        // Optionally delete invoice items too
        $db->query("DELETE FROM invoice_items WHERE invoice_id = :id", ['id' => $id]);

        echo json_encode(["message" => "Invoice deleted successfully"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Error deleting invoice"]);
    }
    exit;
}

public function update()
{
   
   
$db = App::resolve(Database::class);

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die("<script>alert('Invalid CSRF token'); window.history.back();</script>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get existing invoice ID
    $invoiceId = intval($_POST['id'] ?? 0);
    if ($invoiceId <= 0) {
        die("<script>alert('Invalid Invoice ID'); window.history.back();</script>");
    }

    // Sanitize main invoice fields
    $invoice = [
        'id' => $invoiceId,
        'customer_id' => filter_var($_POST['customer_id'], FILTER_SANITIZE_NUMBER_INT),
        'invoice_number' => htmlspecialchars(trim($_POST['invocieno'])),
        'reference' => htmlspecialchars(trim($_POST['refer'])),
        'invoice_date' => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['invoicedate']))),
        'due_date'    => date('Y-m-d', strtotime(str_replace('/', '-', $_POST['invocieduedate']))),
        'tax_format' => htmlspecialchars(trim($_POST['taxformat'])),
        'discount_format' => htmlspecialchars(trim($_POST['discountFormat'])),
        'notes' => htmlspecialchars(trim($_POST['notes'])),
        'subtotal' => floatval($_POST['subtotal']),
        'shipping' => floatval($_POST['shipping']),
        'grand_total' => floatval($_POST['total']),
        'currency' => htmlspecialchars(trim($_POST['mcurrency'])),
        'payment_terms' => htmlspecialchars(trim($_POST['pterms'])),
        'updated_at' => date('Y-m-d H:i:s'),
        'updated_by' => $_SESSION['user']['ID'],
    ];

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
        notes = :notes,
        subtotal = :subtotal,
        shipping = :shipping,
        grand_total = :grand_total,
        currency = :currency,
        payment_terms = :payment_terms,
        updated_at = :updated_at,
        updated_by = :updated_by
        WHERE id = :id
    ", $invoice);

    // Remove old invoice_items first (optional, or handle updates if needed)
    $db->query("DELETE FROM invoice_items WHERE invoice_id = ?", [$invoiceId]);

    // Insert updated invoice items
    foreach ($_POST['product_name'] as $index => $name) {
        $item = [
            'invoice_id' => $invoiceId,
            'product_name' => htmlspecialchars(trim($name)),
            'quantity' => floatval($_POST['product_qty'][$index]),
            'price' => floatval($_POST['product_price'][$index]),
            'tax_percent' => floatval($_POST['product_tax'][$index]),
            'tax_amount' => floatval($_POST['taxa'][$index]),
            'discount' => floatval($_POST['product_discount'][$index]),
            'discount_amount' => floatval($_POST['disca'][$index]),
            'subtotal' => floatval($_POST['product_subtotal'][$index]),
            'product_id' => intval($_POST['pid'][$index]),
            'product_description' => htmlspecialchars(trim($_POST['product_description'][$index])),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $db->query("INSERT INTO invoice_items (
            invoice_id, product_name, quantity, price, tax_percent,
            tax_amount, discount, discount_amount, subtotal, product_id, product_description, created_at
        ) VALUES (
            :invoice_id, :product_name, :quantity, :price, :tax_percent,
            :tax_amount, :discount, :discount_amount, :subtotal, :product_id, :product_description, :created_at
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
      <a href="/AIS/invoice-views?id=' . $invoiceId . '" class="btn btn-info btn-lg" target="_blank">
        <span class="icon-file-text2" aria-hidden="true"></span> View
      </a> &nbsp;&nbsp;';

    $_SESSION['success'] = $successMessage;
    redirect('/AIS/create'); // or wherever you list the invoices
}
}
}
