<?php
use Http\Controllers\Configure\Employees;
use Http\Controllers\Configure\Company;
use Http\Controllers\Configure\Settings;
use Http\Controllers\Customers\Crm;
use Http\Controllers\account\Account;
use Http\Controllers\install\Installers;
use Http\Controllers\miscellaneous\DR;
use Http\Controllers\project\Project;
use Http\Controllers\Customers\Supplier;
use Http\Controllers\Customers\Group;
use Http\Controllers\Customers\Supports;
use Http\Controllers\Login;
use Http\Controllers\Sales\publicinvoice\InvoiceController;
use Http\Controllers\Sales\RecurSales\RecurringSales;
use Http\Controllers\Sales\Quote\QuoteController;
use Http\Controllers\Stock\item\Product;
use Http\Controllers\Stock\item\Category;
use Http\Controllers\Stock\item\warehouses;
use Http\Controllers\Stock\item\Stock;
use Http\Controllers\Stock\purchase\Purchase;
use Http\Controllers\Stock\return\Returnpurchase;
use Http\Controllers\install\Installer;

use Http\Controllers\install\InstallController;
// INSTALLER
// $router->get('/install', [Installers::class, 'welcome']);
// $router->get('/install/ping', [Installers::class, 'ping']); // quick sanity check
// $router->get('/install-requirements', [InstallController::class, 'requirements']);
// $router->get('/install-database', [InstallController::class, 'database']);
// $router->post('/install-save-database', [InstallController::class, 'saveDatabase']);


$router->get('/install',           [Installer::class, 'welcome']);
$router->post('/install-check',    [Installer::class, 'requirements']);
$router->get('/install-db',        [Installer::class, 'dbForm']);
$router->post('/install-db',       [Installer::class, 'saveDb']);
$router->post('/install-run',      [Installer::class, 'runMigrations']);
$router->post('/install-finish',   [Installer::class, 'finish']);

// INDEX
$router->get("/","index.php")->only("guest");
$router->get("/login","Sessions/store.php")->only("guest");






// DASHBOARD
$router->get("/dashboard","Dashboard.php")->only("Manager");

// PROFILE
$router->get("/Profile","profile/index.php")->only("auth");
$router->get("/profile-edit","profile/edit.php")->only("auth");
$router->get("/passwordUpdate","profile/edit_pass.php")->only("auth");
$router->post('/profile-edit', 'profile/update.php')->only('auth');


// SALES
$router->get("/create","Sales/new_invoice.php")->only("Salesperson");
$router->get("/manage","Sales/manage.php")->only("Salesperson");
$router->get("/invoice-view","Sales/publicinvoice/PublicInvoiceController@show")->only("Salesperson");
$router->post("/invoices-update","Sales/publicinvoice/InvoiceController@update")->only("Salesperson");
$router->post("/store-sales","Sales/store.php")->only("Salesperson")->only("Salesperson");
$router->post('/stripe-checkout', 'Sales/Payments/StripeCheckout.php')->only("Salesperson");
$router->get('/stripe-success', 'Sales/Payments/StripeSuccess.php')->only("Salesperson");
$router->post('/paystack-checkout', 'Sales/Payments/PaystackCheckout.php')->only("Salesperson");
$router->get('/paystack-success', 'Sales/Payments/PaystackSuccess.php')->only("Salesperson");
$router->get('/bank', 'Sales/BankCash.php')->only("Salesperson");
$router->get('/invoice-generate', 'Sales/publicinvoice/InvoiceController@generatePDF')->only("Salesperson");
$router->get('/invoice-viewer', 'Sales/publicinvoice/InvoiceController@showViewer')->only("Salesperson");
$router->get('/invoice-views', 'Sales/publicinvoice/invoice@show')->only("Salesperson");
$router->get('/invoice-download', [InvoiceController::class, 'downloadPDF'])->only("Salesperson");
$router->post('/invoices-update-status', 'Sales/publicinvoice/InvoiceController@update_status')->only("Salesperson");
$router->post('/invoices-status', 'Sales/publicinvoice/InvoiceController@status')->only("Salesperson");
$router->post('/invoices-cancel', 'Sales/publicinvoice/InvoiceController@cancel_status')->only("Salesperson");
$router->get("/ajaxlist",'Sales/publicinvoice/InvoiceController@ajaxList')->only("Salesperson");
$router->get('/invoices-delete', 'Sales/publicinvoice/InvoiceController@delete')->only('Salesperson');


