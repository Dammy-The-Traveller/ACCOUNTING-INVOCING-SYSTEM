<?php 
namespace Http\Controllers\Customers;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;


class Crm {
    public function index() {
        // Your code for the CRM index page
       $db = App::resolve(Database::class);
    // Fetch dropdown data once
    
  $customer =[];

      $groups = $db->query("SELECT * FROM customergroup")->get();
    // Check if editing
    if (isset($_GET['id'])) {
       $customerId = (int) $_GET['id'];


        if (!$customerId) {
            die("<script>alert('Invalid Customer'); window.history.back();</script>");
        }

        // Fetch purchase
        $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $customerId
        ])->find();

        if (!$customer) {
            die("<script>alert('Customer not found.'); window.history.back();</script>");
        }
        return views('crm/index.view.php', [
            'customer' => $customer,
            'groups' => $groups,
        ]);
    }

    // New purchase
 return views('crm/index.view.php', [
    'customer' =>$customer,
    'groups' => $groups,
]);

    }

    public function store() {
        //  code for storing CRM data
        $db = App::resolve(Database::class);
// Sanitize input

$customer = [
    'name'             => htmlspecialchars(trim($_POST['name'])),
    'customer_code'    => htmlspecialchars(trim($_POST['cus_code'])),
    'phone'            => htmlspecialchars(trim($_POST['phone'])),
    'email'            => htmlspecialchars(trim($_POST['email'])),
    'address'          => htmlspecialchars(trim($_POST['address'])),
    'city'             => htmlspecialchars(trim($_POST['city'])),
    'region'           => htmlspecialchars(trim($_POST['region'])),
    'country'          => htmlspecialchars(trim($_POST['country'])),
    'postbox'          => htmlspecialchars(trim($_POST['postbox'])),
    'company'          => htmlspecialchars(trim($_POST['company'])),
    'taxid'            => htmlspecialchars(trim($_POST['taxid'])),
    'group_id'         => (int) $_POST['customergroup'],

    'shipping_billing' => isset($_POST['customer1']) ? 'yes' : 'no',
    'shipping_name'    => htmlspecialchars(trim($_POST['name_s'])),
    'shipping_phone'   => htmlspecialchars(trim($_POST['phone_s'])),
    'shipping_email'   => htmlspecialchars(trim($_POST['email_s'])),
    'shipping_address' => htmlspecialchars(trim($_POST['address_s'])),
    'shipping_city'    => htmlspecialchars(trim($_POST['city_s'])),
    'shipping_region'  => htmlspecialchars(trim($_POST['region_s'])),
    'shipping_country' => htmlspecialchars(trim($_POST['country_s'])),
    'shipping_postbox' => htmlspecialchars(trim($_POST['postbox_s'])),
    'created_at' => date('Y-m-d H:i:s'),
    'created_by' => $_SESSION['user']['ID']
];

// Validate required fields
$errors = [];

if (!Validator::string($customer['name'], 2, 255)) {
    $errors['name'] = 'Please provide a valid name.';
}

if (!Validator::email($customer['email'])) {
    $errors['email'] = 'Please provide a valid email address.';
}

if (!Validator::string($customer['address'], 2, 255)) {
    $errors['address'] = 'Please provide a valid billing address.';
}
if (!Validator::string($customer['phone'], 7, 20)) {
    $errors['phone'] = 'Phone number is required and should be valid.';
}

if (!Validator::string($customer['shipping_name'], 2, 255)) {
    $errors['shipping_name'] = 'Shipping name must be valid.';
}

if (! empty($errors)) {
    return views('crm/index.view.php', [
        'errors' => $errors
    ]);
}

// Insert into DB
$db->query("INSERT INTO customers (name, customer_code, phone, email, address, city, region, country, postbox, company, taxid, group_id, shipping_billing, shipping_name, shipping_phone, shipping_email, shipping_address, shipping_city,
        shipping_region, shipping_country, shipping_postbox,created_at, created_by
    ) VALUES (
        :name, :customer_code, :phone, :email, :address, :city, :region, :country, :postbox, :company, :taxid, :group_id,:shipping_billing,:shipping_name, :shipping_phone, :shipping_email, :shipping_address, :shipping_city,
        :shipping_region, :shipping_country, :shipping_postbox, :created_at, :created_by
    )
", $customer);
$_SESSION['success'] = '
      <strong>Success</strong>: Customer has been created successfully!';
redirect('/AIS/create-customer');
exit;
    }

    public function manage() {

        // Your for managing CRM entries
        views('crm/manage.view.php');
    }

    public function view() {
     $db = App::resolve(Database::class);
        $id = (int) $_GET['id'] ?? null;

        if (!$id) {
            die("<script>alert('Invalid Customer'); window.history.back();</script>");
        }

         $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
            'id' => $id
        ])->find();

        if (!$customer) {
            die("<script>alert('Customer not found.'); window.history.back();</script>");
        }
        $group = $db->query("SELECT * FROM customergroup WHERE id =:id", [
            'id' =>$customer['group_id']
        ])->find();
      views('crm/view.view.php', [
            'id' => $id,
            'group'=>$group,
            'customer'=>$customer
        ]);
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
    $customerId = intval($_POST['id'] ?? 0);
    if ($customerId <= 0) {
        die("<script>alert('Invalid customer'); window.history.back();</script>");
    }

    // Sanitize main invoice fields
  try {
    $db->beginTransaction();  
$customer = [
    'name'             => htmlspecialchars(trim($_POST['name'])),
    'phone'            => htmlspecialchars(trim($_POST['phone'])),
    'email'            => htmlspecialchars(trim($_POST['email'])),
    'address'          => htmlspecialchars(trim($_POST['address'])),
    'city'             => htmlspecialchars(trim($_POST['city'])),
    'region'           => htmlspecialchars(trim($_POST['region'])),
    'country'          => htmlspecialchars(trim($_POST['country'])),
    'postbox'          => htmlspecialchars(trim($_POST['postbox'])),
    'company'          => htmlspecialchars(trim($_POST['company'])),
    'taxid'            => htmlspecialchars(trim($_POST['taxid'])),
    'group_id'         => (int) $_POST['customergroup'],

    'shipping_billing' => isset($_POST['customer1']) ? 'yes' : 'no',
    'shipping_name'    => htmlspecialchars(trim($_POST['name_s'])),
    'shipping_phone'   => htmlspecialchars(trim($_POST['phone_s'])),
    'shipping_email'   => htmlspecialchars(trim($_POST['email_s'])),
    'shipping_address' => htmlspecialchars(trim($_POST['address_s'])),
    'shipping_city'    => htmlspecialchars(trim($_POST['city_s'])),
    'shipping_region'  => htmlspecialchars(trim($_POST['region_s'])),
    'shipping_country' => htmlspecialchars(trim($_POST['country_s'])),
    'shipping_postbox' => htmlspecialchars(trim($_POST['postbox_s'])),
    'updated_at' => date('Y-m-d H:i:s'),
    'updated_by' => $_SESSION['user']['ID']
];


    // Validate required fields
    $required_fields = [
        'Customer Name'  => $_POST['name'] ?? null,
    'Email Address'  => $_POST['email'] ?? null,
    'Phone Number'   => $_POST['phone'] ?? null,
    'Address'        => $_POST['address'] ?? null,
    'City'  => $_POST['city'] ?? null,
    'Region'         => $_POST['region'] ?? null,
    'Country'        => $_POST['country'] ?? null,
    'Postbox'        => $_POST['postbox'] ?? null,
    'Company'        => $_POST['company'] ?? null,
    'Tax ID'         => $_POST['taxid'] ?? null,
    'Group ID'       => $_POST['customergroup'] ?? null,
        'Shipping Name'  => $_POST['name_s'] ?? null,
        'Shipping Phone' => $_POST['phone_s'] ?? null,
        'Shipping Email' => $_POST['email_s'] ?? null,
        'Shipping Address' => $_POST['address_s'] ?? null,
        'Shipping City'  => $_POST['city_s'] ?? null,
        'Shipping Region'=> $_POST['region_s'] ?? null,
        'Shipping Country'=> $_POST['country_s'] ?? null,
        'Shipping Postbox'=> $_POST['postbox_s'] ?? null
    ];

foreach ($required_fields as $key => $value) {
    if (empty($value) && $value !== "0" && $value !== 0) {
        die("<script>alert('Error: $key is required. Please fill in all required fields.'); window.history.back();</script>");
    }
}





$customer['id'] = (int) $_POST['id']; 
   // Update `customer` table

$db->query("UPDATE customers SET
    name = :name,
    phone = :phone,
    email = :email,
    address = :address,
    city = :city,
    region = :region,
    country = :country,
    postbox = :postbox,
    company = :company,
    taxid = :taxid,
    group_id = :group_id,
    shipping_billing = :shipping_billing,
    shipping_name = :shipping_name,
    shipping_phone = :shipping_phone,
    shipping_email = :shipping_email,
    shipping_address = :shipping_address,
    shipping_city = :shipping_city,
    shipping_region = :shipping_region,
    shipping_country = :shipping_country,
    shipping_postbox = :shipping_postbox,
    updated_at = :updated_at,
    updated_by = :updated_by
WHERE id = :id
", $customer);

 
    $db->commit();

    // Success message
   $successMessage = '
    <strong>Success</strong>:Customer has been updated successfully!
    <a href="/AIS/view-customer?id=' . $customerId . '" class="btn btn-info btn-lg" target="_blank">
      <span class="icon-file-text2" aria-hidden="true"></span> View
    </a>';
  $_SESSION['success'] = $successMessage;
    redirect('/AIS/create-customer'); 
    exit;
} catch (Exception $e) {
    $db->rollBack();
    $successMessage = '
    <strong>Error</strong>: Customer update fail!!
    <a href="/AIS/view-customer?id=' . $customerId. '" class="btn btn-info btn-lg" target="_blank">
      <span class="icon-file-text2" aria-hidden="true"></span> View
    </a>';
  $_SESSION['success'] = $successMessage;
    redirect('/AIS/create-customer'); 
    exit;
}
}

}

 public function delete()
{
    $db = App::resolve(Database::class);
    $customerId = (int)$_GET['id'] ?? null;

    if (!$customerId) {
               http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid Customer ID']);
        exit;
    }

    // Check if return exists
    $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
        'id' => $customerId
    ])->find();

    if (!$customer) {
          http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
        exit;
    }

        try {
    $db->beginTransaction();
    
    $db->query("DELETE FROM customers WHERE id = :id", [
        'id' => $customerId
    ]);

     $db->commit();
        http_response_code(200);
     echo json_encode(['status' => 'success', 'message' => 'Customer deleted successfully']);
        exit;
        } catch (Exception $e) {
    $db->rollBack();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete Customer']);
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

    // Count total records
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM customers")->find()['total'];

    // Base query and joins
    $baseQuery = "FROM customers
                  LEFT JOIN customergroup ON customergroup.id = customers.group_id";
                 

    $where = "";
    $params = [];

    // Search filter
    if (!empty($searchValue)) {
        $where = " WHERE 
            customers.name LIKE :search 
            OR customergroup.name LIKE :search 
            OR customers.address LIKE :search
            OR customers.email LIKE :search
            OR customers.phone LIKE :search";

        $params['search'] = '%' . $searchValue . '%';
    }

    // Count filtered records
    $filteredQuery = "SELECT COUNT(*) as total " . $baseQuery . $where;
    $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];

    // Data query
    $dataQuery = "SELECT 
                    customers.id, 
                    customers.name, 
                    customers.address, 
                    customers.email, 
                    customers.phone, 
                    customergroup.name as group_name
                  " . $baseQuery . $where . "
                  ORDER BY customers.id DESC 
                  LIMIT $start, $length";

    $data = $db->query($dataQuery, $params)->get();

    $output = [];
    $counter = $start + 1;

    foreach ($data as $row) {

        $output[] = [
            $counter++,
            '<a href="/AIS/view-customer?id=' . $row['id'] . '">'.$row['name'] .'</a>',
           htmlspecialchars($row['address'] ?? ''),
            htmlspecialchars($row['email'] ?? ''),
            htmlspecialchars($row['phone'] ?? ''),
           htmlspecialchars($row['group_name'] ?? ''),
            '<a href="/AIS/view-customer?id=' . $row['id'] . '" class="btn btn-info btn-sm"><span class="icon-eye"></span>View</a>
             <a href="/AIS/customer-edit?id=' . $row['id'] . '" class="btn btn-primary btn-sm" title="Edit"><span class="icon-pencil">Edit</span></a> 
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

    public function invoice() {
            $id = (int) $_GET['id'] ?? null;
            if (!$id) {
                exit("<script>alert('Invalid Customer ID'); window.history.back();</script>");
                
            }
    
            $db = App::resolve(Database::class);
            $customer = $db->query("SELECT * FROM customers WHERE id = :id", [
                'id' => $id
            ])->find();
    
            if (!$customer) {
                exit("<script>alert('Customer not found.'); window.history.back();</script>");
            }
    
            views('crm/invoice.view.php', [
                'customer' => $customer
            ]);
        }

  public function invoiceList()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    // Datatables GET params
    $draw = $_GET['draw'] ?? 1;
    $start = $_GET['start'] ?? 0;
    $length = $_GET['length'] ?? 10;
    $searchValue = $_GET['search']['value'] ?? '';
    $customerId = (int) ($_GET['id'] ?? 0);

    // Count total records (not filtered)
    $totalRecordsQuery = "SELECT COUNT(*) as total FROM invoices";
    $totalRecords = $db->query($totalRecordsQuery)->find()['total'];

    // Base query and filters
    $baseQuery = "FROM invoices
                  LEFT JOIN customers ON customers.id = invoices.customer_id";
    
    $whereClauses = [];
    $params = [];

    if ($customerId > 0) {
        $whereClauses[] = "invoices.customer_id = :customer_id";
        $params['customer_id'] = $customerId;
    }

    if (!empty($searchValue)) {
        $whereClauses[] = "(invoices.invoice_number LIKE :search OR customers.name LIKE :search OR invoices.payment_status LIKE :search)";
        $params['search'] = '%' . $searchValue . '%';
    }

    $whereSql = "";
    if (!empty($whereClauses)) {
        $whereSql = " WHERE " . implode(" AND ", $whereClauses);
    }

    // Count filtered
    $filteredQuery = "SELECT COUNT(*) as total " . $baseQuery . $whereSql;
    $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];

    // Fetch paginated and filtered data
    $dataQuery = "SELECT invoices.invoice_number, invoices.created_at, invoices.grand_total, invoices.payment_status, invoices.id, invoices.public_token, customers.name as customer_name
                  " . $baseQuery . $whereSql . " 
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
            '<a href="/AIS/invoice-views?id='.$row['id'].'" class="btn btn-success btn-xs"><i class="icon-file-text"></i> View</a> 
             &nbsp; 
             <a href="/AIS/invoice-download?id='.$row['id'].'&token='.$row['public_token'].'" class="btn btn-info btn-xs" title="Download"><span class="icon-download"></span></a>
             &nbsp; &nbsp;
             <a href="#" data-object-id="'.$row['id'].'" class="btn btn-danger btn-xs delete-objects"><span class="icon-trash"></span></a>'
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
}