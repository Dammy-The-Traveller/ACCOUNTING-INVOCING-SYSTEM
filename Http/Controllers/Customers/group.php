<?php 
namespace Http\Controllers\Customers;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;

class Group{

        public function index() {
        // Your code for the CRM index page
       $db = App::resolve(Database::class);
    // Fetch dropdown data once

    // Check if editing
    if (isset($_GET['id'])) {
       $groupId = (int) $_GET['id'];


        if (!$groupId) {
            die("<script>alert('Invalid group'); window.history.back();</script>");
        }

        // Fetch purchase
        $group = $db->query("SELECT * FROM customergroup WHERE id = :id", [
            'id' => $groupId
        ])->find();

        if (!$group) {
            die("<script>alert('Customer not found.'); window.history.back();</script>");
        }
        return views('crm/group/index.view.php', [
            'group' => $group,
         
        ]);
    }

    // New purchase
       return views('crm/group/index.view.php');

    }

     public function store() {
        //  code for storing CRM data
        $db = App::resolve(Database::class);
// Sanitize input

$customer = [
    'name'             => htmlspecialchars(trim($_POST['group_name'])),
    'description'            => htmlspecialchars(trim($_POST['group_desc'])),
    'created_at' => date('Y-m-d H:i:s'),
    'created_by' => $_SESSION['user']['ID']
];

// Validate required fields
$errors = [];

if (!Validator::string($customer['name'], 2, 255)) {
    $errors['name'] = 'Please provide a valid name.';
}

if (!Validator::string($customer['description'], 5, 500)) {
    $errors['description'] = 'Please provide a valid description not less than five character and longer than 500 characters.';
}


if (!empty($errors)) {
    return views('crm/group/index.view.php', [
        'errors' => $errors
    ]);
}

// Insert into DB
$db->query("INSERT INTO customergroup (name, description, created_at, created_by
    ) VALUES (
        :name, :description, :created_at, :created_by
    )
", $customer);
$_SESSION['success'] = '
      <strong>Success</strong>: Group has been created successfully!';
redirect('/AIS/group');
exit;
    }

    public function manage() {

        // Your for managing CRM entries
        views('crm/group/manage.view.php');
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
    $customerId = intval($_POST['gid'] ?? 0);
    if ($customerId <= 0) {
        die("<script>alert('Invalid group'); window.history.back();</script>");
    }

    // Sanitize main invoice fields
  try {
    $db->beginTransaction();  
    $customer = [
    'name'             => htmlspecialchars(trim($_POST['group_name'])),
    'description'            => htmlspecialchars(trim($_POST['group_desc'])),
    'updated_at' => date('Y-m-d H:i:s'),
    'updated_by' => $_SESSION['user']['ID']
];

// Validate required fields
$errors = [];

if (!Validator::string($customer['name'], 2, 255)) {
    $errors['name'] = 'Please provide a valid name.';
}

if (!Validator::string($customer['description'], 5, 500)) {
    $errors['description'] = 'Please provide a valid description not less than five character and longer than 500 characters.';
}


if (!empty($errors)) {
    return views('crm/group/index.view.php', [
        'errors' => $errors
    ]);
}

$customer['id'] = (int) $_POST['gid']; 
   // Update `customer` table

$db->query("UPDATE `customergroup` SET
    name = :name,
    description = :description,
    updated_at = :updated_at,
    updated_by = :updated_by
    WHERE id = :id
", $customer);


 
    $db->commit();


    // Success message
   $successMessage = '
    <strong>Success</strong>:Group has been updated successfully!';
  $_SESSION['success'] = $successMessage;
    redirect('/AIS/group?id='.$customerId.''); 
    exit;
} catch (Exception $e) {
    $db->rollBack();
    $successMessage = '
    <strong>Error</strong>: Group update fail!!';
  $_SESSION['success'] = $successMessage;
    redirect('/AIS/group?id='.$customerId.''); 
    exit;
}
}

}

