<?php 
namespace Http\Controllers\Stock\purchase;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;
use Dompdf\Dompdf;
class Purchase{
  public function index()
{
    $db = App::resolve(Database::class);

    // Fetch dropdown data once
    
$purchase =[];
$supplier = [];
$items = [];
$warehouses = [];
$categories = [];
$warehouses = $db->query("SELECT * FROM warehouses WHERE status = 'active'")->get();
    $categories = $db->query("SELECT * FROM categories WHERE status = 'active'")->get();
    // Check if editing
    if (isset($_GET['id'])) {
        $purchaseId = $_GET['id'];

        if (!$purchaseId) {
            die("<script>alert('Invalid Purchase'); window.history.back();</script>");
        }

        // Fetch purchase
        $purchase = $db->query("SELECT * FROM purchases WHERE id = :id", [
            'id' => $purchaseId
        ])->find();

        if (!$purchase) {
            die("<script>alert('Purchase not found.'); window.history.back();</script>");
        }

        // Fetch supplier
        $supplier = $db->query("SELECT * FROM suppliers WHERE id = :id", [
            'id' => $purchase['supplier_id']
        ])->find();

        // Fetch items
        $items = $db->query("SELECT * FROM purchase_items WHERE purchase_id = :id", [
            'id' => $purchaseId
        ])->get();

      
      

        return views('Stock/purchase/index.view.php', [
            'purchase' => $purchase,
            'supplier' => $supplier,
            'items' => $items,
            'warehouses' => $warehouses,
            'categories' => $categories
        ]);
    }

    // New purchase
    return views('Stock/purchase/index.view.php', [
        'warehouses' => $warehouses,
        'categories' => $categories
    ]);
}



    public function supplierStore(){
        $db = App::resolve(Database::class);

        
        $supplier = [
            'name'             => htmlspecialchars(trim($_POST['name'])),
             'supplier_code'       => htmlspecialchars(trim($_POST['supplier_code'])),
            'phone'            => htmlspecialchars(trim($_POST['phone'])),
            'email'            => htmlspecialchars(trim($_POST['email'])),
            'address'          => htmlspecialchars(trim($_POST['address'])),
            'city'             => htmlspecialchars(trim($_POST['city'])),
            'region'           => htmlspecialchars(trim($_POST['region'])),
            'country'          => htmlspecialchars(trim($_POST['country'])),
            'postbox'          => htmlspecialchars(trim($_POST['postbox'])),
            'taxid'            => htmlspecialchars(trim($_POST['taxid']))
        ];
        // var_dump($supplier);
        // exit;
        // Validate required fields
        $errors = [];
        
        if (!Validator::string($supplier['name'], 2, 255)) {
            $errors['name'] = 'Please provide a valid name.';
        }
        
        if (!Validator::email($supplier['email'])) {
            $errors['email'] = 'Please provide a valid email address.';
        }
        
        if (!Validator::string($supplier['address'], 2, 255)) {
            $errors['address'] = 'Please provide a valid billing address.';
        }
            if (!Validator::string($supplier['phone'], 7, 20)) {
            $errors['phone'] = 'Phone number is required and should be valid.';
        }
        
        if (! empty($errors)) {
            return views('Stock/purchase/modal.view.php', [
                'errors' => $errors
            ]);
        }
        
        // Insert into DB
      $inserted = $db->query("INSERT INTO suppliers (name, supplier_code, phone, email, address, city, region, country, postbox, taxid) VALUES (
                :name, :supplier_code, :phone, :email, :address, :city, :region, :country, :postbox, :taxid
            )", $supplier);

if ($inserted) {
    $_SESSION['success'] = '
      <strong>Success</strong>: Supplier has been created successfully!';
    redirect('/AIS/purchase');
    exit;
} else {
    $_SESSION['error'] = '
      <strong>Error</strong>: Failed to add supplier.';
    redirect('/AIS/purchase');
    exit;
}
     
    }

