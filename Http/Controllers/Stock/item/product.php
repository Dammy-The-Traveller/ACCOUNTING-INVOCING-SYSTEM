<?php 
namespace Http\Controllers\Stock\item;
use Core\App;
use Core\Database;
use Core\Validator;

use Exception;

class Product{
    public function index(){
        $db = App::resolve(Database::class);
        // Check if we’re editing (i.e., an ID is passed)
    $product = null;


  if (isset($_GET['id'])) {
    $productId = $_GET['id'];

         if (!$productId) {
            die("<script>alert('Invalid Token'); window.history.back();</script>");
        }
    // Fetch product
    $product = $db->query("SELECT * FROM products WHERE id = :id", [
        'id' => $productId
    ])->find();

     if (!$product) {
        die("<script>alert('Product not found.'); window.history.back();</script>");
     
    }

    $categories = $db->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC", [
        ])->get();
        $warehouses = $db->query("SELECT * FROM warehouses WHERE status = 'active' ORDER BY name ASC", [
        ])->get();
    views('Stock/item/add.view.php', [
    'product' => $product,
    'categories' => $categories,
    'warehouses' => $warehouses
]);
exit;
}
$categories = $db->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC", [
])->get();
$warehouses = $db->query("SELECT * FROM warehouses WHERE status = 'active' ORDER BY name ASC", [
])->get();
        views('Stock/item/add.view.php', [
            'categories' => $categories,
            'warehouses' => $warehouses
        ]);
        exit;
    }
    public function store(){
        $db = App::resolve(Database::class);
        
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            die("<script>alert('Invalid CSRF token'); window.history.back();</script>");
        }
        $data = [
            'name' => htmlspecialchars(trim($_POST['product_name'] ?? '')),
            'code' => htmlspecialchars(trim($_POST['product_code'] ?? '')),
            'category_id' => (int) ($_POST['product_cat'] ?? 0),
            'warehouse_id' => (int) ($_POST['product_warehouse'] ?? 0),
            'price' => floatval($_POST['product_price'] ?? 0),
            'wholesale_price' => floatval($_POST['fproduct_price'] ?? 0),
            'tax_percent' => floatval($_POST['product_tax'] ?? 0),
            'discount' => floatval($_POST['product_disc'] ?? 0),
            'quantity' => intval($_POST['product_qty'] ?? 0),
            'alert_quantity' => intval($_POST['product_qty_alert'] ?? 0), 
            'description' => htmlspecialchars(trim($_POST['product_desc'] ?? '')),
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $_SESSION['user']['ID'],
            'status' => htmlspecialchars(trim($_POST['product_status'] ?? '')),
        ];
        
 

        // dd($data);
        // exit;
        // Optional: validation
        if (!Validator::string($data['name'], 1, 100)) {
            dd('Invalid product name');
        }

        $db->query("
            INSERT INTO products (name, code, category_id, warehouse_id, price, wholesale_price, tax_percent, discount, quantity, alert_quantity, description, created_at, created_by, status)
            VALUES (:name, :code, :category_id, :warehouse_id, :price, :wholesale_price, :tax_percent, :discount,:quantity, :alert_quantity,  :description, :created_at, :created_by, :status)
        ", $data);

        $successMessage = '
        <strong>Success</strong>: Product added successfully!!';
      $_SESSION['success'] = $successMessage;
        redirect('/AIS/stock'); // redirect to product list
    }
    public function update(){
        $db = App::resolve(Database::class);

        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            die("<script>alert('Invalid CSRF token'); window.history.back();</script>");
        }

        if (empty($_POST['product_id'])) {
            $_SESSION['error'] = '<strong>Error</strong>: Invalid product ID.';
            redirect('/AIS/stock');
            exit;
        }
        
          $data = [
            'id' => (int) ($_POST['product_id'] ?? 0),
            'name' => htmlspecialchars(trim($_POST['product_name'] ?? '')),
            'code' => htmlspecialchars(trim($_POST['product_code'] ?? '')),
            'category_id' => (int) ($_POST['product_cat'] ?? 0),
            'warehouse_id' => (int) ($_POST['product_warehouse'] ?? 0),
            'price' => floatval($_POST['product_price'] ?? 0),
            'wholesale_price' => floatval($_POST['fproduct_price'] ?? 0),
            'tax_percent' => floatval($_POST['product_tax'] ?? 0),
            'discount' => floatval($_POST['product_disc'] ?? 0),
            'quantity' => intval($_POST['product_qty'] ?? 0),
            'alert_quantity' => intval($_POST['product_qty_alert'] ?? 0), 
            'description' => htmlspecialchars(trim($_POST['product_desc'] ?? '')),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $_SESSION['user']['ID'],
            'status' => htmlspecialchars(trim($_POST['product_status'] ?? '')),
        ];
     
        $db->query("UPDATE products SET name = :name, code = :code, category_id = :category_id, warehouse_id = :warehouse_id, price = :price, wholesale_price = :wholesale_price, tax_percent = :tax_percent, discount = :discount, quantity = :quantity, alert_quantity = :alert_quantity, description = :description, updated_at = :updated_at, updated_by=:updated_by, status = :status WHERE id = :id", $data);
        $successMessage = '
        <strong>Success</strong>: Product updated successfully!!';
      $_SESSION['success'] = $successMessage;
        redirect('/AIS/stock'); // redirect to product list
    }

    public function manage() {
        $db = App::resolve(Database::class);
    
        $Totalresult = $db->query("SELECT COUNT(*) AS total_product FROM products")->find();
      $totalProducts = $Totalresult ? $Totalresult['total_product'] : 0;
    
     $result = $db->query("SELECT COUNT(*) AS total_prods FROM products WHERE quantity = 0")->find();
      $totalOutOfStock = $result ? $result['total_prods'] : 0;
    
       $recurring = $db->query("SELECT COUNT(*) AS total_products FROM products WHERE quantity > 0")->find();
      $totalInStock = $recurring ? $recurring['total_products'] : 0;
        // Pass it as a variable to the view
        views('Stock/item/manage.view.php', [
            'totalProducts' => $totalProducts,
            'totalOutOfStock' => $totalOutOfStock,
            'totalInStock' => $totalInStock
        ]);
    }
    public function ajaxList()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    // Datatables GET params
    $draw = $_GET['draw'] ?? 1;
    $start = (int)($_GET['start'] ?? 0);