// QUOTE
$router->get("/quote","Sales/Quote/QuoteController@create")->only("Salesperson");
$router->get("/quote-manage","Sales/Quote/QuoteController@manage")->only("Salesperson");
$router->get("/quote-ajaxlist",'Sales/Quote/QuoteController@ajaxList')->only("Salesperson");
$router->post('/quote-store', 'Sales/Quote/QuoteController@store')->only("Salesperson");
$router->get("/quote-view","Sales/Quote/QuoteController@view")->only("Salesperson");
$router->get("/quote-views","Sales/Quote/QuoteController@show")->only("Salesperson");
$router->post('/quote-update-status', 'Sales/Quote/QuoteController@update_status')->only("Salesperson");
$router->get('/quote-generate', 'Sales/Quote/QuoteController@generatePDF')->only("Salesperson");
$router->get('/quote-download', [QuoteController::class, 'downloadPDF'])->only("Salesperson");
$router->get('/quote-viewer', 'Sales/Quote/QuoteController@showViewer')->only("Salesperson");
$router->post("/quote-update","Sales/Quote/QuoteController@update")->only("Salesperson");
$router->get('/quotes-delete', 'Sales/Quote/QuoteController@delete')->only("Salesperson");


// RECURRING SALES
$router->get('/recur-dashboard', [RecurringSales::class, 'dashboard'])->only('Salesperson');
$router->get('/recur-create', [RecurringSales::class, 'create'])->only('Salesperson');
$router->post('/recur-store', [RecurringSales::class, 'store'])->only('Salesperson');
$router->post('/recur-update', [RecurringSales::class, 'update'])->only('Salesperson');
$router->get('/recurring-views', [RecurringSales::class, 'show'])->only('Salesperson');
$router->post('/recur-update-status', [RecurringSales::class, 'update_status'])->only('Salesperson');
$router->post('/RecurringUpdate-status', [RecurringSales::class, 'update_recur_status'])->only('Salesperson');
$router->get('/rec-manage', [RecurringSales::class, 'manage'])->only('Salesperson');
$router->get('/rec-ajaxlist', [RecurringSales::class, 'ajaxList'])->only('Salesperson');
$router->get('/rec-delete', [RecurringSales::class, 'delete'])->only('Salesperson');


// STOCK
$router->get("/stock",[Product::class, 'index'])->only("SalesManager");
$router->post("/stock",[Product::class, 'store'])->only("SalesManager");
// $router->post("/stock-update",[Product::class, 'update'])->only("auth");
// $router->get("/stock-manage",[Product::class, 'manage'])->only("auth");
// $router->get("/stock-ajaxlist",[Product::class, 'ajaxList'])->only("auth");
// $router->get("/stock-delete",[Product::class, 'delete'])->only("auth");

// CATEGORIES
// $router->get("/stock-category",[Category::class, 'index'])->only("auth");
// $router->get("/stock-category-list",[Category::class, 'ajaxList'])->only("auth");
$router->get("/category-add",[Category::class, 'add'])->only("SalesManager");
$router->post("/stock-category-store",[Category::class, 'store'])->only("auth");
// $router->post("/stock-category-update",[Category::class, 'update'])->only("auth");
// $router->get("/category-delete",[Category::class, 'delete'])->only("auth");
// $router->get("/category-product-list",[Category::class, 'productList'])->only("auth");
// $router->get("/product-list-ajax",[Category::class, 'productListAjax'])->only("auth");

