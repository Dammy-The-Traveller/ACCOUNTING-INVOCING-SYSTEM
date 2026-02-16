<?php 
namespace Http\Controllers\Sales\Quote;

use Core\App;
use Core\Database;
use Dompdf\Dompdf;
use Exception; 
class QuoteController
{
    public function create()
    {
        $db = App::resolve(Database::class);

// Check if we’re editing (i.e., an ID is passed)
        $quotes = null;
         $quoteItems = [];
        if (isset($_GET['id'])) {
            $quoteId = $_GET['id'];
        
            // Fetch quote
            $quotes = $db->query("SELECT * FROM quotes WHERE id = :id", [
                'id' => $quoteId
            ])->find();
        
            if (!$quotes) {
                die("<script>alert('Quote not found or token is invalid.'); window.history.back();</script>");   
            }
               $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
                    'id' => $quotes['customer_id']
                ])->find();

                
            // Fetch items
            $quoteItems = $db->query("SELECT * FROM quote_items WHERE quote_id = :id", [
                'id' => $quoteId
            ])->get();
        
        
              $products = $db->query("SELECT * FROM products WHERE id = :id", [
                'id' => $quoteItems[0]['product_id']
            ])->find();
           
            if (!$quotes) {
                die("quote not found.");
            }
                $warehouses = $db->query("SELECT * FROM warehouses WHERE status = 'active' ORDER BY name ASC", [
        ])->get();

      
            views('Sales/Quote/quote.view.php', [
            'quotes' => $quotes,
            'customer' => $customer,
            'quoteItems' => $quoteItems,
            'products' => $products,
            'warehouses' => $warehouses
        ]);
        exit;
        }
               $warehouses = $db->query("SELECT * FROM warehouses WHERE status = 'active' ORDER BY name ASC", [
        ])->get();
        return views('Sales/Quote/quote.view.php',[
            'quotes' => null,
            'customer' => null,
            'quoteItems' => [],
            'products' => [],
            'warehouses' => $warehouses
        ]);
    }

        public function manage()
    {
        views('Sales/Quote/manage.view.php');
    }
      public function view()
    {        
        /**
         * Handles the retrieval and display of a specific quote.
         *
         * - Resolves the database instance.
         * - Retrieves the quote ID from the GET parameters.
         * - Validates the presence of the quote ID.
         * - Fetches the quote data from the database.
         * - If the quote is not found, displays an error message.
         * - Retrieves the associated customer information.
         * - Retrieves all items related to the quote.
         * - Passes the quote, customer, and quote items data to the view for rendering.
         *
         * @throws \Exception If the quote ID is missing or invalid.
         */
          $db = App::resolve(Database::class);
        $quoteId = $_GET['id'] ?? null;
       

        if (!$quoteId) {
            die("<h3>Invalid Token.</h3>");
        }

  
        $quotes = $db->query("SELECT * FROM quotes WHERE id = :id", [
            'id' => $quoteId
        ])->find();

        if (!$quotes) {
            die("<h3>Quote not found or token is invalid.</h3>");
        }

        
        $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $quotes['customer_id']
        ])->find();

        $quoteItems = $db->query("SELECT * FROM quote_items WHERE quote_id = :quote_id", [
            'quote_id' => $quoteId
        ])->get();

        $calculatedSubtotal = array_sum(array_map(function ($item) {
    return $item['rate'] * $item['quantity'];
}, $quoteItems));
$calculatedTax = array_sum(array_column($quoteItems, 'tax_amount'));
$calculatedDiscount = array_sum(array_column($quoteItems, 'discount_amount'));
$calculatedGrandTotal = $calculatedSubtotal + $calculatedTax - $calculatedDiscount + ($quotes['shipping'] ?? 0);