    public function purchaseStore(){
        
        $db = App::resolve(Database::class);

            // ddd($_POST);
            // exit;
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $successMessage = '
    <strong>Error</strong>: Method Not Allowed';
  $_SESSION['success'] = $successMessage;
    redirect('/AIS/purchase'); 
    exit;
}

// CSRF check if enabled
// if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
//     die("<script>alert('INVALID ACCESS'); window.history.back();</script>");
// }

try {
    $db->beginTransaction();

    // var_dump($_POST);
    // exit;
    // 1. Collect and sanitize form data
    $supplier_id = (int)($_POST['supplier_id'] ?? 0);
    $warehouse_id = (int)($_POST['warehouses'] ?? 0);
    $category_id = (int)($_POST['category'] ?? 0);
    $invoice_no = htmlspecialchars(trim($_POST['invocieno'] ?? ''));
    $reference = htmlspecialchars(trim($_POST['refer'] ?? ''));
    $invoice_date = date('Y-m-d', strtotime(str_replace('-', '/', $_POST['invoicedate'] ?? date('Y-m-d'))));
    $due_date = date('Y-m-d', strtotime(str_replace('-', '/', $_POST['invocieduedate'] ?? date('Y-m-d'))));    
    $tax_format = $_POST['taxformat'] ?? 'on';
    $discount_format = $_POST['discountFormat'] ?? '%';
    $notes = htmlspecialchars(trim($_POST['notes'] ?? ''));
    $subtotal = (float)($_POST['subtotal'] ?? 0);
    $shipping = (float)($_POST['shipping'] ?? 0);
    $grand_total = (float)($_POST['total'] ?? 0);
    $update_stock = htmlspecialchars(trim($_POST['update_stock'] ?? 'yes'));
    $payment_terms = htmlspecialchars(trim($_POST['pterms'] ?? '1'));
    $created_by = $_SESSION['user']['ID'];
    $payment_status ='pending';



   // 2. Validate required fields
$required_fields = [
    'Supplier'         => $supplier_id,
    'Warehouse'        => $warehouse_id,
    'Invoice Number'   => $invoice_no,
    'Reference Number' => $reference,
    'Purchase Date'    => $invoice_date,
    'Due Date'         => $due_date,
    'Sub Total'        => $subtotal,
    'Total Amount'     => $grand_total
];

foreach ($required_fields as $key => $value) {
    if (empty($value) && $value !== "0" && $value !== 0) {
        die("<script>alert('Error: $key is required. Please fill in all required fields.'); window.history.back();</script>");
    }
}

// 3. Validate that at least one item is present
if (empty($_POST['product_name']) || !is_array($_POST['product_name']) || count(array_filter($_POST['product_name'])) === 0) {
    die("<script>alert('Error: At least one product item is required.'); window.history.back();</script>");
}

// 4. Validate each item field
$line_items = count($_POST['product_name']);
for ($i = 0; $i < $line_items; $i++) {
    $item_required_fields = [
        'Product Name'       => $_POST['product_name'][$i] ?? '',
        'Quantity'           => $_POST['product_qty'][$i] ?? '',
        'Price'              => $_POST['product_price'][$i] ?? '',
        'Tax Percent'        => $_POST['product_tax'][$i] ?? '',
        'Tax Amount'         => $_POST['taxa'][$i] ?? '',
        'Discount Percent'   => $_POST['product_discount'][$i] ?? '',
        'Discount Amount'    => $_POST['disca'][$i] ?? '',
        'Subtotal'           => $_POST['product_subtotal'][$i] ?? ''
    ];

    foreach ($item_required_fields as $key => $value) {
        if (empty($value) && $value !== "0" && $value !== 0) {
            die("<script>alert('Error: $key is required for item " . ($i + 1) . ". Please check all item rows.'); window.history.back();</script>");
        }
    }
}
    // 2. Insert into purchases table
    $db->query("INSERT INTO purchases 
        (supplier_id, warehouse_id, category_id, invoice_no, reference, invoice_date, due_date, tax_format, discount_format, notes, subtotal, shipping, grand_total, update_stock, payment_terms, created_at, created_by, payment_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)", [
        $supplier_id,
        $warehouse_id,
        $category_id,
        $invoice_no,
        $reference,
        $invoice_date,
        $due_date,
        $tax_format,
        $discount_format,
        $notes,
        $subtotal,
        $shipping,
        $grand_total,
        $update_stock,
        $payment_terms,
        $created_by,
        $payment_status
    ]);

    $purchase_id = $db->lastInsertId();

