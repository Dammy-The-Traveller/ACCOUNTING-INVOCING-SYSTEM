<?php 
namespace Http\Controllers\account;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

global $db;
class Account{
    public function create(){
    $db = App::resolve(Database::class);
    if (isset($_GET['id'])) {
       $accountId = (int) $_GET['id'];


        if (!$accountId) {
            die("<script>alert('Invalid group'); window.history.back();</script>");
        }

        // Fetch purchase
        $account = $db->query("SELECT * FROM accounts WHERE id = :id", [
            'id' => $accountId
        ])->find();

     
        if (!$account) {
            die("<script>alert('Accounts not found.'); window.history.back();</script>");
        }
        return views('account/index.view.php', [
            'account' => $account,
         
        ]);
    }

    // New purchase
       return views('account/index.view.php');
    }

    public function store(){
    global $db;

     $accountNo       = clean($_POST['accno']) ?? '';
    $accountCode     = clean($_POST['acccode']) ?? '';
    $accountName     = clean($_POST['holder']) ?? '';
    $initialBalance  = clean($_POST['intbal']) ?? 0;
    $note            = clean($_POST['acode']) ?? '';
    $currencyId      = (int)$_POST['mcurrency'] ?? '';
    $status          = clean($_POST['status']) ?? 'active';


            $errors = [];

if (!Validator::string( $accountNo, 2, 20)) {
    $errors['account'] = 'Please provide a valid account No not less than 5 characters.';
}


if (!Validator::string( $accountName, 2, 255)) {
    $errors['name'] = 'Please provide a valid name.';
}




if (! empty($errors)) {
    return views('account/index.view.php', [
        'errors' => $errors
    ]);
}
    // Validation


    // Insert into DB
    try {
        $db->query("INSERT INTO accounts 
                (code, name, note, account_number, initial_balance, current_balance, currency_code, status, created_at, updated_at)
            VALUES 
                (:code, :name, :note, :account_number, :initial_balance, :current_balance, :currency_code, :status, NOW(), NOW())
        ", [
            'code'            => $accountCode,
            'name'            => $accountName,
            'note'            => $note,
            'account_number'  => $accountNo,
            'initial_balance' => $initialBalance,
            'current_balance' => $initialBalance, // Start with initial balance
            'currency_code'   => $currencyId ,
            'status'          => $status
        ]);


        $_SESSION['success'] = '<strong>Success</strong>: Account created successfully!';
        redirect('/AIS/account');
        exit;
    } catch (Exception $e) {
          $_SESSION['success'] = '<strong>Success</strong>: Failed to create account!';
        redirect('/AIS/account');
        exit;
    }

   
    }

   public function ajaxList()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    $draw = $_GET['draw'] ?? 1;
    $start = $_GET['start'] ?? 0;
    $length = $_GET['length'] ?? 10;
    $searchValue = $_GET['search']['value'] ?? '';

    $where = "WHERE status IN ('active', 'inactive')";
    $params = [];

    if (!empty($searchValue)) {
        $where .= " AND (name LIKE :search OR code LIKE :search OR account_number LIKE :search)";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Total records
    $totalQuery = "SELECT COUNT(*) as total FROM accounts WHERE status IN ('active', 'inactive')";
    $totalRecords = $db->query($totalQuery)->find()['total'];

    // Filtered records
    $filteredQuery = "SELECT COUNT(*) as total FROM accounts $where";
    $totalFiltered = !empty($searchValue)
        ? $db->query($filteredQuery, $params)->find()['total']
        : $totalRecords;

    // Main data query
    $dataQuery = "SELECT id, code, name, account_number, current_balance, currency_code
        FROM accounts
        $where
        ORDER BY id DESC
        LIMIT $start, $length
    ";

    $accounts = $db->query($dataQuery, $params)->get();

    $output = [];
    $counter = $start + 1;

    foreach ($accounts as $account) {
        // Status formatting
 if($account['currency_code'] == 1){
                    $currencySymbol = '£';
                } elseif($account['currency_code'] == 2){
                    $currencySymbol = '€';
                    } else {
                    $currencySymbol = "₵";
                }  
 

 $output[] = [
    $counter++,
    htmlspecialchars($account['account_number'] ?? ''),
    htmlspecialchars($account['name'] ?? ''),
    number_format((float) $account['current_balance'], 2) . ' ' . htmlspecialchars($currencySymbol ?? ''),
    '
    <a href="/AIS/account-manage-view?id=' . $account['id'] . '" class="btn btn-success btn-xs"><i class="icon-file-text"></i> View</a>
    <a href="/AIS/account-edit?id=' . $account['id'] . '" class="btn btn-warning btn-xs">Edit</a>
    <a href="#" data-object-id="' . $account['id'] . '" class="btn btn-danger btn-xs delete-object"><i class="icon-trash-o"></i></a>'
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

 public function manage()
{
    $db = App::resolve(Database::class);

    // Count all accounts
    $accountCount = $db->query(
        "SELECT COUNT(*) AS total FROM accounts"
    )->find()['total'];

    // Sum of all balances
    $totalBalance = $db->query(
        "SELECT SUM(current_balance) AS total_balance FROM accounts"
    )->find()['total_balance'] ?? 0;

    // Pass data to the view
    views('account/manage.view.php', [
        'accountCount'  => $accountCount,
        'totalBalance'  => $totalBalance
    ]);
}

public function view(){
    $db = App::resolve(Database::class);

    $id = (int)$_GET['id'];
    $account = $db->query("SELECT * FROM accounts WHERE id = ?", [$id])->find();

    // var_dump($account);
    // exit;
        views('account/view.view.php', [
        'account'  => $account
    ]);
}

  public function update(){
    global $db;

    $accountId = (int)$_POST['id'];
     if (!$accountId ) {
        abort(400);
        die("<script>alert('Your Account does not exist. Try again.'); window.history.back();</script>");
      
    }
     $accountNo       = clean($_POST['accno']) ?? '';
    $accountCode     = clean($_POST['acccode']) ?? '';
    $accountName     = clean($_POST['holder']) ?? '';
    $initialBalance  = clean($_POST['intbal']) ?? 0;
    $note            = clean($_POST['acode']) ?? '';
    $currencyId      = (int)$_POST['mcurrency'] ?? '';
    $status          = clean($_POST['status']) ?? 'active';


            $errors = [];

if (!Validator::string( $accountNo, 2, 20)) {
    $errors['account'] = 'Please provide a valid account No not less than 5 characters.';
}


if (!Validator::string( $accountName, 2, 255)) {
    $errors['name'] = 'Please provide a valid name.';
}




if (! empty($errors)) {
    return views('account/index.view.php', [
        'errors' => $errors
    ]);
}
    // Validation


    // Insert into DB
    try {
         $db->beginTransaction();
      $update =  $db->query(" UPDATE accounts SET
        code            = :code,
        name            = :name,
        note            = :note,
        account_number  = :account_number,
        initial_balance = :initial_balance,
        current_balance = :current_balance,
        currency_code   = :currency_code,
        status          = :status,
        updated_at      = NOW()
    WHERE id = :id
", [
    'code'            => $accountCode,
    'name'            => $accountName,
    'note'            => $note,
    'account_number'  => $accountNo,
    'initial_balance' => $initialBalance,
    'current_balance' => $initialBalance, // You can pass a different value if needed
    'currency_code'   => $currencyId,
    'status'          => $status,
    'id'              => $accountId // Make sure this is the correct account's ID
]);

 $db->commit();
if ($update) {
    $_SESSION['success'] = '<strong>Success</strong>: Account updated successfully!';
        redirect('/AIS/account?id='. $accountId .'');
        exit;
}else{
      $_SESSION['success'] = '<strong>Success</strong>: Failed to update account!';
        redirect('/AIS/account?id='. $accountId .'');
        exit; 
}
       
    } catch (Exception $e) {
           $db->rollBack();
          $_SESSION['success'] = '<strong>Success</strong>: Failed to update account!';
        redirect('/AIS/account?id='. $accountId .'');
        exit;
    }

   
    }

public function delete()
{
    header('Content-Type: application/json');
    $db = App::resolve(Database::class);

    $accountId = (int)($_GET['id'] ?? 0);

    if ($accountId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Account ID.']);
        return;
    }

    try {
        $db->beginTransaction();

        // Fetch account
        $account = $db->query("SELECT * FROM accounts WHERE id = ?", [$accountId])->find();

        if (!$account) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Account not found.']);
            return;
        }

        // Delete account
        $db->query("DELETE FROM accounts WHERE id = ?", [$accountId]);

        $db->commit();
        echo json_encode(['status' => 'success', 'message' => 'Account deleted successfully.']);

    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete account. '
        ]);
    }
}

