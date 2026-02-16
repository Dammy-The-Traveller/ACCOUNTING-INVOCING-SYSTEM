<?php 
namespace Http\Controllers\miscellaneous;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

class DR{
    public function statistics(){
        views('miscellaneous/statistics.view.php');
    }

    public function customerStatement(){
        views('miscellaneous/customerStatement.view.php');
    }

    public function statementData()
{
    // var_dump($_POST);
    // exit;
        $accountId = $_POST['payer_id'] ?? null;
    $type      = $_POST['trans_type'] ?? 'All';
    $startDate = $_POST['sdate'] ?? null;
    $endDate   = $_POST['edate'] ?? null;

      views('miscellaneous/customer_statement.view.php',[
        'accountId' => $accountId,
        'type' => $type,
        'startDate' => $startDate,
        'endDate' => $endDate
      ]);
  
    }

public function AjaxStatementTransactions()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    // DataTables params
    $draw        = $_GET['draw'] ?? 1;
    $start       = (int)($_GET['start'] ?? 0);
    $length      = (int)($_GET['length'] ?? 10);
    $searchValue = $_GET['search']['value'] ?? '';

    // Filters from form
    $customerCode = $_GET['pay_acc'] ?? null; // now customer_code
    $type         = $_GET['trans_type'] ?? 'All';
    $startDate    = date('Y-m-d', strtotime($_GET['sdate'] ?? 'now'));
    $endDate      = date('Y-m-d', strtotime($_GET['edate'] ?? 'now'));

    // --------------------
    // 1. Build WHERE clause
    // --------------------
    $whereParts = [];
    $params = [];

    if (!empty($customerCode) && $customerCode !== 'All') {
        $whereParts[] = "t.payer_id = :customer_code";
        $params['customer_code'] = $customerCode;
    }

    if ($type !== 'All') {
        $whereParts[] = "t.type = :type";
        $params['type'] = strtolower($type);
    }

    if (!empty($startDate) && !empty($endDate)) {
        $whereParts[] = "DATE(t.created_at) BETWEEN :startDate AND :endDate";
        $params['startDate'] = $startDate;
        $params['endDate']   = $endDate;
    }

    if (!empty($searchValue)) {
        $whereParts[] = "t.description LIKE :search";
        $params['search'] = '%' . $searchValue . '%';
    }

    $whereSQL = '';
    if (!empty($whereParts)) {
        $whereSQL = "WHERE " . implode(" AND ", $whereParts);
    }

    // --------------------
    // 2. Count total & filtered
    // --------------------
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM transactions t")->find()['total'];

    $filteredQuery = "SELECT COUNT(*) as total 
                      FROM transactions t
                      LEFT JOIN accounts a ON t.account_id = a.id
                      $whereSQL";
    $totalFiltered = !empty($whereParts)
        ? $db->query($filteredQuery, $params)->find()['total']
        : $totalRecords;

    // --------------------
    // 3. Get data (with currency_code from accounts)
    // --------------------
    $dataQuery = "SELECT 
            t.id,
            t.created_at,
            t.description,
            t.type,
            t.amount,
            t.payer_id,
            a.currency_code
        FROM transactions t
        LEFT JOIN accounts a ON t.account_id = a.id
        $whereSQL
        ORDER BY t.created_at ASC
        LIMIT $start, $length
    ";
    $transactions = $db->query($dataQuery, $params)->get();

    // --------------------
    // 4. Calculate running balance
    // --------------------
    $output = [];
    $runningBalance = null;
    $currencySymbol = '';

    if (!empty($customerCode) && $customerCode !== 'All') {
        // Step 1: Get the latest account_id used by this customer
        $account = $db->query(
            "SELECT account_id
             FROM transactions
             WHERE payer_id = :payer_id
             ORDER BY created_at DESC
             LIMIT 1",
            ['payer_id' => $customerCode]
        )->find();

        if ($account && !empty($account['account_id'])) {
            $accountId = $account['account_id'];

            // Step 2: Get account details
            $acc = $db->query(
                "SELECT current_balance, currency_code 
                 FROM accounts 
                 WHERE id = :id",
                ['id' => $accountId]
            )->find();

            if ($acc) {
                $runningBalance = (float) $acc['current_balance'];
                $currencySymbol = $acc['currency_code'] == 1 ? '£' : ($acc['currency_code'] == 2 ? '€' : '₵');
            }
        }
    }