// Verify calculations
if ($calculatedSubtotal != $quotes['subtotal'] || $calculatedGrandTotal != $quotes['grand_total']) {
    // Handle discrepancy (e.g., log error, update invoice)
    error_log("Calculation mismatch: Subtotal $calculatedSubtotal vs {$quotes['subtotal']}, Grand Total $calculatedGrandTotal vs {$quotes['grand_total']}");
}
        // Pass to view
        return views('Sales/Quote/view.view.php', [
            'quotes' => $quotes,
            'quoteId' => $quoteId,
            'customer' => $customer,
            'quoteItems' => $quoteItems,
            'calculatedSubtotal' => $calculatedSubtotal,
            'calculatedTax' => $calculatedTax,
            'calculatedDiscount' => $calculatedDiscount,
            'calculatedGrandTotal' => $calculatedGrandTotal
        ]);
    }
    public function store()
    {
        /**
         * Handles the creation of a new quote.
         *
         * This method processes the form submission for creating a new quote,
         * validates the input, and saves the quote and its items to the database.
         *
         * @return void
         */
        // var_dump($_POST);
        // exit;
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die("<script>alert('Invalid CSRF token'); window.history.back();</script>");
}
        $db = App::resolve(Database::class);
        $data = $_POST;

        

        if (empty($data['customer_id']) || empty($data['invocieno'])) {
            return json_encode(['status' => 'error', 'message' => 'Required fields missing']);
        }

        // Save quote
        $db->beginTransaction();
         $date = date('Y-m-d', strtotime( $data['invoicedate']));
          $Duedate = date('Y-m-d', strtotime( $data['invocieduedate']));
       
          $cleanData = [
    'quote_number'     => clean($data['invocieno']),
    'reference'        => clean($data['refer']),
    'customer_id'      => (int) $data['customer_id'],
    'quote_date'       => clean($date),
    'due_date'    => clean($Duedate),
    'tax_format'       => clean($data['taxformat']),
    'discount_format'  => clean($data['discountFormat']),
    'notes'            => clean( html_entity_decode($data['notes'])),
    'proposal'         => htmlspecialchars( html_entity_decode(trim($data['propos']))),
    'subtotal'         => (float) $data['subtotal'],
    'shipping'         => (float) $data['shipping'],
    'total_tax'        => isset($data['taxa'][0]) ? (float) $data['taxa'][0] : 0,
    'total_discount'   => isset($data['disca'][0]) ? (float) $data['disca'][0] : 0,
    'grand_total'      => (float) $data['total'],
     'payment_terms'    => (int) $data['pterms'],
    'currency'         => clean($data['mcurrency'])
];

        try {
           
            // Insert into `quotes` table
            $db->query("INSERT INTO quotes (quote_number, reference, customer_id,  quote_date, due_date, tax_format, discount_format, notes, proposal, subtotal,shipping, total_tax, total_discount, grand_total, payment_terms, currency,  created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())", [
                $cleanData['quote_number'],
    $cleanData['reference'],
      $cleanData['customer_id'],
    $cleanData['quote_date'],
    $cleanData['due_date'],
    $cleanData['tax_format'],
    $cleanData['discount_format'],
    $cleanData['notes'],
   html_entity_decode(strip_tags($data['propos'])),
    $cleanData['subtotal'],
    $cleanData['shipping'],
    $cleanData['total_tax'],
    $cleanData['total_discount'],
    $cleanData['grand_total'],
    $cleanData['payment_terms'],
        $cleanData['currency']
            ]);

            $quote_id = $db->lastInsertId();

            // Save quote items
            foreach ($data['product_name'] as $i => $name) {
                $db->query("INSERT INTO quote_items (quote_id, product_name, quantity, rate, tax_percent, tax_amount, discount, subtotal, product_description, product_id, status, created_at, discount_amount) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?, ?, NOW(), ?)", [
                    $quote_id,
                    $name,
                    $data['product_qty'][$i],
                    $data['product_price'][$i],
                    $data['product_tax'][$i],
                    $data['taxa'][$i],
                    $data['product_discount'][$i],
                    $data['product_subtotal'][$i],
                    $data['product_description'][$i],
                    $data['pid'][$i],
                    $data['status'][$i] ?? 'Pending',
                    $data['disca'][$i]
                ]);

            }

                  $token = sha1($quote_id . uniqid('', true));


    // Save token if needed (optional)
    $db->query("UPDATE quotes SET public_token = :token WHERE id = :id", [
        'token' => $token,
        'id' => $quote_id
    ]);
            $db->commit();
              $successMessage = '
      <strong>Success</strong>: Quote has been created successfully!
      <a href="/AIS/quote-view?id=' . $quote_id . '" class="btn btn-info btn-lg" target="_blank">
        <span class="icon-file-text2" aria-hidden="true"></span> View
      </a>';

        $_SESSION['success'] = $successMessage;
            redirect('/AIS/quote'); 
        } catch (\Exception $e) {
            $db->rollBack();
                 $successMessage = '
      <strong>Error</strong>: Quote was not created!
      <a href="/AIS/quote" class="btn btn-danger btn-lg" target="_blank">
        Go Back
      </a>';

         $_SESSION['success'] = $successMessage;
            redirect('/AIS/quote'); 
        }

        exit;
    }

    public function update_status()
{
    $status = $_POST['status'] ?? null;
    $quoteId = $_POST['tid'] ?? null;


    if (!$status || !$quoteId) {
        abort(400);
        die("<script>alert('Quote does not exist. Try again.'); window.history.back();</script>");
      
    }

     $db = App::resolve(Database::class);
    try {
        $db->beginTransaction();

        // Update invoices table
     $affected =   $db->query("UPDATE quote_items SET status = ? WHERE quote_id = ? LIMIT 1", [$status, $quoteId]);


        // Update payments table if entry exists
  

        if ($affected->rowCount() === 0) {
    $db->rollBack();
    die("<script>alert('Quote does not exist or has been updated to similar status recently. Try again.'); window.history.back();</script>");
}
        $db->commit();
            $successMessage = '
      <strong>Success</strong>: Details updated successfully!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/quote-view?id='.$quoteId.''); 
    } catch (\Exception $e) {
        $db->rollBack();
          $successMessage = '
      <strong>Error</strong>: Details not updated!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/quote-view?id='.$quoteId.''); 
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
        $quoteId = $_GET['id'] ?? null;
       

        if (!$quoteId) {
            die("<h3>Invalid Token.</h3>");
        }

  
        $quotes = $db->query("SELECT * FROM quotes WHERE id = :id", [
            'id' => $quoteId
        ])->find();

        if (!$quotes) {
            die("<h3>Quote not found or token is invalid.</h3>");
        }

        
        $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $quotes['customer_id']
        ])->find();

        $quoteItems = $db->query("SELECT * FROM quote_items WHERE quote_id = :quote_id", [
            'quote_id' => $quoteId
        ])->get();

                $calculatedSubtotal = array_sum(array_map(function ($item) {
    return $item['rate'] * $item['quantity'];
}, $quoteItems));
$calculatedTax = array_sum(array_column($quoteItems, 'tax_amount'));
$calculatedDiscount = array_sum(array_column($quoteItems, 'discount_amount'));
$calculatedGrandTotal = $calculatedSubtotal + $calculatedTax - $calculatedDiscount + ($quotes['shipping'] ?? 0);