// WAREHOUSES
// $router->get("/stock-warehouses",[warehouses::class, 'index'])->only("auth");
// $router->get("/stock-warehouses-ajaxlist",[warehouses::class, 'ajaxList'])->only("auth");
$router->get("/stock-warehouses-add",[warehouses::class, 'add'])->only("SalesManager");
$router->post("/stock-warehouses-store",[warehouses::class, 'store'])->only("auth");
// $router->post("/stock-warehouses-update",[warehouses::class, 'update'])->only("auth");
// $router->get("/stock-warehouses-delete",[warehouses::class, 'deleteWarehouse'])->only("auth");
// $router->get("/warehouse-product-list",[warehouses::class, 'productList'])->only("auth");
// $router->get("/warehouse-product-list-ajax",[warehouses::class, 'productListAjax'])->only("auth");
// $router->get("/stock-transfer",[Stock::class, 'index'])->only("auth");
// $router->get('/stock-transfer-products', [Stock::class, 'getProductsByWarehouse'])->only("auth");
// $router->post('/stock-transfer-store', [Stock::class, 'storeTransfer'])->only("auth");

// PURCHASE
$router->get("/purchase",[Purchase::class, 'index'])->only("SalesManager");
$router->post("/purchase",[Purchase::class, 'supplierStore'])->only("SalesManager");
$router->post("/purchase-store",[Purchase::class, 'purchaseStore'])->only("SalesManager");
$router->get("/purchase-manage",[Purchase::class, 'manage'])->only("SalesManager");
$router->get("/purchase-views",[Purchase::class, 'purchaseView'])->only("SalesManager");
$router->post("/purchase-update-status",[Purchase::class, 'updateStatus'])->only('SalesManager');
$router->get('/purchase-print', [Purchase::class, 'generatePDF'])->only('SalesManager');
$router->get('/purchase-viewer', [Purchase::class, 'showViewer'])->only('SalesManager');
$router->get('/purchase-download', [Purchase::class, 'downloadPDF'])->only('SalesManager');
$router->get('/public-purchase', [Purchase::class, 'public'])->only('SalesManager');
$router->post("/purchase-status",[Purchase::class, 'Status'])->only('SalesManager');
$router->post("/purchase-update",[Purchase::class, 'update'])->only("SalesManager");
$router->get("/purchase-ajaxlist",[Purchase::class, 'ajaxList'])->only("SalesManager");
$router->get("/purchase-delete",[Purchase::class, 'delete'])->only("SalesManager");



// RETURN
// $router->get("/return",[Returnpurchase::class, 'index'])->only("auth");
// $router->post("/return",[Returnpurchase::class, 'supplierStore'])->only("auth");
// $router->post("/return-store",[Returnpurchase::class, 'returnStore'])->only("auth");
// $router->get("/return-manage",[Returnpurchase::class, 'manage'])->only("auth");
// $router->get("/return-views",[Returnpurchase::class, 'returnView'])->only("auth");
// $router->post("/return-update-status",[Returnpurchase::class, 'updateStatus'])->only('auth');
// $router->get('/return-print', [Returnpurchase::class, 'generatePDF'])->only('auth');
// $router->get('/return-viewer', [Returnpurchase::class, 'showViewer'])->only('auth');
// $router->get('/return-download', [Returnpurchase::class, 'downloadPDF'])->only('auth');
// $router->get('/public-return', [Returnpurchase::class, 'public'])->only('auth');
// $router->post("/return-status",[Returnpurchase::class, 'Status'])->only('auth');
// $router->post("/return-update",[Returnpurchase::class, 'update'])->only("auth");
// $router->get("/return-ajaxlist",[Returnpurchase::class, 'ajaxList'])->only("auth");
// $router->get("/return-delete",[Returnpurchase::class, 'delete'])->only("auth");



// API
$router->get("/api/fetch_supplier_data","apis/fetch_supplier_data.php")->only("auth");
$router->get("/api/product_search","apis/fetch_product_details.php")->only("auth");
$router->get("/api/product_details","apis/product_details.php")->only("auth");