    // 3. Handle line items
    $names = $_POST['product_name'] ?? [];
$quantities = $_POST['product_qty'] ?? [];
$prices = $_POST['product_price'] ?? [];
$taxes = $_POST['product_tax'] ?? [];
$tax_amounts = $_POST['taxa'] ?? [];
$discounts = $_POST['product_discount'] ?? [];
$discount_amounts = $_POST['disca'] ?? [];
$subtotals = $_POST['product_subtotal'] ?? [];
$descriptions = $_POST['product_description'] ?? [];
$product_ids = $_POST['pid'] ?? [];


foreach ($names as $index => $name) {
    $db->query("INSERT INTO purchase_items 
        (purchase_id, product_id, product_name, quantity, price, tax_percent, tax_amount, discount, discount_amount, subtotal, product_description)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
        $purchase_id,
        (int)($product_ids[$index]),
        htmlspecialchars(trim($name)),
        (int)($quantities[$index]),
        (float)($prices[$index]),
        (float)($taxes[$index]),
        (float)($tax_amounts[$index]),
        (float)($discounts[$index]),
        (float)($discount_amounts[$index]),
        (float)($subtotals[$index]),
        htmlspecialchars(trim($descriptions[$index]))
    ]);

        if ($update_stock === 'yes') {
            // Step 1: Fetch the specific product row
            $stockUpdate = $db->query("SELECT * FROM products WHERE id = ? AND warehouse_id = ? AND category_id = ?", [
                $product_ids[$index],
                $warehouse_id,
                $category_id
            ])->find();
        
            if ($stockUpdate) {
                // Step 2: Update the main quantity (for this warehouse row)
                $new_qty = (int)$stockUpdate['quantity'] + (int)$quantities[$index];
        
                // Step 3: Decode the stock_by_warehouse JSON
                $stock_by_warehouse = json_decode($stockUpdate['stock_by_warehouse'], true) ?? [];
        
                // Step 4: Update this warehouse's quantity in the JSON
                if (isset($stock_by_warehouse[$warehouse_id])) {
                    $stock_by_warehouse[$warehouse_id] += (int)$quantities[$index];
                } else {
                    $stock_by_warehouse[$warehouse_id] = (int)$quantities[$index];
                }
        
                // Step 5: Re-encode the JSON
                $updated_stock_json = json_encode($stock_by_warehouse);
        
                // Step 6: Update the row
                $db->query("UPDATE products SET quantity = ?, stock_by_warehouse = ? WHERE id = ? AND warehouse_id = ? AND category_id = ?", [
                    $new_qty,
                    $updated_stock_json,
                    $product_ids[$index],
                    $warehouse_id,
                    $category_id
                ]);
            } else {
                die("<script>alert('Error: Product not found in this warehouse. Please add it first.'); window.history.back();</script>");
            }
        }
        
    }

    $db->commit();

    // 5. Redirect or return success
    $successMessage = '
    <strong>Success</strong>: Purchase has been created successfully!
    <a href="/AIS/purchase-views?id=' . $purchase_id . '" class="btn btn-info btn-lg" target="_blank">
      <span class="icon-file-text2" aria-hidden="true"></span> View
    </a>';
  $_SESSION['success'] = $successMessage;
    redirect('/AIS/purchase'); 
    exit;

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo "Error processing purchase: " . $e->getMessage();
}
    }

    public function purchaseView(){
        $db = App::resolve(Database::class);
        $purchase_id = $_GET['id']; 
        if (!filter_var($purchase_id, FILTER_VALIDATE_INT)) {
            die("<script>alert('Invalid purchase ID'); window.history.back();</script>");
        }
        $purchase = $db->query("SELECT * FROM purchases WHERE id = ?", [
            $purchase_id
        ])->find();

         if (!$purchase) {
            die("<h3>purchase not found or token is invalid.</h3>");
        }
            // Fetch customer info
        $supplier = $db->query("SELECT * FROM suppliers WHERE id = :id", [
            'id' => $purchase['supplier_id']
        ])->find();

        // Fetch purchase items
        $items = $db->query("SELECT * FROM purchase_items WHERE purchase_id = :purchase_id", [
            'purchase_id' => $purchase_id
        ])->get();

        $payment = $db->query("SELECT * FROM payments WHERE invoice_id = :id", [
            'id' => $purchase_id
        ])->find() ?? ['amount' => 0];

               // Calculate totals for verification
$calculatedSubtotal = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $items));
$calculatedTax = array_sum(array_column($items, 'tax_amount'));
$calculatedDiscount = array_sum(array_column($items, 'discount_amount'));
$calculatedGrandTotal = $calculatedSubtotal + $calculatedTax - $calculatedDiscount + ($purchase['shipping'] ?? 0);

