<?php 
use Core\App;
use Core\Database;

$db = App::resolve(Database::class);
$query = " SELECT i.id, c.name AS customer_name, i.status, i.due_date, i.grand_total
    FROM invoices i
    LEFT JOIN customers c ON i.customer_id = c.id
    ORDER BY i.id DESC
    LIMIT 20
";
$invoices = $db->query($query)->get();


 $whereSQL = "WHERE 1=1"; // Modify if you need specific conditions
$query = "
    SELECT 
        t.id,
        t.created_at,
        a.name AS account_name,
        t.type,
        t.amount,
        COALESCE(NULLIF(p.payment_method, ''), NULLIF(t.payment_method, ''), 'Cash') AS payment_method
    FROM transactions t
    INNER JOIN accounts a ON a.id = t.account_id
    LEFT JOIN payments p ON p.id = t.payment_id
    $whereSQL
    ORDER BY t.created_at DESC
    LIMIT 10
";
$transactions = $db->query($query)->get();



// Current date and month
$today = date('Y-m-d');
$currentYear = date('Y');
$currentMonth = date('m');

// Query for Today Invoices (count of invoices created today)
$todayInvoicesQuery = "
    SELECT COUNT(*) as count
    FROM invoices
    WHERE DATE(invoice_date) = :today
";
$todayInvoices = $db->query($todayInvoicesQuery, ['today' => $today])->find()['count'];

// Query for This Month Invoices (count of invoices in current month)
$monthInvoicesQuery = "
    SELECT COUNT(*) as count
    FROM invoices
    WHERE YEAR(invoice_date) = :year AND MONTH(invoice_date) = :month
";
$monthInvoices = $db->query($monthInvoicesQuery, ['year' => $currentYear, 'month' => $currentMonth])->find()['count'];

// Query for Today Sales (sum of grand_total and currency for invoices created today)
$todaySalesQuery = "
    SELECT COALESCE(SUM(grand_total), 0) as total, MAX(currency) as currency
    FROM invoices
    WHERE DATE(invoice_date) = :today
";
$todaySalesData = $db->query($todaySalesQuery, ['today' => $today])->find();
$todaySales = $todaySalesData['total'];
$todayCurrency = $todaySalesData['currency'] ?? null;
$todayCurrencySymbol = ($todayCurrency == 1) ? '£' : ($todayCurrency == 2 ? '€' : '₵');

// Query for This Month Sales (sum of grand_total and currency for invoices in current month)
$monthSalesQuery = "
    SELECT COALESCE(SUM(grand_total), 0) as total, MAX(currency) as currency
    FROM invoices
    WHERE YEAR(invoice_date) = :year AND MONTH(invoice_date) = :month
";
$monthSalesData = $db->query($monthSalesQuery, ['year' => $currentYear, 'month' => $currentMonth])->find();
$monthSales = $monthSalesData['total'];
$monthCurrency = $monthSalesData['currency'] ?? null;
$monthCurrencySymbol = ($monthCurrency == 1) ? '£' : ($monthCurrency == 2 ? '€' : '₵');


?>