// Verify calculations
if ($calculatedSubtotal != $quotes['subtotal'] || $calculatedGrandTotal != $quotes['grand_total']) {
    // Handle discrepancy (e.g., log error, update invoice)
    error_log("Calculation mismatch: Subtotal $calculatedSubtotal vs {$quotes['subtotal']}, Grand Total $calculatedGrandTotal vs {$quotes['grand_total']}");
}
        // Pass to view
        return views('Sales/Quote/preview.view.php', [
            'quotes' => $quotes,
            'quoteId' => $quoteId,
            'customer' => $customer,
            'quoteItems' => $quoteItems,
            'calculatedSubtotal' => $calculatedSubtotal,
            'calculatedTax' => $calculatedTax,
            'calculatedDiscount' => $calculatedDiscount,
            'calculatedGrandTotal' => $calculatedGrandTotal
        ]);
    }

    public function generatePDF()
    {
    $db = App::resolve(Database::class);
        $quoteId = $_GET['id'] ?? null;
        $token = $_GET['token'] ?? null;

        if (!$quoteId || !$token) {
            die("<h3>Invalid Token.</h3>");
        }

        // Fetch the invoice with token verification
        $quotes = $db->query("SELECT * FROM quotes WHERE id = :id AND public_token = :token", [
            'id' => $quoteId,
            'token' => $token
        ])->find();

        if (!$quotes) {
            die("<h3>Invoice not found or token is invalid.</h3>");
        }

        // Fetch customer info
        $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $quotes['customer_id']
        ])->find();

        // Fetch invoice items
        $quoteItems = $db->query("SELECT * FROM quote_items WHERE quote_id = :quotes_id", [
            'quotes_id' => $quoteId
        ])->get();

                        $calculatedSubtotal = array_sum(array_map(function ($item) {
    return $item['rate'] * $item['quantity'];
}, $quoteItems));
$calculatedTax = array_sum(array_column($quoteItems, 'tax_amount'));
$calculatedDiscount = array_sum(array_column($quoteItems, 'discount_amount'));
$calculatedGrandTotal = $calculatedSubtotal + $calculatedTax - $calculatedDiscount + ($quotes['shipping'] ?? 0);