// Verify calculations
if ($calculatedSubtotal != $purchase['subtotal'] || $calculatedGrandTotal != $purchase['grand_total']) {
    // Handle discrepancy (e.g., log error, update invoice)
    error_log("Calculation mismatch: Subtotal $calculatedSubtotal vs {$purchase['subtotal']}, Grand Total $calculatedGrandTotal vs {$purchase['grand_total']}");
}
        return views('Stock/purchase/view.view.php', [
            'purchase' => $purchase,
            'supplier' => $supplier,
            'items' => $items,
            'payment' => $payment,
            'calculatedSubtotal' => $calculatedSubtotal,
            'calculatedTax' => $calculatedTax,
            'calculatedDiscount' => $calculatedDiscount,
            'calculatedGrandTotal' => $calculatedGrandTotal
        ]);
    }

    public function updateStatus()
{
    $status = $_POST['status'] ?? null;
    $purchaseId = $_POST['tid'] ?? null;

    if (!$status || !$purchaseId) {
        abort(400);
        die("<script>alert('Purchase does not exist. Try again.'); window.history.back();</script>");
      
    }

     $db = App::resolve(Database::class);
    try {
        $db->beginTransaction();

        // Update purchases table
     $affecteds =   $db->query("UPDATE purchases SET payment_status = ? WHERE id = ? LIMIT 1", [$status, $purchaseId]);

        // Update payments table if entry exists
    //  $affected =   $db->query("UPDATE payments SET status = ? WHERE purchase_id = ? LIMIT 1", [$status, $purchaseId]);

//         if ($affected->rowCount() === 0) {
//     $db->rollBack();
//     die("<script>alert('purchase does not exist. Try again.'); window.history.back();</script>");
// }
        $db->commit();
            $successMessage = '
      <strong>Success</strong>: Details updated successfully!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/purchase-views?id='.$purchaseId.''); 
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update status']);
    }
}


public function generatePDF()
    {
    $db = App::resolve(Database::class);
        $purchaseId = $_GET['id'] ?? null;
        $payment = [];

        if (!$purchaseId) {
            die("<h3>Invalid Token.</h3>");
        }

        // Fetch the purchase with token verification
        $purchase = $db->query("SELECT * FROM purchases WHERE id = :id", [
            'id' => $purchaseId
        ])->find();

        if (!$purchase) {
            die("<h3>purchase not found or token is invalid.</h3>");
        }

        // Fetch customer info
        $supplier = $db->query("SELECT * FROM suppliers WHERE id = :id", [
            'id' => $purchase['supplier_id']
        ])->find();

        // Fetch purchase items
        $items = $db->query("SELECT * FROM purchase_items WHERE purchase_id = :purchase_id", [
            'purchase_id' => $purchaseId
        ])->get();

        $payment = $db->query("SELECT * FROM payments WHERE invoice_id = :id", [
            'id' => $purchaseId
        ])->find() ?? ['amount' => 0];

        $calculatedSubtotal = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $items));
$calculatedTax = array_sum(array_column($items, 'tax_amount'));
$calculatedDiscount = array_sum(array_column($items, 'discount_amount'));
$calculatedGrandTotal = $calculatedSubtotal + $calculatedTax - $calculatedDiscount + ($purchase['shipping'] ?? 0);

// Verify calculations
if ($calculatedSubtotal != $purchase['subtotal'] || $calculatedGrandTotal != $purchase['grand_total']) {
    // Handle discrepancy (e.g., log error, update invoice)
    error_log("Calculation mismatch: Subtotal $calculatedSubtotal vs {$purchase['subtotal']}, Grand Total $calculatedGrandTotal vs {$purchase['grand_total']}");
}
        ob_start();
        include realpath(__DIR__ . '/../../../..') . "/views/Stock/purchase/pdf_template.php";

        $html = ob_get_clean();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'Landscape');
        $dompdf->render();

        // Save PDF file to disk
        $pdfOutput = $dompdf->output();
       $pdfPath = realpath(__DIR__ . '/../../../..') . "/Public/purchases/purchase_{$purchaseId}.pdf";
       if (file_exists($pdfPath)) {
        unlink($pdfPath); // Remove previous version
    }
        file_put_contents($pdfPath, $pdfOutput);

        // Redirect to viewer
        header("Location: /AIS/purchase-viewer?id={$purchaseId}");
        exit;

        
}