$length = (int)($_GET['length'] ?? 10);
    $searchValue = $_GET['search']['value'] ?? '';

    // Count total records
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM products";
    $totalRecords = $db->query($totalRecordsQuery)->find()['total'];

    // Main query
    $baseQuery = "FROM products LEFT JOIN categories ON products.category_id = categories.id";


    // Apply search filter if present
    $where = "";
    $params = [];
    if (!empty($searchValue)) {
        $where = " WHERE 
            products.name LIKE :search 
            OR products.code LIKE :search 
            OR products.quantity LIKE :search
            OR products.price LIKE :search
            OR products.wholesale_price LIKE :search
            OR products.tax_percent LIKE :search
            OR products.discount LIKE :search
            OR products.alert_quantity LIKE :search
            OR products.description LIKE :search
            ";

        $params['search'] = '%' . $searchValue . '%';
    }

    // Count filtered
    $filteredQuery = "SELECT COUNT(*) as total " . $baseQuery . $where;
    $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];

    // Fetch paginated and filtered data
    $dataQuery = "SELECT 
    products.id,
    products.name, 
    products.quantity, 
    products.code, 
    categories.name AS category_name,
    products.price, 
    products.status 
" . $baseQuery . $where . " 
ORDER BY products.id DESC 
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
        case 'inactive':
            $status = 'Inactive';
            $statusClass = 'st-partial';
            break;
        default:
            $status = 'Active';
            $statusClass = 'st-paid';
            break;
    }
        $output[] = [
            
            $counter++,
            htmlspecialchars($row['name']),
            $row['quantity'],
            $row['code'],
            $row['category_name'],
            number_format($row['price'], 2),
            '<span class="' . $statusClass . '">' . ucfirst($status) . '</span>',
            '<a href="/AIS/stock?id=' . $row['id'] . '" class="btn btn-primary btn-xs"><span class="icon-pencil"></span> Edit</a>&nbsp; &nbsp;
            <a href="#" data-object-id="'.$row['id'].'" class="btn btn-danger btn-xs  delete-object"><span class="icon-bin"></span> Delete</a>'
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

    // Validate the ID
    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid ID"]);
        exit;
    }
    
    try {
        // Check if product is used in any invoice items
        $check = $db->query("SELECT COUNT(*) AS usage_count FROM invoice_items WHERE product_id = :id", ['id' => $id])->find();
    
        if ($check && $check['usage_count'] > 0) {
            // Product is used in invoices – don't delete
            http_response_code(400);
            echo json_encode(["message" => "Cannot delete this product because it is used in existing invoices."]);
            exit;
        }
    
        // Safe to delete product
        $db->query("DELETE FROM products WHERE id = :id", ['id' => $id]);
    
        echo json_encode(["message" => "Product deleted successfully"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Error deleting product"]);
    }
    exit;
    
     }


}