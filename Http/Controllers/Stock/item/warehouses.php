<?php 
namespace Http\Controllers\Stock\item;
use Core\App;
use Core\Database;
use Core\Validator;

class warehouses{
    public function index(){
        views('Stock/item/warehouses/index.view.php');
    }
        public function ajaxList()
    {
        $db = App::resolve(Database::class);
        header('Content-Type: application/json');
    
        // DataTables GET params
        $draw = $_GET['draw'] ?? 1;
        $start = (int)($_GET['start'] ?? 0);
        $length = (int)($_GET['length'] ?? 10);
        $searchValue = $_GET['search']['value'] ?? '';
    
        // Count total records (number of unique categories)
        $totalRecordsQuery = "SELECT COUNT(*) as total FROM warehouses";
       $totalRecords = $db->query($totalRecordsQuery)->find()['total'];

    
        // Apply search filter if present
        $where = "";
        $params = [];
        if (!empty($searchValue)) {
            $where = "WHERE 
                w.name LIKE :search 
                OR p.name LIKE :search";
            $params['search'] = '%' . $searchValue . '%';
        }
        
    
        // Count filtered records
        $filteredQuery = "SELECT COUNT(*) as total FROM (
            SELECT w.id
            FROM warehouses w
            LEFT JOIN products p ON w.id = p.warehouse_id
            $where
            GROUP BY w.id
        ) AS grouped";        
        $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];
    
        // Main data query grouped by category
        $dataQuery = "SELECT 
    w.id,
    w.name,
    COUNT(DISTINCT p.id) AS total_products,
    COALESCE(SUM(p.quantity), 0) AS stock_quantity,

    COALESCE(SUM(p.quantity * p.price), 0) AS worth_stock,
    COALESCE(s.sales_worth, 0) AS worth_sales

FROM warehouses w
LEFT JOIN products p ON p.warehouse_id = w.id
-- Subquery to get sales worth per warehouse
LEFT JOIN (
    SELECT 
        pr.warehouse_id,
        SUM(ii.quantity * ii.price) AS sales_worth
    FROM invoice_items ii
    JOIN products pr ON ii.product_id = pr.id
    GROUP BY pr.warehouse_id
) s ON s.warehouse_id = w.id