public function showViewer()
{
    $purchaseId = $_GET['id'] ?? 0;
    include realpath(__DIR__ . '/../../../..') . "/views/Stock/purchase/pdf_viewer.php";
   
}

public function downloadPDF()
{
    $invoiceId = $_GET['id'] ?? null;

    if (!$invoiceId) {
        die('Invalid request.');
    }

    $pdfPath = realpath(__DIR__ . '/../../../..') . "/Public/purchases/purchase_{$invoiceId}.pdf";

    // Regenerate the PDF always (or you can wrap in a condition if preferred)
    $db = App::resolve(Database::class);

    $purchase = $db->query("SELECT * FROM purchases WHERE id = :id", [
        'id' => $invoiceId
    ])->find();

    if (!$purchase) {
        die("<h3>Purchase not found or token is invalid.</h3>");
    }

    $supplier = $db->query("SELECT * FROM suppliers WHERE id = :id", [
        'id' => $purchase['supplier_id']
    ])->find();

    $items = $db->query("SELECT * FROM purchase_items WHERE purchase_id = :purchase_id", [
        'purchase_id' => $invoiceId
    ])->get();

    $payment = $db->query("SELECT * FROM payments WHERE invoice_id = :id", [
        'id' => $invoiceId
    ])->find() ?? ['amount' => 0];

    $calculatedSubtotal = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $items));
$calculatedTax = array_sum(array_column($items, 'tax_amount'));
$calculatedDiscount = array_sum(array_column($items, 'discount_amount'));
$calculatedGrandTotal = $calculatedSubtotal + $calculatedTax - $calculatedDiscount + ($purchase['shipping'] ?? 0);

// Verify calculations
if ($calculatedSubtotal != $purchase['subtotal'] || $calculatedGrandTotal != $purchase['grand_total']) {
    // Handle discrepancy (e.g., log error, update invoice)
    error_log("Calculation mismatch: Subtotal $calculatedSubtotal vs {$purchase['subtotal']}, Grand Total $calculatedGrandTotal vs {$purchase['grand_total']}");
}
    ob_start();
    include realpath(__DIR__ . '/../../../..') . "/views/Stock/purchase/pdf_template.php";
    $html = ob_get_clean();

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'Landscape');
    $dompdf->render();

    $pdfOutput = $dompdf->output();

    // Overwrite previous PDF if exists
    if (file_exists($pdfPath)) {
        unlink($pdfPath);
    }

    file_put_contents($pdfPath, $pdfOutput);

    // ✅ Force download of the actual PDF file
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="purchase_' . $invoiceId . '.pdf"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($pdfPath));
    readfile($pdfPath);
    exit;
}

public function public(){
    
    $db = App::resolve(Database::class);
    $invoiceId = $_GET['id'] ?? null;

    if (!$invoiceId) {
        die('Invalid request.');
    }
    $purchase = $db->query("SELECT * FROM purchases WHERE id = :id", [
        'id' => $invoiceId
    ])->find();

    if (!$purchase) {
        die("<h3>Purchase not found or token is invalid.</h3>");
    }

    $supplier = $db->query("SELECT * FROM suppliers WHERE id = :id", [
        'id' => $purchase['supplier_id']
    ])->find();

    $items = $db->query("SELECT * FROM purchase_items WHERE purchase_id = :purchase_id", [
        'purchase_id' => $invoiceId
    ])->get();

    $payment = $db->query("SELECT * FROM payments WHERE invoice_id = :id", [
        'id' => $invoiceId
    ])->find() ?? ['amount' => 0];

    return views('Stock/purchase/public.view.php', [
        'purchase' => $purchase,
        'supplier' => $supplier,
        'items' => $items,
        'payment'=> $payment
    ]);

}

public function Status()
{
    $status = $_POST['status'] ?? null;
    $purchaseId = $_POST['tid'] ?? null;

    if (!$status || !$purchaseId) {
        abort(400);
        die("<script>alert('Purchase does not exist. Try again.'); window.history.back();</script>");  
    }

     $db = App::resolve(Database::class);
    try {
        $db->beginTransaction();

        // Update purchases table
     $affecteds =   $db->query("UPDATE purchases SET status = ? WHERE id = ? LIMIT 1", [$status, $purchaseId]);

        $db->commit();
            $successMessage = '
      <strong>Success</strong>: Details updated successfully!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/purchase-views?id='.$purchaseId.''); 
    } catch (Exception $e) {
        $db->rollBack();
        $successMessage = '
        <strong>Error</strong>: Details fail to update!!';
      $_SESSION['success'] = $successMessage;
        redirect('/AIS/purchase-views?id='.$purchaseId.''); 
    }
}