public function balancesheet(){

    $db = App::resolve(Database::class);

     $accountCount = $db->query(
        "SELECT * FROM accounts"
    )->get();

      $totalBalance = $db->query(
        "SELECT SUM(current_balance) AS total_balance FROM accounts"
    )->find()['total_balance'] ?? 0;

        views('account/balancesheet.view.php', [
        'accountCount'  => $accountCount,
         'totalBalance'  => $totalBalance
    ]);
}

public function statement()
{
    $db = App::resolve(Database::class);

    // Fetch accounts from the DB
    $accounts = $db->query("SELECT id, account_number, name FROM accounts")->get();

    views('account/statement.view.php', [
        'accounts' => $accounts
    ]);
}

public function statementData()
{
    // var_dump($_POST);
    // exit;
        $accountId = $_POST['pay_acc'] ?? null;
    $type      = $_POST['trans_type'] ?? 'All';
    $startDate = $_POST['sdate'] ?? null;
    $endDate   = $_POST['edate'] ?? null;

      views('account/view_statement.view.php',[
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
   $start  = (int)($_GET['start'] ?? 0);
$length = (int)($_GET['length'] ?? 10);
    $searchValue = $_GET['search']['value'] ?? '';

    // Filters from form
    $accountId = $_GET['pay_acc'] ?? null;
    $type      = $_GET['trans_type'] ?? 'All';
   $startDate = date('Y-m-d', strtotime($_GET['sdate']));
$endDate   = date('Y-m-d', strtotime($_GET['edate']));

    // --------------------
    // 1. Build WHERE clause
    // --------------------
    $whereParts = [];
    $params = [];

    if (!empty($accountId) && $accountId !== 'All') {
        $whereParts[] = "t.account_id = :account_id";
        $params['account_id'] = $accountId;
    }

    if ($type !== 'All') {
        $whereParts[] = "t.type = :type";
        $params['type'] = strtolower($type); // expecting 'credit' or 'debit'
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

    $filteredQuery = "SELECT COUNT(*) as total FROM transactions t $whereSQL";
    $totalFiltered = !empty($whereParts)
        ? $db->query($filteredQuery, $params)->find()['total']
        : $totalRecords;

    // --------------------
    // 3. Get data
    // --------------------
$dataQuery = "
    SELECT 
        t.id,
        t.created_at,
        t.description,
        t.type,
        t.amount,
        t.account_id,
        a.account_number,
        a.name AS account_name,
        a.currency_code,
        a.current_balance
    FROM transactions t
    INNER JOIN accounts a ON a.id = t.account_id
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

    if (!empty($accountId) && $accountId !== 'All') {
        // Get initial balance for the account
        $acc = $db->query("SELECT current_balance, currency_code FROM accounts WHERE id = :id", ['id' => $accountId])->find();
        $runningBalance = (float) $acc['current_balance'];
        $currencySymbol = $acc['currency_code'] == 1 ? '£' : ($acc['currency_code'] == 2 ? '€' : '₵');
    }

    foreach ($transactions as $tx) {
        if (!empty($accountId) && $accountId !== 'All') {
            // Update running balance
            if (strtolower($tx['type']) === 'credit') {
                $runningBalance += $tx['amount'];
            } elseif (strtolower($tx['type']) === 'debit') {
                $runningBalance -= $tx['amount'];
            }
        } else {
            // Multiple accounts — get currency per row
            $currencySymbol = $tx['currency_code'] == 1 ? '£' : ($tx['currency_code'] == 2 ? '€' : '₵');
        }

        $output[] = [
            date('Y-m-d', strtotime($tx['created_at'])),
            htmlspecialchars($tx['description']),
            strtolower($tx['type']) === 'debit' ? number_format($tx['amount'], 2) . " $currencySymbol" : '',
            strtolower($tx['type']) === 'credit' ? number_format($tx['amount'], 2) . " $currencySymbol" : '',
            (!empty($accountId) && $accountId !== 'All') 
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


public function AjaxAllTransactions()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    $draw   = $_GET['draw'] ?? 1;
    $start  = (int)($_GET['start'] ?? 0);
    $length = (int)($_GET['length'] ?? 10);
    $searchValue = $_GET['search']['value'] ?? '';

    $whereSQL = '';
    $params = [];
    if (!empty($searchValue)) {
        $whereSQL = "WHERE 
            t.description LIKE :search OR 
            a.name LIKE :search OR 
            c.name LIKE :search OR
            u.name LIKE :search";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Total records
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM transactions")->find()['total'];

    // Total filtered
    $filteredQuery = "SELECT COUNT(*) as total 
        FROM transactions t
        INNER JOIN accounts a ON a.id = t.account_id
        LEFT JOIN customers c ON c.customer_code = t.payer_id
        LEFT JOIN users u ON u.id = t.payer_id
        LEFT JOIN payments p ON p.id = t.payment_id
        $whereSQL
    ";
    $totalFiltered = !empty($searchValue)
        ? $db->query($filteredQuery, $params)->find()['total']
        : $totalRecords;

    // Main data query with fallback
    $dataQuery = "SELECT 
        t.id,
        t.created_at,
        a.name AS account_name,
        t.type,
        t.amount,
        -- Payment method fallback: payments -> transactions -> default
        COALESCE(NULLIF(p.payment_method, ''), NULLIF(t.payment_method, ''), 'Cash') AS payment_method,
        -- Customer name fallback: customers -> users -> default
        COALESCE(NULLIF(c.name, ''), NULLIF(u.firstname, ''), 'Walk-in Customer') AS customer_name,
        a.currency_code
    FROM transactions t
    INNER JOIN accounts a ON a.id = t.account_id
    LEFT JOIN customers c ON c.customer_code = t.payer_id
    LEFT JOIN users u ON u.id = t.payer_id
    LEFT JOIN payments p ON p.id = t.payment_id
    $whereSQL
    ORDER BY t.created_at DESC
    LIMIT {$start}, {$length}
    ";

    $transactions = $db->query($dataQuery, $params)->get();

    $output = [];
    foreach ($transactions as $tx) {
        $currencySymbol = $tx['currency_code'] == 1 ? '£' : ($tx['currency_code'] == 2 ? '€' : '₵');

        $debit = strtolower((string)($tx['type'] ?? '')) === 'debit'
            ? number_format($tx['amount'], 2) . " $currencySymbol"
            : '';
        $credit = strtolower((string)($tx['type'] ?? '')) === 'credit'
            ? number_format($tx['amount'], 2) . " $currencySymbol"
            : '';

        // Payment method display
        $method = strtolower($tx['payment_method']);
        if (in_array($method, ['stripe', 'paystack'])) {
            $method = 'Card';
        } else {
            $method = ucfirst($method);
        }

        $output[] = [
            date('Y-m-d', strtotime($tx['created_at'])),
            htmlspecialchars($tx['account_name']),
            $debit,
            $credit,
            htmlspecialchars($tx['customer_name']),
            htmlspecialchars($method),
            '
<a href="/AIS/transaction-view?id=' . $tx['id'] . '" class="btn btn-success btn-xs"><i class="icon-file-text"></i> View</a>
<a href="/AIS/trans-generate?id=' . $tx['id'] . '" target="_blank" class="btn btn-info btn-xs"><span class="icon-print"></span></a>
<a href="#" data-object-id="' . $tx['id'] . '" class="btn btn-danger btn-xs delete-object"><i class="icon-trash-o"></i></a>'
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


public function transaction()
{


    views('account/transaction.view.php');
}

public function transactionView()
{
    $db = App::resolve(Database::class);

   $transactionId = (int) $_GET['id'];


        if (!$transactionId) {
            die("<script>alert('Invalid Transaction'); window.history.back();</script>");
        }

        $transaction = $db->query("SELECT * FROM transactions WHERE id = :id", [
            'id' => $transactionId
        ])->find();
           // Fetch purchase
        if (!$transaction) {
            $transaction =[];
        }
            $customer = $db->query("SELECT * FROM customers WHERE customer_code = :id", [
            'id' => $transaction['payer_id']
        ])->find();

           if (!$customer) {
    $customer = $db->query("SELECT * FROM suppliers WHERE supplier_code = :id", [
            'id' => $transaction['payer_id']
        ])->find();
 if (!$customer) {
            $customer = [];
 }
        }


      $category = $db->query("SELECT * FROM categories WHERE id = :id", [
            'id' => $transaction['category_id']
        ])->find();

          if (!$category) {
            $category = [];
        }
     
    views('account/transactions.view.php',[
        'transaction'=> $transaction,
        'customer'=> $customer,
        'category'=> $category,
        'transactionId'=> $transactionId
    ]);
}

public function printTransactionPDF()
{
    // Resolve database connection (adjust to your DB code)
    $db = App::resolve(Database::class);
    $transactionId = (int) $_GET['id'];
       if (!$transactionId) {
        die("Transaction not found");
    }
    // Fetch transaction with joins for related data
$transaction = $db->query("
    SELECT 
        t.id,
        t.created_at,
        t.type,
        t.amount,
        t.description,
        a.name AS account_name,
        a.currency_code,
        c.address AS customer_address,
        c.phone AS customer_phone,
        c.email AS customer_email,
        cat.name AS category_name,
        -- Payment method fallback: payments -> transactions -> default
        COALESCE(NULLIF(p.payment_method, ''), NULLIF(t.payment_method, ''), 'Cash') AS payment_method,
        -- Customer name fallback: customers -> users -> default
        COALESCE(NULLIF(c.name, ''), NULLIF(u.firstname, ''), 'ADMIN') AS customer_name
    FROM transactions t
    INNER JOIN accounts a ON a.id = t.account_id
    LEFT JOIN customers c ON c.customer_code = t.payer_id
    LEFT JOIN payments p ON p.id = t.payment_id
    LEFT JOIN categories cat ON cat.id = t.category_id
    LEFT JOIN users u ON u.id = t.payer_id
    WHERE t.id = :id
", ['id' => $transactionId])->find();


// var_dump($transaction);
// exit;
    if (!$transaction) {
        die("Transaction not found");
    }

    // Fake company info (replace with DB values if you have them)
    $company = [
        'name'    => 'DAMMY TECH',
        'address' => 'K3 Dove Street Golf Hills, Achimota,',
        'phone'   => '+233531102302',
        'email'   => 'info@dtt.com'
    ];

    // Determine debit/credit values
    $debit = strtolower($transaction['type']) === 'debit' ? $transaction['amount'] : 0;
    $credit = strtolower($transaction['type']) === 'credit' ? $transaction['amount'] : 0;

    // Currency symbol
    $currencySymbol = $transaction['currency_code'] == 1 ? '£' :
                     ($transaction['currency_code'] == 2 ? '€' : '$');

    // If stripe or paystack => Card
    if (in_array(strtolower($transaction['payment_method']), ['stripe', 'paystack'])) {
        $transaction['payment_method'] = 'Card';
    }

    // HTML template for PDF
    ob_start();
    ?>
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #1e1e1e;
                color: #fff;
                font-size: 14px;
            }
            hr {
                border: none;
                border-top: 1px solid #555;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            td {
                vertical-align: top;
                padding: 4px 6px;
            }
            .bold { font-weight: bold; }
            .right { text-align: right; }
        </style>
    </head>
    <body>
        <p class="bold">Transaction Details ID : TRN#<?= $transaction['id'] ?></p>
        <hr>
        <table>
            <tr>
                <td>Date : <?= date('d-m-Y', strtotime($transaction['created_at'])) ?></td>
                <td>Transaction ID : TRN#<?= $transaction['id'] ?></td>
                <td>Category : <?= htmlspecialchars($transaction['category_name'] ?? '') ?></td>
            </tr>
        </table>
        <hr>
        <table>
            <tr>
                <td class="bold"><?= $company['name'] ?></td>
                <td class="bold"><?= htmlspecialchars($transaction['customer_name'] ?? 'N/A') ?></td>
                <td class="right">Debit : <?= $currencySymbol . ' ' . number_format($debit, 2) ?></td>
            </tr>
            <tr>
                <td><?= $company['address'] ?></td>
                <td><?= htmlspecialchars($transaction['customer_address'] ?? '') ?></td>
                <td class="right">Credit : <?= $currencySymbol . ' ' . number_format($credit, 2) ?></td>
            </tr>
            <tr>
                <td>Phone: <?= $company['phone'] ?></td>
                <td>Phone: <?= htmlspecialchars($transaction['customer_phone'] ?? '') ?></td>
                <td class="right">Type : <?= ucfirst($transaction['type']) ?></td>
            </tr>
            <tr>
                <td>Email: <?= $company['email'] ?></td>
                <td>Email: <?= htmlspecialchars($transaction['customer_email'] ?? '') ?></td>
                <td></td>
            </tr>
        </table>
        <br>
        <p>Note : <?= htmlspecialchars($transaction['description'] ?? '') ?></p>
    </body>
    </html>
    <?php
    $html = ob_get_clean();

    // Initialize Dompdf
    $options = new Options();
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Stream to browser without forcing download
    $dompdf->stream("transaction_{$transaction['id']}.pdf", ["Attachment" => false]);
}

public function deleteTransaction()
{
     $db = App::resolve(Database::class);
    $id = (int) $_GET['id'];
    // Validate ID (must be numeric)
    if (!is_numeric($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid transaction ID.']);
        return;
    }
    $db->beginTransaction();

    $transaction = $db->query(
        "SELECT account_id, type, amount FROM transactions WHERE id = :id",
        ['id' => $id]
    )->find();

    if (!$transaction) {
          echo json_encode(['status' => 'error', 'message' => 'Invalid transaction.']);
        return;
    }

    // 2. Adjust account balance
    if (strtolower($transaction['type']) === 'credit') {
        // Credit increases balance, so we reverse by subtracting
        $db->query(
            "UPDATE accounts SET current_balance = current_balance - :amount WHERE id = :acc_id",
            ['amount' => $transaction['amount'], 'acc_id' => $transaction['account_id']]
        );
    } elseif (strtolower($transaction['type']) === 'debit') {
        // Debit decreases balance, so we reverse by adding
        $db->query(
            "UPDATE accounts SET current_balance = current_balance + :amount WHERE id = :acc_id",
            ['amount' => $transaction['amount'], 'acc_id' => $transaction['account_id']]
        );
    }
    // Delete transaction
    $deleted = $db->query(
        "DELETE FROM transactions WHERE id = :id",
        ['id' => $id]
    );
 $db->commit();

    if ($deleted) {

        echo json_encode(['status' => 'success', 'message' => 'Transaction deleted and account balance updated.']);
    } else {
           $db->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete transaction. '
        ]);
    }
}


public function add(){

    $db = App::resolve(Database::class);

        if (isset($_GET['id'])) {
       $Id = (int) $_GET['id'];


        if (!$Id) {
            die("<script>alert('Invalid Transaction'); window.history.back();</script>");
        }

        // Fetch purchase
      

           $transaction = $db->query("SELECT * FROM transactions WHERE id = :id", [
            'id' => $Id
        ])->find();
     
        if (!$transaction) {
            die("<script>alert('Transaction not found.'); window.history.back();</script>");
        }


          $account = $db->query("SELECT * FROM accounts WHERE id = :id", [
            'id' => $transaction['account_id']
        ])->find();

            if (!$account) {
            die("<script>alert('Account not found.'); window.history.back();</script>");
        }

        $categories = $db->query("SELECT * FROM categories where id=:id", [
            'id'=>$transaction['category_id']
        ])->get();

                  if (!$categories) {
            die("<script>alert('Account not found.'); window.history.back();</script>");
        }

        return views('account/add.view.php', [
            'account' => $account,
              'categories'=> $categories,
              'transaction'=>$transaction
         
        ]);
    }
        $accounts = $db->query("SELECT * FROM accounts")->get();
   $categories = $db->query("SELECT * FROM categories")->get();


    views('account/add.view.php',[
        'accounts'=>$accounts,
        'categories'=> $categories
    ]);
}

public function storeTransaction()
{
    $db = App::resolve(Database::class);

    // Validate required fields
    $required = ['payer_id', 'pay_acc', 'date', 'amount', 'pay_type', 'pay_cat', 'paymethod'];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['success'] = "Missing required field: {$field}";
            header("Location: /AIS/addTrans");
            exit;
        }
    }

    $payer_id   = (int) $_POST['payer_id'];
    $account_id = (int) $_POST['pay_acc'];
    $date       = $_POST['date'];
    $amount     = (float) $_POST['amount'];
    $type       = $_POST['pay_type'];  // Income | Expense
    $category   = $_POST['pay_cat'];
    $method     = $_POST['paymethod'];
    $note       = clean($_POST['note']) ?? '';

    // Adjust amount sign for Expenses
    $debit  = $type === "Expense" ? $amount : 0;
    $credit = $type === "Income" ? $amount : 0;

    // Insert into transactions table
    $db->query("INSERT INTO transactions 
            (account_id, type, amount, payer_id, category_id,description,  payment_method,  created_at) 
        VALUES 
            (:account_id, :type, :amount, :payer_id, 
             :category_id, :description, :payment_method, :date)
    ", [
        'account_id' => $account_id,
        'type' => $type,
        'amount'=> $amount,
        'payer_id'   => $payer_id,
        'category_id'=> $category,
        'description'=> $note,
        'payment_method'=> $method,
        'date'=>  date('Y-m-d H:i:s', strtotime($date)),
    ]);

    // Update account balance
    if ($type === "Income") {
        $db->query("
            UPDATE accounts SET current_balance = current_balance + :amount 
            WHERE id = :id
        ", ['amount' => $amount, 'id' => $account_id]);
    } else {
        $db->query("
            UPDATE accounts SET current_balance = current_balance - :amount 
            WHERE id = :id
        ", ['amount' => $amount, 'id' => $account_id]);
    }

    $_SESSION['success'] = "Transaction added successfully.";
    header("Location: /AIS/addTrans");
    exit;
}

public function transfer(){

    $db = App::resolve(Database::class);
  
        $accounts = $db->query("SELECT * FROM accounts")->get();

    views('account/transfer.view.php',[
        'accounts'=>$accounts
    ]);
}

public function saveTransfer()
{
    $db = App::resolve(Database::class);

    $from_account = (int) $_POST['pay_acc'];
    $to_account   = (int) $_POST['pay_acc2'];
    $amount       = (float) $_POST['amount'];

   
    // 1️⃣ Validate
    if ($from_account === $to_account) {
        $_SESSION['success'] = "Source and destination accounts cannot be the same.";
        header("Location: /AIS/Transfer");
        exit;
    }

    if ($amount <= 0) {
        $_SESSION['success'] = "Invalid amount.";
        header("Location: /AIS/Transfer");
        exit;
    }
 try {
      $db->beginTransaction();
    // Check if source account has enough balance
    $balance = $db->query("SELECT current_balance, name FROM accounts WHERE id = :id", [
        'id' => $from_account
    ])->find();


      $balanceTo = $db->query("SELECT current_balance, name FROM accounts WHERE id = :id", [
        'id' => $to_account
    ])->find();
    if (!$balance || !$balanceTo || $balance['current_balance'] < $amount) {
        $_SESSION['success'] = "Insufficient balance in source account.";
        header("Location: /AIS/Transfer");
        exit;
    }

    // $date = date('Y-m-d ');
    // $created_at = time();

 
    // 2️⃣ Record outgoing transaction (Expense)
    $db->query(" INSERT INTO transactions (account_id, payment_id,type, amount, payer_id,  category_id, description, payment_method, created_at)
        VALUES (:account_id, :payment_id, :type, :amount, :payer_id,  :category_id, :description, :payment_method, NOW())
    ", [
        'account_id' => $from_account,
        'payment_id'=> 0,
        'type'      => 'debit',
        'amount'      => $amount,
        'payer_id' => $_SESSION['user']['ID'],
        'category_id' => 0,
        'description'       => "Transfer to account {$balanceTo['name']} with ID of {$to_account}",
         'payment_method' => 'Web'
        
    ]);

    //    var_dump($balance);
    // exit;
    // 3️⃣ Record incoming transaction (Income)
  $db->query(" INSERT INTO transactions (account_id, payment_id,type, amount, payer_id,  category_id, description, payment_method, created_at)
        VALUES (:account_id, :payment_id, :type, :amount, :payer_id,  :category_id, :description, :payment_method, NOW())
    ", [
           'account_id' => $to_account,
        'payment_id'=> 0,
        'type'      => 'credit',
        'amount'      => $amount,
        'payer_id' => $_SESSION['user']['ID'],
        'category_id' => 0,
        'description'       => "Transfer to account {$balance['name']} with ID of {$from_account}",
        'payment_method' => 'Web'
    ]);

    // 4️⃣ Update balances
    $db->query(" UPDATE accounts 
        SET current_balance = current_balance - :amount 
        WHERE id = :id
    ", ['amount' => $amount, 'id' => $from_account]);

    $db->query(" UPDATE accounts 
        SET current_balance = current_balance + :amount 
        WHERE id = :id
    ", ['amount' => $amount, 'id' => $to_account]);

     $db->commit();
    $_SESSION['success'] = "Transfer completed successfully.";
    header("Location: /AIS/Transfer");
    exit;

    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['success'] = "Transfer Failed.";
    header("Location: /AIS/Transfer");
    exit;
    }
}

    public function income() {
           views('account/incomes.view.php');
    }

    public function IncomeAjaxAllTransactions()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    $draw   = $_GET['draw'] ?? 1;
    $start  = (int)($_GET['start'] ?? 0);
    $length = (int)($_GET['length'] ?? 10);
    $searchValue = $_GET['search']['value'] ?? '';

   $whereSQL = "WHERE t.type = 'credit'";
    $params = [];
if (!empty($searchValue)) {
    $whereSQL .= " AND (
        t.description LIKE :search OR 
        a.name LIKE :search OR 
        c.name LIKE :search OR
        u.name LIKE :search
    )";
    $params['search'] = '%' . $searchValue . '%';
}

    // Total records
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM transactions")->find()['total'];

    // Total filtered
    $filteredQuery = "SELECT COUNT(*) as total 
        FROM transactions t
        INNER JOIN accounts a ON a.id = t.account_id
        LEFT JOIN customers c ON c.customer_code = t.payer_id
        LEFT JOIN users u ON u.id = t.payer_id
        LEFT JOIN payments p ON p.id = t.payment_id
        $whereSQL
    ";
    $totalFiltered = !empty($searchValue)
        ? $db->query($filteredQuery, $params)->find()['total']
        : $totalRecords;

    // Main data query with fallback
    $dataQuery = "SELECT 
        t.id,
        t.created_at,
        a.name AS account_name,
        t.type,
        t.amount,
        -- Payment method fallback: payments -> transactions -> default
        COALESCE(NULLIF(p.payment_method, ''), NULLIF(t.payment_method, ''), 'Cash') AS payment_method,
        -- Customer name fallback: customers -> users -> default
        COALESCE(NULLIF(c.name, ''), NULLIF(u.firstname, ''), 'ADMIN') AS customer_name,
        a.currency_code
    FROM transactions t
    INNER JOIN accounts a ON a.id = t.account_id
    LEFT JOIN customers c ON c.customer_code = t.payer_id
    LEFT JOIN users u ON u.id = t.payer_id
    LEFT JOIN payments p ON p.id = t.payment_id
    $whereSQL
    ORDER BY t.created_at DESC
    LIMIT {$start}, {$length}
    ";

    $transactions = $db->query($dataQuery, $params)->get();

    $output = [];
    foreach ($transactions as $tx) {
        $currencySymbol = $tx['currency_code'] == 1 ? '£' : ($tx['currency_code'] == 2 ? '€' : '₵');

        $credit = strtolower((string)($tx['type'] ?? '')) === 'credit'
            ? number_format($tx['amount'], 2) . " $currencySymbol"
            : '';

        // Payment method display
        $method = strtolower($tx['payment_method']);
        if (in_array($method, ['stripe', 'paystack'])) {
            $method = 'Card';
        } else {
            $method = ucfirst($method);
        }

        $output[] = [
            date('Y-m-d', strtotime($tx['created_at'])),
            htmlspecialchars($tx['account_name']),
            $credit,
            htmlspecialchars($tx['customer_name']),
            htmlspecialchars($method),
            '
<a href="/AIS/transaction-view?id=' . $tx['id'] . '" class="btn btn-success btn-xs"><i class="icon-file-text"></i> View</a>
<a href="/AIS/trans-generate?id=' . $tx['id'] . '" target="_blank" class="btn btn-info btn-xs"><span class="icon-print"></span></a>
<a href="#" data-object-id="' . $tx['id'] . '" class="btn btn-danger btn-xs delete-object"><i class="icon-trash-o"></i></a>'
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

  public function expense() {
           views('account/expense.view.php');
    }

    public function ExpenseAjaxAllTransactions()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    $draw   = $_GET['draw'] ?? 1;
    $start  = (int)($_GET['start'] ?? 0);
    $length = (int)($_GET['length'] ?? 10);
    $searchValue = $_GET['search']['value'] ?? '';

   $whereSQL = "WHERE t.type = 'debit'";
    $params = [];
if (!empty($searchValue)) {
    $whereSQL .= " AND (
        t.description LIKE :search OR 
        a.name LIKE :search OR 
        c.name LIKE :search OR
        u.name LIKE :search
    )";
    $params['search'] = '%' . $searchValue . '%';
}

    // Total records
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM transactions")->find()['total'];

    // Total filtered
    $filteredQuery = "SELECT COUNT(*) as total 
        FROM transactions t
        INNER JOIN accounts a ON a.id = t.account_id
        LEFT JOIN customers c ON c.customer_code = t.payer_id
        LEFT JOIN users u ON u.id = t.payer_id
        LEFT JOIN payments p ON p.id = t.payment_id
        $whereSQL
    ";
    $totalFiltered = !empty($searchValue)
        ? $db->query($filteredQuery, $params)->find()['total']
        : $totalRecords;

    // Main data query with fallback
    $dataQuery = "SELECT 
        t.id,
        t.created_at,
        a.name AS account_name,
        t.type,
        t.amount,
        -- Payment method fallback: payments -> transactions -> default
        COALESCE(NULLIF(p.payment_method, ''), NULLIF(t.payment_method, ''), 'Cash') AS payment_method,
        -- Customer name fallback: customers -> users -> default
        COALESCE(NULLIF(c.name, ''), NULLIF(u.firstname, ''), 'ADMIN') AS customer_name,
        a.currency_code
    FROM transactions t
    INNER JOIN accounts a ON a.id = t.account_id
    LEFT JOIN customers c ON c.customer_code = t.payer_id
    LEFT JOIN users u ON u.id = t.payer_id
    LEFT JOIN payments p ON p.id = t.payment_id
    $whereSQL
    ORDER BY t.created_at DESC
    LIMIT {$start}, {$length}
    ";

    $transactions = $db->query($dataQuery, $params)->get();

    $output = [];
    foreach ($transactions as $tx) {
        $currencySymbol = $tx['currency_code'] == 1 ? '£' : ($tx['currency_code'] == 2 ? '€' : '₵');

     
        $debit = strtolower((string)($tx['type'] ?? '')) === 'debit'
            ? number_format($tx['amount'], 2) . " $currencySymbol"
            : '';

        // Payment method display
        $method = strtolower($tx['payment_method']);
        if (in_array($method, ['stripe', 'paystack'])) {
            $method = 'Card';
        } else {
            $method = ucfirst($method);
        }

        $output[] = [
            date('Y-m-d', strtotime($tx['created_at'])),
            htmlspecialchars($tx['account_name']),
            $debit,
            htmlspecialchars($tx['customer_name']),
            htmlspecialchars($method),
            '
<a href="/AIS/transaction-view?id=' . $tx['id'] . '" class="btn btn-success btn-xs"><i class="icon-file-text"></i> View</a>
<a href="/AIS/trans-generate?id=' . $tx['id'] . '" target="_blank" class="btn btn-info btn-xs"><span class="icon-print"></span></a>
<a href="#" data-object-id="' . $tx['id'] . '" class="btn btn-danger btn-xs delete-object"><i class="icon-trash-o"></i></a>'
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