// CUSTOMERS
// $router->get("/create-customer",[Crm::class, 'index'])->only("auth");
// $router->get("/customer-edit",[Crm::class, 'index'])->only("auth");
$router->post("/customers-store","Customers/store.php")->only("auth");
// $router->post("/customer-store",[Crm::class, 'store'])->only("auth");
$router->get("/api/fetch_client_data","apis/fetch_client_data.php")->only("auth");
// $router->get("/manage-customers",[Crm::class, 'manage'])->only("auth");
// $router->get("/customer-ajaxList",[Crm::class, 'ajaxList'])->only("auth");
// $router->get("/view-customer",[Crm::class, 'view'])->only("auth");
// $router->post("/customer-update",[Crm::class, 'update'])->only("auth");
// $router->get("/customer-delete",[Crm::class, 'delete'])->only("auth");
// $router->get("/customer-invoice",[Crm::class, 'invoice'])->only("auth");
// $router->get("/customer-invoice-ajaxlist",[Crm::class, 'invoiceList'])->only("auth");


//GROUP
// $router->get("/group",[Group::class, 'index'])->only("auth");
// $router->post("/group-store",[Group::class, 'store'])->only("auth");
// $router->post("/group-update",[Group::class, 'update'])->only("auth");
// $router->get("/group-manage",[Group::class, 'manage'])->only("auth");
// $router->get("/client-manage",[Group::class, 'client'])->only("auth");
// $router->get("/group-ajaxList",[Group::class, 'ajaxList'])->only("auth");
// $router->get("/group-view",[Group::class, 'groupList'])->only("auth");
// $router->get("/group-delete",[Group::class, 'delete'])->only("auth");

//SUPPORT
// $router->get("/Unsolved",[Supports::class, 'index'])->only("auth");
// $router->get("/tickets",[Supports::class, 'manage'])->only("auth");
// $router->get("/support-ajaxList",[Supports::class, 'ajaxList'])->only("auth");
// $router->get("/support-manage-ajaxList",[Supports::class, 'mangeAjaxList'])->only("auth");
// $router->post("/store-support",[Supports::class, 'store'])->only("auth");
// $router->get("/ticket-thread",[Supports::class, 'thread'])->only("auth");
// $router->get("/delete-ticket",[Supports::class, 'delete'])->only("auth");
// $router->post("/ticket-update",[Supports::class, 'updateStatus'])->only("auth");
// $router->post("/ticket-store",[Supports::class, 'storeReply'])->only("auth");
//SUPPORT FORM
// $router->get("/Support",[Login::class, 'index'])->only("auth");


//SUPPLIER
$router->get("/supplier",[Supplier::class, 'index'])->only("Manager");
$router->post("/supplier-store",[Supplier::class, 'Store'])->only("Manager");
// $router->post("/supplier-update",[Supplier::class, 'update'])->only("auth");
// $router->get("/supplier-manage",[Supplier::class, 'manage'])->only("auth");
// $router->get("/supplier-ajaxList",[Supplier::class, 'ajaxList'])->only("auth");
// $router->get("/supplier-delete",[Supplier::class, 'delete'])->only("auth");


//PROJECT
// $router->get("/addproject",[Project::class, 'index'])->only("auth");
// $router->post("/project-store",[Project::class, 'store'])->only("auth");
// $router->post("/project-update",[Project::class, 'update'])->only("auth");
// $router->get("/project-manage",[Project::class, 'manage'])->only("auth");
// $router->get("/project-ajaxList",[Project::class, 'ajaxList'])->only("auth");
// $router->get("/todo",[Project::class, 'todo'])->only("auth");