public function update()
{
   
   
$db = App::resolve(Database::class);
// var_dump($_POST);
// exit;
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die("<script>alert('Invalid Access'); window.history.back();</script>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get existing invoice ID
    $purchaseId = intval($_POST['id'] ?? 0);
    if ($purchaseId <= 0) {
        die("<script>alert('Invalid Purchase ID'); window.history.back();</script>");
    }

    // Sanitize main invoice fields
  try {
    $db->beginTransaction();  
    $supplier_id = (int)($_POST['supplier_id'] ?? 0);
    $warehouse_id = (int)($_POST['warehouses'] ?? 0);
    $category_id = (int)($_POST['category'] ?? 0);
    $invoice_no = htmlspecialchars(trim($_POST['invocieno'] ?? ''));
    $reference = htmlspecialchars(trim($_POST['refer'] ?? ''));
    $invoice_date = date('Y-m-d', strtotime(str_replace('-', '/', $_POST['invoicedate'] ?? date('Y-m-d'))));
    $due_date = date('Y-m-d', strtotime(str_replace('-', '/', $_POST['invocieduedate'] ?? date('Y-m-d'))));    
    $tax_format = $_POST['taxformat'] ?? 'on';
    $discount_format = $_POST['discountFormat'] ?? '%';
    $notes = htmlspecialchars(trim($_POST['notes'] ?? ''));
    $subtotal = (float)($_POST['subtotal'] ?? 0);
    $shipping = (float)($_POST['shipping'] ?? 0);
    $grand_total = (float)($_POST['total'] ?? 0);
    $update_stock = htmlspecialchars(trim($_POST['update_stock'] ?? 'yes'));
    $payment_terms = htmlspecialchars(trim($_POST['pterms'] ?? '1'));
    $payment_status ='pending';
$purchase = [ 
        'id' => $purchaseId,
        'supplier_id' => $supplier_id,
        'warehouse_id' => $warehouse_id,
        'category_id' => $category_id,
        'invoice_no' => $invoice_no,
        'reference' => $reference,
        'invoice_date' => $invoice_date,
        'due_date' => $due_date,
        'tax_format' => $tax_format,
        'discount_format' => $discount_format,
        'notes' => $notes,
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'grand_total' => $grand_total,
        'update_stock' => $update_stock,
        'payment_terms' => $payment_terms,
        'payment_status' => $payment_status,
        'status' => 'active',
        'updated_at' => date('Y-m-d H:i:s'),
        'updated_by' => $_SESSION['user']['ID'],
        
    ];

    // Validate required fields
    $required_fields = [
        'Supplier'         => $supplier_id,
    'Warehouse'        => $warehouse_id,
    'Invoice Number'   => $invoice_no,
    'Reference Number' => $reference,
    'Purchase Date'    => $invoice_date,
    'Due Date'         => $due_date,
    'Sub Total'        => $subtotal,
    'Total Amount'     => $grand_total
    ];

   foreach ($required_fields as $key => $value) {
    if (empty($value) && $value !== "0" && $value !== 0) {
        die("<script>alert('Error: $key is required. Please fill in all required fields.'); window.history.back();</script>");
    }
}


// 3. Validate that at least one item is present
if (empty($_POST['product_name']) || !is_array($_POST['product_name']) || count(array_filter($_POST['product_name'])) === 0) {
    die("<script>alert('Error: At least one product item is required.'); window.history.back();</script>");
}

// 4. Validate each item field
$line_items = count($_POST['product_name']);
for ($i = 0; $i < $line_items; $i++) {
    $item_required_fields = [
        'Product Name'       => $_POST['product_name'][$i] ?? '',
        'Quantity'           => $_POST['product_qty'][$i] ?? '',
        'Price'              => $_POST['product_price'][$i] ?? '',
        'Tax Percent'        => $_POST['product_tax'][$i] ?? '',
        'Tax Amount'         => $_POST['taxa'][$i] ?? '',
        'Discount Percent'   => $_POST['product_discount'][$i] ?? '',
        'Discount Amount'    => $_POST['disca'][$i] ?? '',
        'Subtotal'           => $_POST['product_subtotal'][$i] ?? ''
    ];

    foreach ($item_required_fields as $key => $value) {
        if (empty($value) && $value !== "0" && $value !== 0) {
            die("<script>alert('Error: $key is required for item " . ($i + 1) . ". Please check all item rows.'); window.history.back();</script>");
        }
    }
}

// var_dump($purchase);
// exit;
   // Update `purchases` table
$db->query("UPDATE purchases SET
    supplier_id = :supplier_id,
    warehouse_id = :warehouse_id,
    category_id = :category_id,
    invoice_no = :invoice_no,
    reference = :reference,
    invoice_date = :invoice_date,
    due_date = :due_date,
    tax_format = :tax_format,
    discount_format = :discount_format,
    notes = :notes,
    subtotal = :subtotal,
    shipping = :shipping,
    grand_total = :grand_total,
    update_stock = :update_stock,
    payment_terms = :payment_terms,
    payment_status = :payment_status,
    status = :status,
    updated_at = :updated_at,
    updated_by = :updated_by
    WHERE id = :id
", $purchase);


// Remove old purchase_items first
$db->query("DELETE FROM purchase_items WHERE purchase_id = ?", [$purchaseId]);


    $names = $_POST['product_name'] ?? [];
$quantities = $_POST['product_qty'] ?? [];
$prices = $_POST['product_price'] ?? [];
$taxes = $_POST['product_tax'] ?? [];
$tax_amounts = $_POST['taxa'] ?? [];
$discounts = $_POST['product_discount'] ?? [];
$discount_amounts = $_POST['disca'] ?? [];
$subtotals = $_POST['product_subtotal'] ?? [];
$descriptions = $_POST['product_description'] ?? [];
$product_ids = $_POST['pid'] ?? [];

// Insert updated purchase_items
foreach ($names as $index => $name) {
    $db->query("INSERT INTO purchase_items 
        (purchase_id, product_id, product_name, quantity, price, tax_percent, tax_amount, discount, discount_amount, subtotal, product_description)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
        $purchaseId,
        (int)($product_ids[$index]),
        htmlspecialchars(trim($name)),
        (int)($quantities[$index]),
        (float)($prices[$index]),
        (float)($taxes[$index]),
        (float)($tax_amounts[$index]),
        (float)($discounts[$index]),
        (float)($discount_amounts[$index]),
        (float)($subtotals[$index]),
        htmlspecialchars(trim($descriptions[$index]))
    ]);

        if ($update_stock === 'yes') {
            // Step 1: Fetch the specific product row
            $stockUpdate = $db->query("SELECT * FROM products WHERE id = ? AND warehouse_id = ? AND category_id = ?", [
                (int)$product_ids[$index],
                $warehouse_id,
                $category_id
            ])->find();
            if ($stockUpdate) {
                // Step 2: Update the main quantity (for this warehouse row)
                $new_qty = (int)$stockUpdate['quantity'] + (int)$quantities[$index];
        
                // Step 3: Decode the stock_by_warehouse JSON
                $stock_by_warehouse = json_decode($stockUpdate['stock_by_warehouse'], true) ?? [];
        
                // Step 4: Update this warehouse's quantity in the JSON
                if (isset($stock_by_warehouse[$warehouse_id])) {
                    $stock_by_warehouse[$warehouse_id] += (int)$quantities[$index];
                } else {
                    $stock_by_warehouse[$warehouse_id] = (int)$quantities[$index];
                }
        
                // Step 5: Re-encode the JSON
                $updated_stock_json = json_encode($stock_by_warehouse);
        
                // Step 6: Update the row
                $db->query("UPDATE products SET quantity = ?, stock_by_warehouse = ? WHERE id = ? AND warehouse_id = ? AND category_id = ?", [
                    $new_qty,
                    $updated_stock_json,
                    $product_ids[$index],
                    $warehouse_id,
                    $category_id
                ]);
            } else {
                die("<script>alert('Error: Product not found in this warehouse. Please add it first.'); window.history.back();</script>");
            }
        }
        
    }

    $db->commit();

    // Success message
   $successMessage = '
    <strong>Success</strong>: Purchase has been updated successfully!
    <a href="/AIS/purchase-views?id=' . $purchaseId . '" class="btn btn-info btn-lg" target="_blank">
      <span class="icon-file-text2" aria-hidden="true"></span> View
    </a>';
  $_SESSION['success'] = $successMessage;
    redirect('/AIS/purchase'); 
    exit;
} catch (Exception $e) {
    $db->rollBack();
    $successMessage = '
    <strong>Error</strong>: Purchase update fail!!
    <a href="/AIS/purchase-views?id=' . $purchaseId . '" class="btn btn-info btn-lg" target="_blank">
      <span class="icon-file-text2" aria-hidden="true"></span> View
    </a>';
  $_SESSION['success'] = $successMessage;
    redirect('/AIS/purchase'); 
    exit;
}
}

}