    foreach ($transactions as $tx) {
        if (!empty($customerCode) && $customerCode !== 'All') {
            // Update running balance
            if (strtolower($tx['type']) === 'credit') {
                $runningBalance += $tx['amount'];
            } elseif (strtolower($tx['type']) === 'debit') {
                $runningBalance -= $tx['amount'];
            }
        } else {
            // Multiple customers — get currency per row from joined data
            $currencySymbol = $tx['currency_code'] == 1 ? '£' :
                              ($tx['currency_code'] == 2 ? '€' : '₵');
        }

        $output[] = [
            date('Y-m-d', strtotime($tx['created_at'])),
            htmlspecialchars($tx['description']),
            strtolower($tx['type']) === 'debit' ? number_format($tx['amount'], 2) . " $currencySymbol" : '',
            strtolower($tx['type']) === 'credit' ? number_format($tx['amount'], 2) . " $currencySymbol" : '',
            (!empty($customerCode) && $customerCode !== 'All') 
                ? number_format($runningBalance, 2) . " $currencySymbol"
                : 'N/A'
        ];
    }

    // --------------------
    // 5. Return JSON
    // --------------------
    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalFiltered,
        "data" => $output
    ]);
    exit;
}

    public function SupplierStatement(){
        views('miscellaneous/supplierStatement.view.php');
    }

       public function statementSupplier()
{
    // var_dump($_POST);
    // exit;
        $accountId = $_POST['payer_id'] ?? null;
        if(!$accountId){
          die("<script>alert('Invalid SUpplier'); window.history.back();</script>");
        }
    $type      = $_POST['trans_type'] ?? 'All';
    $startDate = $_POST['sdate'] ?? null;
    $endDate   = $_POST['edate'] ?? null;

      views('miscellaneous/supplier_statement.view.php',[
        'accountId' => $accountId,
        'type' => $type,
        'startDate' => $startDate,
        'endDate' => $endDate
      ]);
  
       }