GROUP BY w.id, w.name
ORDER BY w.name;
";
    
    
        $data = $db->query($dataQuery, $params)->get();
    
        $output = [];
        $counter = $start + 1;
    
        foreach ($data as $row) {
            $output[] = [
                $counter++,
                htmlspecialchars($row['name']), // warehouse name
                $row['total_products'],         // total distinct products in warehouse
                $row['stock_quantity'],         // total quantity (summed)
                '$' . number_format($row['worth_stock'], 2) . ' / ' . '$' . number_format($row['worth_sales'], 2),
                '<a href="/AIS/warehouse-product-list?id=' . $row['id'] . '" class="btn btn-success btn-xs"><i class="icon-file-text"></i> View</a>
                &nbsp;
                <a href="/AIS/stock-warehouses-add?id=' . $row['id'] . '" class="btn btn-warning btn-xs"><i class="icon-pencil"></i> Edit</a>
                &nbsp;
                <a href="#" data-object-id="' . $row['id'] . '" class="btn btn-danger btn-xs delete-object" title="Delete"><i class="icon-trash-o"></i></a>'
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
    public function add(){

        $db = App::resolve(Database::class);
        $warehouses = null;


        if (isset($_GET['id'])) {
          $warehouseId = $_GET['id'];
      
               if (!$warehouseId) {
                  die("<script>alert('Invalid Token'); window.history.back();</script>");
              }
          // Fetch warehouse
          $warehouses = $db->query("SELECT * FROM warehouses WHERE id = :id", [
              'id' => $warehouseId
          ])->find();
      
           if (!$warehouses) {
              die("<script>alert('Warehouse not found.'); window.history.back();</script>");
           
          }
          views('Stock/item/warehouses/add.view.php', [
          'warehouses' => $warehouses,
      ]);
      exit;
      }
        views('Stock/item/warehouses/add.view.php');
    }

    public function deleteWarehouse()
    {
        $db = App::resolve(Database::class);
        header('Content-Type: application/json');
        $warehouseId = $_GET['id'] ?? null;
        // Step 1: Check for existing products in this warehouse
        $productCheck = $db->query("SELECT COUNT(*) AS product_count FROM products WHERE warehouse_id = ?", [$warehouseId])->find();
    
        if ($productCheck && $productCheck['product_count'] > 0) {
            // Products exist in warehouse — protect deletion
            echo json_encode([
                'status' => 'error',
                'message' => 'Cannot delete this warehouse because products are assigned to it.'
            ]);
            exit;
        }
    
        // Step 2: No products found — safe to delete
        $delete = $db->query("DELETE FROM warehouses WHERE id = ?", [$warehouseId]);
    
        echo json_encode([
            'status' => 'success',
            'message' => 'Warehouse deleted successfully.'
        ]);
        exit;
    }
    
    public function update(){
        $db = App::resolve(Database::class);
    
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            die("<script>alert('Invalid CSRF token'); window.history.back();</script>");
        }
    
        if (empty($_POST['warehouse_id'])) {
            $_SESSION['error'] = '<strong>Error</strong>: Invalid warehouse ID.';
            redirect('/AIS/stock-warehouses-add');
            exit;
        }
        
          $data = [
            'id' => (int) ($_POST['warehouse_id'] ?? 0),
            'name' => htmlspecialchars(trim($_POST['product_catname'] ?? '')),
            'location' => htmlspecialchars(trim($_POST['product_catdesc'] ?? '')),
            'status' => htmlspecialchars(trim($_POST['warehouse_status'] ?? '')),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $_SESSION['user']['ID'],
       
        ];
     
        $db->query("UPDATE warehouses SET name = :name, location = :location, status = :status, updated_at = :updated_at, updated_by=:updated_by WHERE id = :id", $data);
        $successMessage = '
        <strong>Success</strong>: Warehouse updated successfully!!';
      $_SESSION['success'] = $successMessage;
        redirect('/AIS/stock-warehouses-add'); // redirect to product list
    }
    public function store(){
        $db = App::resolve(Database::class);

     
        $data = [
            'name' => htmlspecialchars(trim($_POST['product_catname'] ?? '')),
            'location' => htmlspecialchars(trim($_POST['product_catdesc'] ?? '')),
            'status' => htmlspecialchars(trim($_POST['warehouse_status'] ?? '')),
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $_SESSION['user']['ID'] ?? 0,
        ];

        if (!Validator::string($_POST['product_catname'], 1, 100)) {
            $_SESSION['error'] = 'Invalid warehouse name';
            redirect('/AIS/stock-warehouses-add');
            exit;
        }
        $db->query("INSERT INTO warehouses (name, location, status, created_at, created_by) VALUES (:name, :location, :status, :created_at, :created_by)", $data);
        $_SESSION['success'] = 'Warehouse added successfully';
        redirect('/AIS/stock-warehouses-add');
        exit;
    }

    public function productList(){
        views('Stock/item/warehouses/product_list.view.php');
    }

    public function productListAjax()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    $warehouse_id = $_GET['id'] ?? null;
    $draw = $_GET['draw'] ?? 1;
    $start = (int)($_GET['start'] ?? 0);
    $length = (int)($_GET['length'] ?? 10);
    $searchValue = $_GET['search']['value'] ?? '';

    // Total record count
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM products")->find()['total'];

    $baseQuery = "FROM products 
        LEFT JOIN warehouses ON products.warehouse_id = warehouses.id";

    $whereParts = [];
    $params = [];

    // Filter by category_id if passed (e.g., dropdown filter)
    if (!empty($warehouse_id)) {
        $whereParts[] = "products.warehouse_id = :warehouse_id";
        $params['warehouse_id'] = $warehouse_id;
    }

    // Global search filter
    if (!empty($searchValue)) {
        $whereParts[] = "(products.name LIKE :search 
            OR products.code LIKE :search 
            OR products.quantity LIKE :search
            OR products.price LIKE :search
            OR products.wholesale_price LIKE :search
            OR products.tax_percent LIKE :search
            OR products.discount LIKE :search
            OR products.alert_quantity LIKE :search
            OR products.description LIKE :search
            OR warehouses.name LIKE :search)";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Combine WHERE clauses
    $whereSQL = '';
    if (!empty($whereParts)) {
        $whereSQL = " WHERE " . implode(" AND ", $whereParts);
    }

    // Filtered count
    $filteredQuery = "SELECT COUNT(*) as total " . $baseQuery . $whereSQL;
    $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];

    // Final query
    $dataQuery = "SELECT 
        products.id,
        products.name, 
        products.quantity, 
        products.code, 
        warehouses.name AS warehouse_name,
        products.price, 
        products.status 
    " . $baseQuery . $whereSQL . "
    ORDER BY products.id DESC 
    LIMIT $start, $length";

    $data = $db->query($dataQuery, $params)->get();

    // Prepare output
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
            $row['warehouse_name'],
            number_format($row['price'], 2),
            '<span class="' . $statusClass . '">' . ucfirst($status) . '</span>',
            '<a href="/AIS/stock?id=' . $row['id'] . '" class="btn btn-primary btn-xs"><span class="icon-pencil"></span> Edit</a>&nbsp; &nbsp;
            <a href="#" data-object-id="' . $row['id'] . '" class="btn btn-danger btn-xs delete-object"><span class="icon-bin"></span> Delete</a>'
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