 public function delete()
{
    $db = App::resolve(Database::class);
    $groupId = (int)$_GET['id'] ?? null;

    if (!$groupId) {
               http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid group ID']);
        exit;
    }

    // Check if return exists
    $group = $db->query("SELECT * FROM customers WHERE group_id = :id", [
        'id' => $groupId
    ])->find();

    if ($group) {
          http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Group Can not be deleted it associated with a customer ']);
        exit;
    }

        try {
    $db->beginTransaction();
    
    $db->query("DELETE FROM customergroup WHERE id = :id", [
        'id' => $groupId
    ]);

     $db->commit();
        http_response_code(200);
     echo json_encode(['status' => 'success', 'message' => 'group deleted successfully']);
        exit;
        } catch (Exception $e) {
    $db->rollBack();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete group']);
        exit;
    }
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

    // Search filter
    $where = "";
    $params = [];

    if (!empty($searchValue)) {
        $where = "WHERE 
                    customergroup.name LIKE :search 
                    OR COUNT(customers.id) LIKE :search_fake";
        $params['search'] = '%' . $searchValue . '%';
        $params['search_fake'] = '%' . $searchValue . '%'; // for COUNT
    }

    // Total record count (before filtering)
    $totalQuery = "SELECT COUNT(*) as total FROM customergroup";
    $totalRecords = $db->query($totalQuery)->find()['total'];

    // Get filtered record count
    $filteredQuery = "
        SELECT COUNT(*) as total
        FROM (
            SELECT customergroup.id
            FROM customergroup
            LEFT JOIN customers ON customergroup.id = customers.group_id
            GROUP BY customergroup.id
            " . ($where ? "HAVING customergroup.name LIKE :search OR COUNT(customers.id) LIKE :search_fake" : "") . "
        ) as temp";

    $totalFiltered = !empty($searchValue)
        ? $db->query($filteredQuery, $params)->find()['total']
        : $totalRecords;

    // Main data query with LIMIT
    $dataQuery = "
        SELECT 
            customergroup.id,
            customergroup.name,
            COUNT(customers.id) as total_customers
        FROM customergroup
        LEFT JOIN customers ON customergroup.id = customers.group_id
        GROUP BY customergroup.id, customergroup.name
        " . ($where ? "HAVING customergroup.name LIKE :search OR COUNT(customers.id) LIKE :search_fake" : "") . "
        ORDER BY customergroup.id DESC
        LIMIT $start, $length
    ";

    $data = $db->query($dataQuery, $params)->get();

    // Output formatting
    $output = [];
    $counter = $start + 1;

    foreach ($data as $row) {
        $output[] = [
            $counter++,
            htmlspecialchars($row['name']),
            htmlspecialchars($row['total_customers']),
            '<a href="/AIS/client-manage?id=' . $row['id'] . '" class="btn btn-info btn-sm"><span class="icon-eye"></span>View</a>
            <a href="/AIS/group?id=' . $row['id'] . '" class="btn btn-primary btn-sm">Edit</a>
             <a href="#" data-object-id="' . $row['id'] . '" class="btn btn-danger btn-sm delete-object">Delete</a>'
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

  public function client() {
        // Your for managing CRM entries
        $id = (int)$_GET['id'];
          if (!$id) {
            die("<script>alert('Invalid group'); window.history.back();</script>");
        }
        views('crm/group/client.view.php',[
            'id'=>$id
        ]);
    }
 public function groupList()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    // DataTables GET params
    $draw = $_GET['draw'] ?? 1;
    $start = $_GET['start'] ?? 0;
    $length = $_GET['length'] ?? 10;
    $searchValue = $_GET['search']['value'] ?? '';
    $groupid = (int) ($_GET['id'] ?? 0);

    // Check if group exists
    $groupExists = $db->query("SELECT COUNT(*) as count FROM customergroup WHERE id = :id", ['id' => $groupid])->find();

    // var_dump( $_GET['id']);
    // exit;
    if (!$groupExists || $groupExists['count'] == 0) {
        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        ]);
        exit;
    }

    // Base query
    $baseQuery = "
        FROM customers
        LEFT JOIN customergroup ON customergroup.id = customers.group_id
        WHERE customers.group_id = :groupid
    ";

    $params = ['groupid' => $groupid];

    // Apply search filter
    if (!empty($searchValue)) {
        $baseQuery .= " AND (
            customers.name LIKE :search 
            OR customergroup.name LIKE :search 
            OR customers.address LIKE :search
            OR customers.email LIKE :search
            OR customers.phone LIKE :search
        )";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Count total records in this group
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM customers WHERE group_id = :groupid";
    $totalRecords = $db->query($totalRecordsQuery, ['groupid' => $groupid])->find()['total'];

    // Count filtered records
    $filteredQuery = "SELECT COUNT(*) as total " . $baseQuery;
    $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];

    // Data query
    $dataQuery = "
        SELECT 
            customers.id, 
            customers.name, 
            customers.address, 
            customers.email, 
            customers.phone, 
            customergroup.name as group_name
        " . $baseQuery . "
        ORDER BY customers.id DESC 
        LIMIT $start, $length
    ";

    $data = $db->query($dataQuery, $params)->get();

    // Output formatting
    $output = [];
    $counter = $start + 1;

    foreach ($data as $row) {
        $output[] = [
            $counter++,
            '<a href="/AIS/view-customer?id=' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</a>',
            htmlspecialchars($row['address'] ?? ''),
            htmlspecialchars($row['email'] ?? ''),
            htmlspecialchars($row['phone'] ?? ''),
            htmlspecialchars($row['group_name'] ?? ''),
            '<a href="/AIS/view-customer?id=' . $row['id'] . '" class="btn btn-info btn-sm"><span class="icon-eye"></span> View</a>
             <a href="/AIS/customer-edit?id=' . $row['id'] . '" class="btn btn-primary btn-sm"><span class="icon-pencil"></span> Edit</a>
             <a href="#" data-object-id="' . $row['id'] . '" class="btn btn-danger btn-sm delete-object"><span class="icon-trash"></span></a>'
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