public function SupAjaxStatementTransactions()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    // DataTables params
    $draw        = $_GET['draw'] ?? 1;
    $start       = (int)($_GET['start'] ?? 0);
    $length      = (int)($_GET['length'] ?? 10);
    $searchValue = $_GET['search']['value'] ?? '';

    // Filters from form
    $customerCode = $_GET['pay_acc'] ?? null; // now customer_code
    $type         = $_GET['trans_type'] ?? 'All';
    $startDate    = date('Y-m-d', strtotime($_GET['sdate'] ?? 'now'));
    $endDate      = date('Y-m-d', strtotime($_GET['edate'] ?? 'now'));

    // --------------------
    // 1. Build WHERE clause
    // --------------------
    $whereParts = [];
    $params = [];

    if (!empty($customerCode) && $customerCode !== 'All') {
        $whereParts[] = "t.payer_id = :supplier_code";
        $params['supplier_code'] = $customerCode;
    }

    if ($type !== 'All') {
        $whereParts[] = "t.type = :type";
        $params['type'] = strtolower($type);
    }

    if (!empty($startDate) && !empty($endDate)) {
        $whereParts[] = "DATE(t.created_at) BETWEEN :startDate AND :endDate";
        $params['startDate'] = $startDate;
        $params['endDate']   = $endDate;
    }

    if (!empty($searchValue)) {
        $whereParts[] = "t.description LIKE :search";
        $params['search'] = '%' . $searchValue . '%';
    }

    $whereSQL = '';
    if (!empty($whereParts)) {
        $whereSQL = "WHERE " . implode(" AND ", $whereParts);
    }

    // --------------------
    // 2. Count total & filtered
    // --------------------
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM transactions t")->find()['total'];

    $filteredQuery = "SELECT COUNT(*) as total 
                      FROM transactions t
                      LEFT JOIN accounts a ON t.account_id = a.id
                      $whereSQL";
    $totalFiltered = !empty($whereParts)
        ? $db->query($filteredQuery, $params)->find()['total']
        : $totalRecords;

    // --------------------
    // 3. Get data (with currency_code from accounts)
    // --------------------
    $dataQuery = "SELECT 
            t.id,
            t.created_at,
            t.description,
            t.type,
            t.amount,
            t.payer_id,
            a.currency_code
        FROM transactions t
        LEFT JOIN accounts a ON t.account_id = a.id
        $whereSQL
        ORDER BY t.created_at ASC
        LIMIT $start, $length
    ";
    $transactions = $db->query($dataQuery, $params)->get();

    // --------------------
    // 4. Calculate running balance
    // --------------------
    $output = [];
    $runningBalance = null;
    $currencySymbol = '';

    if (!empty($customerCode) && $customerCode !== 'All') {
        // Step 1: Get the latest account_id used by this customer
        $account = $db->query(
            "SELECT account_id
             FROM transactions
             WHERE payer_id = :payer_id
             ORDER BY created_at DESC
             LIMIT 1",
            ['payer_id' => $customerCode]
        )->find();

        if ($account && !empty($account['account_id'])) {
            $accountId = $account['account_id'];

            // Step 2: Get account details
            $acc = $db->query(
                "SELECT current_balance, currency_code 
                 FROM accounts 
                 WHERE id = :id",
                ['id' => $accountId]
            )->find();

            if ($acc) {
                $runningBalance = (float) $acc['current_balance'];
                $currencySymbol = $acc['currency_code'] == 1 ? '£' : ($acc['currency_code'] == 2 ? '€' : '₵');
            }
        }
    }

    foreach ($transactions as $tx) {
        if (!empty($customerCode) && $customerCode !== 'All') {
            // Update running balance
            if (strtolower($tx['type']) === 'credit') {
                $runningBalance += $tx['amount'];
            } elseif (strtolower($tx['type']) === 'debit') {
                $runningBalance -= $tx['amount'];
            }
        } else {
            // Multiple customers — get currency per row from joined data
            $currencySymbol = $tx['currency_code'] == 1 ? '£' :
                              ($tx['currency_code'] == 2 ? '€' : '₵');
        }

        $output[] = [
            date('Y-m-d', strtotime($tx['created_at'])),
            htmlspecialchars($tx['description']),
            strtolower($tx['type']) === 'debit' ? number_format($tx['amount'], 2) . " $currencySymbol" : '',
            strtolower($tx['type']) === 'credit' ? number_format($tx['amount'], 2) . " $currencySymbol" : '',
            (!empty($customerCode) && $customerCode !== 'All') 
                ? number_format($runningBalance, 2) . " $currencySymbol"
                : 'N/A'
        ];
    }

    // --------------------
    // 5. Return JSON
    // --------------------
    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalFiltered,
        "data" => $output
    ]);
    exit;
}

public function IncomeCalculate() {
    header('Content-Type: application/json');
    $db = App::resolve(Database::class);

    $payAcc = $_POST['pay_acc'] ?? '0';
$sdate = isset($_POST['sdate']) ? date('Y-m-d', strtotime($_POST['sdate'])) : date('Y-m-d');
$edate = isset($_POST['edate']) ? date('Y-m-d', strtotime($_POST['edate'])) : date('Y-m-d');

$where = "WHERE DATE(created_at) BETWEEN :start AND :end";
$params = ['start' => $sdate, 'end' => $edate];

if (!empty($_POST['pay_acc']) && $_POST['pay_acc'] != '0') {
    $where .= " AND account_id = :acc";
    $params['acc'] = $_POST['pay_acc'];
}

$sql = "SELECT SUM(amount) as total 
        FROM transactions 
        $where AND type = 'credit'";

$totalIncome = $db->query($sql, $params)->find()['total'] ?? 0;
// var_dump($params);
// exit;

    echo json_encode([
        'status' => 'success',
        'total_income' => number_format($totalIncome, 2),
    ]);
}