//ACCOUNT
$router->get("/account",[Account::class, 'create'])->only("accountant");
$router->get("/account-edit",[Account::class, 'create'])->only("accountant");
$router->post("/account-store",[Account::class, 'store'])->only("accountant");
$router->post("/account-update",[Account::class, 'update'])->only("accountant");
$router->get("/account-manage",[Account::class, 'manage'])->only("accountant");
$router->get("/account-manage-ajaxList",[Account::class, 'ajaxList'])->only("accountant");
$router->get("/account-manage-delete",[Account::class, 'delete'])->only("accountant");
$router->get("/account-manage-view",[Account::class, 'view'])->only("accountant");
$router->get("/BalanceSheet",[Account::class, 'balancesheet'])->only("accountant");
$router->get("/statement",[Account::class, 'statement'])->only("accountant");
$router->post("/viewstatement", [Account::class, 'statementData'])->only("accountant");
$router->get("/statementAjax",[Account::class, 'AjaxStatementTransactions'])->only("accountant");
$router->get("/transactionAjax",[Account::class, 'AjaxAllTransactions'])->only("accountant");
$router->get("/transactions",[Account::class, 'transaction'])->only("accountant");
$router->get("/transaction-view",[Account::class, 'transactionView'])->only("accountant");
$router->get("/trans-viewer",[Account::class, 'showViewer'])->only("accountant");
$router->get('/trans-generate', [Account::class, 'printTransactionPDF']);
$router->get("/transaction-delete",[Account::class, 'deleteTransaction'])->only("accountant");
$router->get('/addTrans', [Account::class, 'add'])->only("accountant");
$router->post('/addTrans', [Account::class, 'storeTransaction'])->only("accountant");
$router->get('/Transfer', [Account::class, 'transfer'])->only("accountant");
$router->post('/Transfer', [Account::class, 'saveTransfer'])->only("accountant");
//INCOME
$router->get("/income",[Account::class, 'income'])->only("accountant");
$router->get("/incomeAjax",[Account::class, 'IncomeAjaxAllTransactions'])->only("accountant");
//EXPENSE
$router->get("/expense",[Account::class, 'expense'])->only("accountant");
$router->get("/expenseAjax",[Account::class, 'ExpenseAjaxAllTransactions'])->only("accountant");


//DATA & REPORTS
$router->get("/statistics",[DR::class, 'statistics'])->only("accountant");
$router->get("/customerstatement",[DR::class, 'customerstatement'])->only("accountant");
$router->post("/customerstatement", [DR::class, 'statementData'])->only("accountant");
$router->get("/cusstatementAjax",[DR::class, 'AjaxStatementTransactions'])->only("accountant");
$router->get("/SupplierStatement",[DR::class, 'SupplierStatement'])->only("accountant");
$router->post("/SupplierStatement", [DR::class, 'statementSupplier'])->only("accountant");
$router->get("/supstatementAjax",[DR::class, 'SupAjaxStatementTransactions'])->only("accountant");
$router->post("/income",[DR::class, 'IncomeCalculate'])->only("accountant");
$router->get("/incomeStatement",[DR::class, 'income'])->only("accountant");
$router->post("/expense",[DR::class, 'expenseCalculate'])->only("accountant");
$router->get("/expenseStatement",[DR::class, 'expense'])->only("accountant");
$router->get("/tax",[DR::class, 'tax'])->only("accountant");
$router->post("/viewTax", [DR::class, 'TaxData'])->only("accountant");
$router->get("/taxAjax",[DR::class, 'ajaxList'])->only("accountant");


// CONFIGURE
$router->get("/employees",[Employees::class, 'index'])->only("superAdmin");
$router->get("/AddEmployee",[Employees::class, 'add'])->only("superAdmin");
$router->get("/EmployeesAjaxList",[Employees::class, 'ajaxList'])->only("superAdmin");
$router->get("/company",[Settings::class, 'index'])->only("superAdmin");
$router->get("/dtformat",[Settings::class, 'DateTime'])->only("superAdmin");
$router->get("/setgoals",[Settings::class, 'goal'])->only("superAdmin");
$router->get("/email",[Settings::class, 'email'])->only("superAdmin");
$router->get("/recaptcha",[Settings::class, 'security'])->only("superAdmin");
// LOGOUT
$router->delete("/logout","Sessions/destroy.php")->only("auth");
$router->get("/logout","Sessions/destroy.php")->only("auth");