// Verify calculations
if ($calculatedSubtotal != $quotes['subtotal'] || $calculatedGrandTotal != $quotes['grand_total']) {
    // Handle discrepancy (e.g., log error, update invoice)
    error_log("Calculation mismatch: Subtotal $calculatedSubtotal vs {$quotes['subtotal']}, Grand Total $calculatedGrandTotal vs {$quotes['grand_total']}");
}
        ob_start();
        include realpath(__DIR__ . '/../../../..') . "/views/Sales/Quote/pdf_template.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'Landscape');
        $dompdf->render();

        // Save PDF file to disk
        $pdfOutput = $dompdf->output();
       $pdfPath = realpath(__DIR__ . '/../../../..') . "/Public/Quote/quote_{$quoteId}_{$token}.pdf";
        file_put_contents($pdfPath, $pdfOutput);

        // Redirect to viewer
        header("Location: /AIS/quote-viewer?id={$quoteId}&token={$token}");
        exit;

        
}

    public function showViewer()
{
    $quoteId = $_GET['id'] ?? 18;
    $token = $_GET['token'] ?? null;
    include realpath(__DIR__ . '/../../../..') . "/views/Sales/Quote/pdf_viewer.php";
   
}
public function downloadPDF()
{
    $quoteId = $_GET['id'] ?? null;
        $token = $_GET['token'] ?? null;

    if (!$quoteId || !$token) {
        die('Invalid request.');
    }

    // Define PDF path
    $filePath = realpath(__DIR__ . '/../../../..') . "/Public/Quote/quote_{$quoteId}_{$token}.pdf";

    // If file doesn't exist, regenerate it
    if (!file_exists($filePath)) {
               $db = App::resolve(Database::class);

        // Fetch invoice
            // Fetch the invoice with token verification
        $quotes = $db->query("SELECT * FROM quotes WHERE id = :id AND public_token = :token", [
            'id' => $quoteId,
            'token' => $token
        ])->find();

        if (!$quotes) {
            die("<h3>Invoice not found or token is invalid.</h3>");
        }

        // Fetch customer info
        $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $quotes['customer_id']
        ])->find();

        // Fetch invoice items
        $quoteItems = $db->query("SELECT * FROM quote_items WHERE quote_id = :quotes_id", [
            'quotes_id' => $quoteId
        ])->get();

                $calculatedSubtotal = array_sum(array_map(function ($item) {
    return $item['rate'] * $item['quantity'];
}, $quoteItems));
$calculatedTax = array_sum(array_column($quoteItems, 'tax_amount'));
$calculatedDiscount = array_sum(array_column($quoteItems, 'discount_amount'));
$calculatedGrandTotal = $calculatedSubtotal + $calculatedTax - $calculatedDiscount + ($quotes['shipping'] ?? 0);

