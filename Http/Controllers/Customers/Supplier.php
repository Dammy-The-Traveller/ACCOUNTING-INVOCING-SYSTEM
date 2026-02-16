<?php 
namespace Http\Controllers\Customers;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
class Supplier{
 public function manage()
{

    // Count tickets by status
    views('crm/supplier/manage.view.php');
}

    public function Store(){
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
            return views('crm/supplier/index.view.php', [
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
    redirect('/AIS/supplier');
    exit;
} else {
    $_SESSION['error'] = '
      <strong>Error</strong>: Failed to add supplier.';
    redirect('/AIS/supplier');
    exit;
}
     
    }
public function index(){
        $db = App::resolve(Database::class);

  $supplier =[];

     
    // Check if editing
    if (isset($_GET['id'])) {
       $supplierId = (int) $_GET['id'];


        if (!$supplierId) {
            die("<script>alert('Invalid Supplier'); window.history.back();</script>");
        }

        // Fetch purchase
        $supplier = $db->query("SELECT * FROM supplier WHERE id = :id", [
            'id' => $supplierId
        ])->find();

        if (!$supplier) {
            die("<script>alert('Customer not found.'); window.history.back();</script>");
        }
        return views('crm/supplier/index.view.php', [
            'supplier' => $supplier
        ]);
    }


    views('crm/supplier/index.view.php',
[
        'supplier' => $supplier
    ]
);
}

public function ajaxList()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    $draw = $_GET['draw'] ?? 1;
    $start = $_GET['start'] ?? 0;
    $length = $_GET['length'] ?? 10;
    $searchValue = $_GET['search']['value'] ?? '';

    // Count total suppliers
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM suppliers")->find()['total'];

    $baseQuery = "FROM suppliers";

    $where = "";
    $params = [];

    // Search filter
    if (!empty($searchValue)) {
        $where = " WHERE 
            suppliers.name LIKE :search 
            OR suppliers.address LIKE :search
            OR suppliers.email LIKE :search
            OR suppliers.phone LIKE :search";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Count filtered records
    $filteredQuery = "SELECT COUNT(*) as total " . $baseQuery . $where;
    $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];

    // Fetch paginated data
    $dataQuery = "SELECT 
                    suppliers.id, 
                    suppliers.name, 
                    suppliers.address, 
                    suppliers.email, 
                    suppliers.phone 
                  " . $baseQuery . $where . "
                  ORDER BY suppliers.id DESC 
                  LIMIT $start, $length";

    $data = $db->query($dataQuery, $params)->get();

    $output = [];
    $counter = $start + 1;

    foreach ($data as $row) {
        $output[] = [
            $counter++,
            '<a href="/AIS/view-customer?id=' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</a>',
            htmlspecialchars($row['address'] ?? ''),
            htmlspecialchars($row['email'] ?? ''),
            htmlspecialchars($row['phone'] ?? ''),
            '<a href="/AIS/view-customer?id=' . $row['id'] . '" class="btn btn-info btn-sm"><span class="icon-eye"></span> View</a>
             <a href="/AIS/customer-edit?id=' . $row['id'] . '" class="btn btn-primary btn-sm" title="Edit"><span class="icon-pencil"></span> Edit</a> 
             <a href="#" data-object-id="' . $row['id'] . '" class="btn btn-danger btn-sm delete-object"><span class="icon-trash"></span> Delete</a>'
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
public function delete()
{
    $db = App::resolve(Database::class);

    $supplierId = (int)($_GET['id'] ?? 0);

    if (!$supplierId) {
         http_response_code(400);
       echo json_encode(['status' => 'error', 'message' => 'Invalid supplier ID.']);
        return;
    }

    try {
        $db->beginTransaction();

       $purchase = $db->query("SELECT * FROM purchases WHERE supplier_id = ?", [$supplierId]);

       if ($purchase) {
         http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Supplier has a purchase attached to it.']);
        return;
       }

        $supplier = $db->query("SELECT id FROM suppliers WHERE id = :id", ['id' => $supplierId])->find();
    if (!$supplier) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Supplier not found']);
        exit;
    }
        // Delete the ticket (replies + attachments will be auto-deleted by ON DELETE CASCADE)
        $db->query("DELETE FROM suppliers WHERE id = ?", [$supplierId]);

        $db->commit();

     echo json_encode(['status' => 'success', 'message' => 'Supplier deleted successfully.']);

    } catch (Exception $e) {
        $db->rollBack();
       echo json_encode(['status' => 'error', 'message' => 'Failed to delete supplier.']);
    }
}

}