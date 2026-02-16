<?php 
namespace Http\Controllers\Configure;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;

class Employees {

public function index(){
    views('configure/employees.view.php');
}
public function add(){
    views('configure/add.view.php');
}

public function ajaxList()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    $draw = $_GET['draw'] ?? 1;
    $start = $_GET['start'] ?? 0;
    $length = $_GET['length'] ?? 10;
    $searchValue = $_GET['search']['value'] ?? '';

    $where = "WHERE 1=1"; // Base condition to simplify appending
    $params = [];

    if (!empty($searchValue)) {
        $where .= " AND (CONCAT(firstname, ' ', lastname) LIKE :search OR user_type LIKE :search OR block LIKE :search)";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Total records
    $totalQuery = "SELECT COUNT(*) as total FROM users";
    $totalRecords = $db->query($totalQuery)->find()['total'];

    // Filtered records
    $filteredQuery = "SELECT COUNT(*) as total FROM users $where";
    $totalFiltered = !empty($searchValue)
        ? $db->query($filteredQuery, $params)->find()['total']
        : $totalRecords;

    // Main data
    $dataQuery = "
        SELECT id, firstname, lastname, user_type, block
        FROM users
        $where
        ORDER BY id DESC
        LIMIT $start, $length
    ";

    $users = $db->query($dataQuery, $params)->get();

    $output = [];
    $counter = $start + 1;

    foreach ($users as $user) {
        // Map user_type to role name
        $roleMap = [
            1 => 'Super Admin',
            2 => 'Sales Manager',
            3 => 'Sales Person',
            4 => 'Accountant',
            5 => 'Manager'
        ];
        $role = isset($roleMap[$user['user_type']]) ? $roleMap[$user['user_type']] : 'Unknown';

        // Map block to status
        $status = ($user['block'] === 'Y') ? 'Inactive' : 'Active';
        $statusClass = ($user['block'] === 'Y') ? 'st-inactive' : 'st-active';

        // Concatenate firstname and lastname
        $fullName = htmlspecialchars($user['firstname'] . ' ' . $user['lastname']);

        $output[] = [
            $counter++,
            $fullName,
            htmlspecialchars($role),
            '<span class="' . $statusClass . '">' . htmlspecialchars($status) . '</span>',
            '
           
            <a href="#" data-object-id="'.$user['id'].'" class="btn btn-orange btn-xs delete-object" title="Disable"><i class="icon-eye-slash"></i> Disable</a>
            <a href="#pop_model" data-toggle="modal" data-remote="false" data-object-id="' . $user['id'] . '" class="btn btn-danger btn-xs delemp" title="Delete"><i class="icon-trash-o"></i> Delete</a>
            '
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