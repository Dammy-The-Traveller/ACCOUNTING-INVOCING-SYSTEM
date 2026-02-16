<?php 
namespace Http\Controllers\Stock\item;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;

class Category{

    public function index(){
        views('Stock/item/category.view.php');
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
        $totalRecordsQuery = "SELECT COUNT(DISTINCT categories.id) as total 
                              FROM categories 
                              LEFT JOIN products ON categories.id = products.category_id";
        $totalRecords = $db->query($totalRecordsQuery)->find()['total'];
    
        // Apply search filter if present
        $where = "";
        $params = [];
        if (!empty($searchValue)) {
            $where = "WHERE 
                categories.name LIKE :search 
                OR products.name LIKE :search";
            $params['search'] = '%' . $searchValue . '%';
        }
    
        // Count filtered records
        $filteredQuery = "SELECT COUNT(*) as total FROM (
            SELECT categories.id
            FROM categories 
            LEFT JOIN products ON categories.id = products.category_id
            $where
            GROUP BY categories.id
        ) AS grouped";
        $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];
    
        // Main data query grouped by category
        $dataQuery = "SELECT 
    c.id,
    c.name AS category_name,
    COUNT(DISTINCT p.id) AS total_products,
    COALESCE(SUM(p.quantity), 0) AS total_quantity,
    COALESCE(SUM(p.price), 0) AS total_price,
    COALESCE(SUM(p.price * p.quantity), 0) AS stock_worth,
    COALESCE(s.sales_worth, 0) AS sales_worth
FROM categories c
LEFT JOIN products p ON c.id = p.category_id
LEFT JOIN (
    SELECT 
        pr.category_id,
        SUM(ii.quantity * ii.price) AS sales_worth
    FROM products pr
    JOIN invoice_items ii ON ii.product_id = pr.id
    GROUP BY pr.category_id
) s ON s.category_id = c.id
$where
GROUP BY c.id
ORDER BY c.name ASC
LIMIT $start, $length

";
    
    
        $data = $db->query($dataQuery, $params)->get();
    
        $output = [];
        $counter = $start + 1;
    
        foreach ($data as $row) {
            $output[] = [
                $counter++,
                htmlspecialchars($row['category_name']),
                $row['total_products'],
                $row['total_quantity'],
                '$' .number_format($row['stock_worth'], 2). ' / ' . '$' .number_format($row['sales_worth'], 2),
                '<a href="/AIS/category-product-list?id='.$row['id'].'" class="btn btn-success btn-xs"><i class="icon-file-text"></i> View</a>
                &nbsp;
                <a href="/AIS/category-add?id='.$row['id'].'" class="btn btn-warning btn-xs"><i class="icon-pencil"></i> Edit</a>
                &nbsp;<a href="#" data-object-id="'.$row['id'].'" class="btn btn-danger btn-xs delete-object" title="Delete"><i class="icon-trash-o"></i></a>'
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
        $categories = null;


        if (isset($_GET['id'])) {
          $categoryId = $_GET['id'];
      
               if (!$categoryId) {
                  die("<script>alert('Invalid Token'); window.history.back();</script>");
              }
          // Fetch category
          $categories = $db->query("SELECT * FROM categories WHERE id = :id", [
              'id' => $categoryId
          ])->find();
      
           if (!$categories) {
              die("<script>alert('Category not found.'); window.history.back();</script>");
           
          }
      
      
          views('Stock/item/add_category.view.php', [
          'categories' => $categories,
      ]);
      exit;
      }
        views('Stock/item/add_category.view.php');
    }

    public function store(){
        $db = App::resolve(Database::class);

     
        $data = [
            'name' => htmlspecialchars(trim($_POST['product_catname'] ?? '')),
            'description' => htmlspecialchars(trim($_POST['product_catdesc'] ?? '')),
            'status' => htmlspecialchars(trim($_POST['category_status'] ?? '')),
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $_SESSION['user']['ID'] ?? 0,
        ];

        if (!Validator::string($_POST['product_catname'], 1, 100)) {
            $_SESSION['error'] = 'Invalid category name';
            redirect('/AIS/category-add');
            exit;
        }
        $db->query("INSERT INTO categories (name, description, status, created_at, created_by) VALUES (:name, :description, :status, :created_at, :created_by)", $data);
        $_SESSION['success'] = 'Category added successfully';
        redirect('/AIS/category-add');
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
        // Check if any products are assigned to this category
        $check = $db->query("SELECT COUNT(*) AS product_count FROM products WHERE category_id = :id", ['id' => $id])->find();
    
        if ($check && $check['product_count'] > 0) {
            // Products found – don't delete
            http_response_code(400);
            echo json_encode(["message" => "Cannot delete this category because products are assigned to it."]);
            exit;
        }
    
        // No products – safe to delete category
        $db->query("DELETE FROM categories WHERE id = :id", ['id' => $id]);
    
        echo json_encode(["message" => "Category deleted successfully"]);
    
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Error deleting category"]);
    }
    exit;    
}

public function update(){
    $db = App::resolve(Database::class);

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("<script>alert('Invalid CSRF token'); window.history.back();</script>");
    }

    if (empty($_POST['category_id'])) {
        $_SESSION['error'] = '<strong>Error</strong>: Invalid category ID.';
        redirect('/AIS/category-add');
        exit;
    }
    
      $data = [
        'id' => (int) ($_POST['category_id'] ?? 0),
        'name' => htmlspecialchars(trim($_POST['product_catname'] ?? '')),
        'description' => htmlspecialchars(trim($_POST['product_catdesc'] ?? '')),
        'status' => htmlspecialchars(trim($_POST['category_status'] ?? '')),
        'updated_at' => date('Y-m-d H:i:s'),
        'updated_by' => $_SESSION['user']['ID'],
   
    ];
 
    $db->query("UPDATE categories SET name = :name, description = :description, status = :status, updated_at = :updated_at, updated_by=:updated_by WHERE id = :id", $data);
    $successMessage = '
    <strong>Success</strong>: Category updated successfully!!';
  $_SESSION['success'] = $successMessage;
    redirect('/AIS/category-add'); // redirect to product list
}
public function productList(){
 views('Stock/item/product_list.view.php');
}
public function productListAjax()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    $category_id = $_GET['id'] ?? null;
    $draw = $_GET['draw'] ?? 1;
    $start = (int)($_GET['start'] ?? 0);
    $length = (int)($_GET['length'] ?? 10);
    $searchValue = $_GET['search']['value'] ?? '';

    // Total record count
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM products")->find()['total'];

    $baseQuery = "FROM products 
        LEFT JOIN categories ON products.category_id = categories.id";

    $whereParts = [];
    $params = [];

    // Filter by category_id if passed (e.g., dropdown filter)
    if (!empty($category_id)) {
        $whereParts[] = "products.category_id = :category_id";
        $params['category_id'] = $category_id;
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
            OR categories.name LIKE :search)";
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
        categories.name AS category_name,
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
            $row['category_name'],
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