public function delete()
{
    $db = App::resolve(Database::class);
    $purchaseId = $_GET['id'] ?? null;

    if (!$purchaseId) {
               http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid Purchase ID']);
        exit;
    }

    // Check if purchase exists
    $purchase = $db->query("SELECT * FROM purchases WHERE id = :id", [
        'id' => $purchaseId
    ])->find();

    if (!$purchase) {
          http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Purchase not found']);
        exit;
    }

        try {
    // Delete purchase and related items
    $db->query("DELETE FROM purchase_items WHERE purchase_id = :id", [
        'id' => $purchaseId
    ]);
    
    $db->query("DELETE FROM purchases WHERE id = :id", [
        'id' => $purchaseId
    ]);

     echo json_encode(['status' => 'success', 'message' => 'Purchase deleted successfully']);
        exit;
        } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete purchase']);
        exit;
    }
}

public function manage() {
 
    // Pass it as a variable to the view
    views('Stock/purchase/manage.view.php');
}

public function ajaxList()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    // DataTables GET params
    $draw = $_GET['draw'] ?? 1;
    $start = $_GET['start'] ?? 0;
    $length = $_GET['length'] ?? 10;
    $searchValue = $_GET['search']['value'] ?? '';

    // Count total records
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM purchases")->find()['total'];

    // Base query and joins
    $baseQuery = "FROM purchases 
                  LEFT JOIN suppliers ON suppliers.id = purchases.supplier_id";

    $where = "";
    $params = [];

    // Search filter
    if (!empty($searchValue)) {
        $where = " WHERE 
            purchases.invoice_no LIKE :search 
            OR suppliers.name LIKE :search 
            OR purchases.payment_status LIKE :search";

        $params['search'] = '%' . $searchValue . '%';
    }

    // Count filtered records
    $filteredQuery = "SELECT COUNT(*) as total " . $baseQuery . $where;
    $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];

    // Data query
    $dataQuery = "SELECT 
                    purchases.invoice_no, 
                    purchases.due_date, 
                    purchases.status, 
                    purchases.grand_total, 
                    purchases.payment_status, 
                    purchases.id, 
                    suppliers.name as supplier_name
                  " . $baseQuery . $where . "
                  ORDER BY purchases.id DESC 
                  LIMIT $start, $length";

    $data = $db->query($dataQuery, $params)->get();

    $output = [];
    $counter = $start + 1;

    foreach ($data as $row) {
        switch (strtolower($row['payment_status'])) {
            case 'active':
                $status = 'Active';
                $statusClass = 'st-paid';
                break;
            case 'partial':
                $status = 'Partial';
                $statusClass = 'st-partial';
                break;
            case 'cancelled':
                $status = 'Cancelled';
                $statusClass = 'st-canceled';
                break;
            case 'due':
                $status = 'Due';
                $statusClass = 'st-canceled';
                break;
            default:
                $status = ucfirst($row['payment_status']);
                $statusClass = 'st-paid';
                break;
        }

        $output[] = [
            $counter++,
            htmlspecialchars($row['invoice_no']),
            htmlspecialchars($row['supplier_name']),
            date('Y-m-d', strtotime($row['due_date'])),
            number_format($row['grand_total'], 2),
            '<span class="' . $statusClass . '">' . $status . '</span>',
            '<a href="/AIS/purchase-views?id=' . $row['id'] . '" class="btn btn-success btn-xs"><i class="icon-file-text"></i> View</a> &nbsp; 
             <a href="/AIS/purchase-download?id=' . $row['id'] . '" class="btn btn-info btn-xs" title="Download"><span class="icon-download"></span></a> &nbsp;
             <a href="#" data-object-id="' . $row['id'] . '" class="btn btn-danger btn-xs delete-objects"><span class="icon-trash"></span></a>'
        ];
    }

    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalFiltered,
        "data" => $output
    ]);
    exit;
}

}