public function income(){
    global $db;
    $accounts = $db->query("SELECT * FROM accounts")->get();
    views('miscellaneous/income.view.php',[
        'accounts' => $accounts
    ]);

}

public function expenseCalculate() {
    header('Content-Type: application/json');
    $db = App::resolve(Database::class);

    $payAcc = $_POST['pay_acc'] ?? '0';
$sdate = isset($_POST['sdate']) ? date('Y-m-d', strtotime($_POST['sdate'])) : date('Y-m-d');
$edate = isset($_POST['edate']) ? date('Y-m-d', strtotime($_POST['edate'])) : date('Y-m-d');

$where = "WHERE DATE(created_at) BETWEEN :start AND :end";
$params = ['start' => $sdate, 'end' => $edate];

if (!empty($_POST['pay_acc']) && $_POST['pay_acc'] != '0') {
    $where .= " AND account_id = :acc";
    $params['acc'] = $_POST['pay_acc'];
}

$sql = "SELECT SUM(amount) as total 
        FROM transactions 
        $where AND type = 'debit'";

$totalIncome = $db->query($sql, $params)->find()['total'] ?? 0;
// var_dump($params);
// exit;

    echo json_encode([
        'status' => 'success',
        'total_income' => number_format($totalIncome, 2),
    ]);
}

public function expense(){
    global $db;
    $accounts = $db->query("SELECT * FROM accounts")->get();
    views('miscellaneous/expense.view.php',[
        'accounts' => $accounts
    ]);

}

public function tax()
{
    views('miscellaneous/tax.view.php');
}

public function TaxData()
{
    // var_dump($_POST);
    // exit;

    $ty = $_POST['ty'] ?? null;
    $startDate = $_POST['sdate'] ?? null;
    $endDate   = $_POST['edate'] ?? null;

      views('miscellaneous/view_tax.view.php',[
        'startDate' => $startDate,
        'endDate' => $endDate,
        'ty' => $ty
      ]);
  
}    