// Verify calculations
if ($calculatedSubtotal != $quotes['subtotal'] || $calculatedGrandTotal != $quotes['grand_total']) {
    // Handle discrepancy (e.g., log error, update invoice)
    error_log("Calculation mismatch: Subtotal $calculatedSubtotal vs {$quotes['subtotal']}, Grand Total $calculatedGrandTotal vs {$quotes['grand_total']}");
}
        // Load HTML template (e.g., invoice-view.php) and pass invoice data
        ob_start();
        include realpath(__DIR__ . '/../../../..') . "/views/Sales/Quote/pdf_template.php";
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

public function update()
{
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("<script>alert('Invalid CSRF token'); window.history.back();</script>");
    }

    $db = App::resolve(Database::class);
    $data = $_POST;

    if (empty($data['customer_id']) || empty($data['invocieno']) || empty($data['id'])) {
         die("<script>alert('Required fields missing'); window.history.back();</script>");
       
    }

//   var_dump($data);
//   exit;
    $quote_id = (int) $data['id'];

    $date = date('Y-m-d', strtotime(str_replace('/', '-', $data['invoicedate'])));
    $dueDate = date('Y-m-d', strtotime(str_replace('/', '-', $data['invocieduedate'])));

    $cleanData = [
        'quote_number'     => clean($data['invocieno']),
        'reference'        => clean($data['refer']),
        'customer_id'      => (int) $data['customer_id'],
        'quote_date'       => clean($date),
        'due_date'         => clean($dueDate),
        'tax_format'       => clean($data['taxformat']),
        'discount_format'  => clean($data['discountFormat']),
        'notes'            => clean(html_entity_decode($data['notes'])),
        'proposal'         => htmlspecialchars(html_entity_decode(trim($data['propos']))),
        'subtotal'         => (float) $data['subtotal'],
        'shipping'         => (float) $data['shipping'],
        'total_tax'        => isset($data['taxa'][0]) ? (float) $data['taxa'][0] : 0,
        'total_discount'   => isset($data['disca'][0]) ? (float) $data['disca'][0] : 0,
        'grand_total'      => (float) $data['total'],
        'payment_terms'    => (int) $data['pterms'],
        'currency'         => clean($data['mcurrency']),
    ];

    try {
        $db->beginTransaction();
 
        // Update the main quote record
     $affecteds =   $db->query("UPDATE quotes SET 
            quote_number = ?, 
            reference = ?, 
            customer_id = ?, 
            quote_date = ?, 
            due_date = ?, 
            tax_format = ?, 
            discount_format = ?, 
            notes = ?, 
            proposal = ?, 
            subtotal = ?, 
            shipping = ?, 
            total_tax = ?, 
            total_discount = ?, 
            grand_total = ?, 
            payment_terms = ?, 
            currency = ?, 
            updated_at = NOW() 
            WHERE id = ?", [
            $cleanData['quote_number'],
            $cleanData['reference'],
            $cleanData['customer_id'],
            $cleanData['quote_date'],
            $cleanData['due_date'],
            $cleanData['tax_format'],
            $cleanData['discount_format'],
            $cleanData['notes'],
            html_entity_decode(strip_tags($data['propos'])),
            $cleanData['subtotal'],
            $cleanData['shipping'],
            $cleanData['total_tax'],
            $cleanData['total_discount'],
            $cleanData['grand_total'],
            $cleanData['payment_terms'],
            $cleanData['currency'],
            $quote_id
        ]);

        // Delete old quote items
        $db->query("DELETE FROM quote_items WHERE quote_id = ?", [$quote_id]);

        // Insert updated items
        foreach ($data['product_name'] as $i => $name) {
         $affected =   $db->query("INSERT INTO quote_items (quote_id, product_name, quantity, rate, tax_percent, tax_amount, discount, subtotal, product_description, product_id, status, created_at, discount_amount) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)", [
                $quote_id,
                $name,
                $data['product_qty'][$i],
                $data['product_price'][$i],
                $data['product_tax'][$i],
                $data['taxa'][$i],
                $data['product_discount'][$i],
                $data['product_subtotal'][$i],
                $data['product_description'][$i],
                $data['pid'][$i],
                $data['status'][$i] ?? 'Pending',
                $data['disca'][$i]
            ]);
        }

    
        if ($affected->rowCount() === 0) {
    $db->rollBack();
    die("<script>alert('Quote does not exist. Try again.'); window.history.back();</script>");
}
        
      
        // Update token (optional)
        $token = sha1($quote_id . uniqid('', true));
        $db->query("UPDATE quotes SET public_token = :token WHERE id = :id", [
            'token' => $token,
            'id' => $quote_id
        ]);

        
        $db->commit();

        $_SESSION['success'] = '
        <strong>Success</strong>: Quote has been updated successfully!
        <a href="/AIS/quote-view?id=' . $quote_id . '" class="btn btn-info btn-lg" target="_blank">
            <span class="icon-file-text2" aria-hidden="true"></span> View
        </a>';

        redirect('/AIS/quote');
    } catch (\Exception $e) {
        $db->rollBack();
        $_SESSION['success'] = '
        <strong>Error</strong>: Quote update failed!
        <a href="/AIS/quote-view?id=' . $quote_id . '" class="btn btn-danger btn-lg" target="_blank">
            Go Back
        </a>';
        redirect('/AIS/quote');
    }

    exit;
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
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM quotes";
    $totalRecords = $db->query($totalRecordsQuery)->find()['total'];

    // Main query base
    $baseQuery = "FROM quotes
                  LEFT JOIN customers ON customers.id = quotes.customer_id
                  LEFT JOIN quote_items ON quote_items.quote_id = quotes.id";

    // Search filter
    $where = "";
    $params = [];
    if (!empty($searchValue)) {
        $where = " WHERE 
            quotes.quote_number LIKE :search 
            OR customers.name LIKE :search 
            OR quotes.payment_status LIKE :search";

        $params['search'] = '%' . $searchValue . '%';
    }

    // Count filtered records
    $filteredQuery = "SELECT COUNT(DISTINCT quotes.id) as total " . $baseQuery . $where;
    $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];

    // Fetch paginated and filtered data
    $dataQuery = "SELECT 
                    quotes.id, 
                    quotes.quote_number, 
                    quotes.created_at, 
                    quotes.grand_total, 
                    quotes.public_token, 
                    quote_items.status, 
                    customers.name as customer_name
                  " . $baseQuery . $where . " 
                  GROUP BY quotes.id
                  ORDER BY quotes.id DESC 
                  LIMIT $start, $length";

    $data = $db->query($dataQuery, $params)->get();

    // Build response data
    $output = [];
    $counter = $start + 1;

    foreach ($data as $row) {
       
        $output[] = [
            $counter++,
            $row['quote_number'],
            $row['customer_name'],
            date('Y-m-d', strtotime($row['created_at'])),
            number_format($row['grand_total'], 2),
            '<span class="st-due">' . $row['status'] . '</span>',
            '<a href="/AIS/quote-view?id=' . $row['id'] . '" class="btn btn-success btn-xs"><i class="icon-file-text"></i> View</a> &nbsp; 
             <a href="/AIS/quote-download?id=' . $row['id'] . '&token=' . $row['public_token'] . '" class="btn btn-info btn-xs" title="Download"><span class="icon-download"></span></a> &nbsp; 
             <a href="#" data-object-id="'.$row['id'].'"  class="btn btn-danger btn-xs delete-quote"><span class="icon-trash"></span></a>'
        ];
    }

    // Return JSON response
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
        $db->query("DELETE FROM quotes WHERE id = :id", ['id' => $id]);

        // Optionally delete invoice items too
        $db->query("DELETE FROM quote_items WHERE quote_id = :id", ['id' => $id]);

        echo json_encode(["message" => "Invoice deleted successfully"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Error deleting invoice"]);
    }
    exit;
}

}