public function ajaxList()
    {
        $db = App::resolve(Database::class);
        header('Content-Type: application/json');

        // Determine type and set table/column specifics
        $type = $_GET['ty'] ?? 'Purchases'; // Default to Purchases if not set
        if ($type === 'Sales') {
            $mainTable = 'invoices';
            $itemsTable = 'invoice_items';
            $partyTable = 'customers';
            $partyIdCol = 'customer_id';
            $partyCodeCol = 'customer_code';
            $partyNameCol = 'customer_name';
            $numberCol = 'invoice_number';
            $taxAlias = 'invoice_tax';
            $createdAtCol = 'invoices.created_at';
            $itemsIdCol = 'invoice_id';
            $tokenCol = 'public_token'; // Token column for Sales
        } else {
            $type = 'Purchases'; // Normalize
            $mainTable = 'purchases';
            $itemsTable = 'purchase_items';
            $partyTable = 'suppliers';
            $partyIdCol = 'supplier_id';
            $partyCodeCol = 'supplier_code';
            $partyNameCol = 'supplier_name';
            $numberCol = 'invoice_no';
            $taxAlias = 'purchase_tax';
            $createdAtCol = 'purchases.created_at';
            $itemsIdCol = 'purchase_id';
            $tokenCol = NULL; // No public_token for Purchases
        }

        // Validate DataTables parameters
       $draw = (int)$_GET['draw'] ?? 1;
    $start = (int) $_GET['start'] ?? 0;
    $length = (int) $_GET['length'] ?? 10;
    $searchValue = $_GET['search']['value'] ?? '';
    $startDate = date('Y-m-d', strtotime($_GET['sdate'])) ?? date('Y-m-d');
    $endDate = date('Y-m-d', strtotime($_GET['edate'])) ?? date('Y-m-d');

        // Count total records
        $totalRecordsQuery = "SELECT COUNT(*) as total FROM $mainTable";
        $totalRecords = $db->query($totalRecordsQuery)->find()['total'];

        // Base query
        $baseQuery = "FROM $mainTable
                      LEFT JOIN $partyTable ON $partyTable.id = $mainTable.$partyIdCol
                      LEFT JOIN $itemsTable ON $itemsTable.$itemsIdCol = $mainTable.id
                      LEFT JOIN transactions ON transactions.payer_id = $partyTable.$partyCodeCol
                      LEFT JOIN accounts ON accounts.id = transactions.account_id";

        // Build WHERE clause
        $where = [];
        $params = [];
        if (!empty($searchValue)) {
            $where[] = "($mainTable.$numberCol LIKE :search 
                        OR $partyTable.name LIKE :search 
                        OR $itemsTable.tax_amount LIKE :search)";
            $params['search'] = '%' . $searchValue . '%';
        }
        if (!empty($startDate) && !empty($endDate) && $endDate >= $startDate) {
            $where[] = "DATE($createdAtCol) BETWEEN :startDate AND :endDate";
            $params['startDate'] = $startDate;
            $params['endDate'] = $endDate;
        }
        $whereClause = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

        // Count filtered records
        $filteredQuery = "SELECT COUNT(DISTINCT $mainTable.id) as total " . $baseQuery . $whereClause;
        $totalFiltered = $db->query($filteredQuery, $params)->find()['total'];

        // Fetch paginated data
        $selectFields = "$mainTable.$numberCol as number, $mainTable.grand_total, $mainTable.id";
        $groupByFields = "$mainTable.id, $mainTable.$numberCol, $mainTable.grand_total";
        if ($tokenCol) {
            $selectFields .= ", $mainTable.$tokenCol as token";
            $groupByFields .= ", $mainTable.$tokenCol";
        }
        $selectFields .= ", SUM($itemsTable.tax_amount) as $taxAlias, $partyTable.$partyCodeCol as party_code, 
                         $partyTable.name as party_name, SUM(transactions.amount) as transaction_amount, 
                         transactions.account_id, accounts.currency_code";
        $groupByFields .= ", $partyTable.$partyCodeCol, $partyTable.name, transactions.account_id, accounts.currency_code";

        $dataQuery = "SELECT $selectFields " . $baseQuery . $whereClause . " 
                      GROUP BY $groupByFields
                      ORDER BY $mainTable.id DESC 
                      LIMIT " . (int)$start . ", " . (int)$length;
        $data = $db->query($dataQuery, $params)->get();

        // Process data
        $output = [];
        $counter = $start + 1;
        
        // Initialize running balance per account
        $runningBalances = [];
        foreach ($data as $row) {
            $accountId = $row['account_id'] ?? null;
            $currencySymbol = '₵'; // Default symbol
            
            // Fetch initial balance and currency
            if ($accountId && !isset($runningBalances[$accountId])) {
                $acc = $db->query("SELECT current_balance, currency_code FROM accounts WHERE id = :id", ['id' => $accountId])->find();
                $runningBalances[$accountId] = (float)($acc['current_balance'] ?? 0);
                $currencySymbol = $this->getCurrencySymbol($acc['currency_code'] ?? 0);
            } elseif ($accountId) {
                $currencySymbol = $this->getCurrencySymbol($row['currency_code'] ?? 0);
            }

            // Update running balance
            if ($accountId) {
                $amount = (float)($row['transaction_amount'] ?? 0);
                if ($type === 'Purchases') {
                    $amount = -$amount; // Subtract for outflows in purchases
                }
                $runningBalances[$accountId] += $amount;
            }

            $output[] = [
                $counter++,
                $row['number'],
                $row['party_name'],
                number_format($row['grand_total'], 2),
                number_format($row[$taxAlias] ?? 0, 2),
                $accountId ? number_format($runningBalances[$accountId], 2) . " $currencySymbol" : 'N/A'
            ];
        }

        // Return JSON response
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $output
        ]);
        exit;
    }


    // Helper method (add this outside the function or in a class)
private function getCurrencySymbol($code) {
    return $code == 1 ? '£' : ($code == 2 ? '€' : '₵');
}
}