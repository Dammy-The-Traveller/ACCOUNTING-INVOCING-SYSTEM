# AIS - Complete Documentation

**Version**: 1.0.0  
**Last Updated**: February 2026  
**Author**: Dammy The Traveller

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Core Framework](#core-framework)
4. [Project Structure](#project-structure)
5. [Database Schema](#database-schema)
6. [Module Documentation](#module-documentation)
7. [API Reference](#api-reference)
8. [Middleware & Authorization](#middleware--authorization)
9. [Forms & Validation](#forms--validation)
10. [Working with Views](#working-with-views)
11. [Development Guide](#development-guide)
12. [Troubleshooting](#troubleshooting)

---

## System Overview

### What is AIS?
 AIS is a comprehensive **Accounting and Inventory System** designed for small to medium-sized businesses. It provides:

- **Complete financial management** with chart of accounts and balance tracking
- **Inventory control** with real-time stock management across multiple warehouses
- **Sales operations** including invoicing, recurring sales, and quote generation
- **Purchase management** with supplier relationships and return processing
- **Customer relationship management** (CRM) for tracking clients and interactions
- **Multi-user support** with role-based access control

### Key Benefits

- Single unified platform for accounting, inventory, and sales
- Real-time financial visibility and reporting
- Integration with payment gateways (Stripe, Paystack)
- PDF document generation for invoices and reports
- Role-based security for different user types

---

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Client Browser                        │
└──────────────────────┬──────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────┐
│                   Web Server Layer                       │
│              (Apache/Nginx with PHP-FPM)                 │
└──────────────────────┬──────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────┐
│                 Application Layer                        │
│  ┌──────────────────────────────────────────────────┐   │
│  │  Router  │ Middleware │ Controllers │ Services   │   │
│  └──────────────────────────────────────────────────┘   │
│                       ▲                                  │
│                       │                                  │
│  ┌──────────────────────────────────────────────────┐   │
│  │  Views & Forms │ Validation │ Authentication     │   │
│  └──────────────────────────────────────────────────┘   │
└──────────────────────┬──────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────┐
│              Data Access Layer                          │
│  ┌──────────────────────────────────────────────────┐   │
│  │  Database Abstraction │ Query Builder │ Models   │   │
│  └──────────────────────────────────────────────────┘   │
└──────────────────────┬──────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────┐
│                   MySQL Database                        │
│         (Tables: Users, Accounts, Products, etc)        │
└─────────────────────────────────────────────────────────┘
```

### Design Patterns Used

1. **MVC (Model-View-Controller)**: Separation of concerns
2. **Service Container/Dependency Injection**: Loose coupling
3. **Middleware Pipeline**: Request/response processing
4. **Repository Pattern**: Data access abstraction
5. **Factory Pattern**: Object creation

---

## Core Framework

### 1. Application Bootstrap (`Core/App.php`)

The `App` class acts as a **service locator and dependency injection facade**.

**Purpose**: Provides static access to the service container for binding and resolving dependencies.

**Key Methods**:

```php
// Set the container instance
App::setContainer($container);

// Get the container
$container = App::Container();

// Bind a service to the container
App::bind('Core\Database', function() {
    return new Database($config);
});

// Resolve a service from the container
$db = App::resolve('Core\Database');
```

**Usage Example**:

```php
// In bootstrap.php
$container = new Container();
$container->bind('Core\Database', function(){
    $config = require base_path('config.php');
    return new Database($config['database']);
});
App::setContainer($container);
```

### 2. Routing System (`Core/Router.php`)

The `Router` class handles **URL routing** and **request dispatching**.

**Key Methods**:

```php
// Register routes
$router->get('/path', $controller);
$router->post('/path', $controller);
$router->put('/path', $controller);
$router->delete('/path', $controller);
$router->patch('/path', $controller);

// Assign middleware to a route
$router->get('/dashboard', 'Dashboard.php')->only('auth');

// Resolve and execute a route
$router->route($uri, $method);
```

**Route Definition Formats**:

```php
// Format 1: String with @ separator (Class-based)
$router->get('/users', 'Users/index@list');

// Format 2: Array notation (Class-based)
$router->post('/users', [UsersController::class, 'store']);

// Format 3: PHP file path (Flat files)
$router->get('/dashboard', 'Dashboard.php');
```

**Middleware Assignment**:

```php
// Apply middleware to restrict access
$router->get('/admin', 'Admin/index.php')->only('Admin');
$router->post('/profile', 'profile/update.php')->only('auth');
```

### 3. Database Layer (`Core/Database.php`)

The `Database` class provides **PDO-based database abstraction**.

**Key Methods**:

```php
// Execute a query with parameters
$db->query('SELECT * FROM users WHERE email = :email', ['email' => $email]);

// Fetch all results
$results = $db->get();  // Returns array

// Fetch single result
$result = $db->find();  // Returns array or false

// Fetch or abort if not found
$result = $db->findOrFail();  // Aborts with 404 if not found

// Get count of affected rows
$count = $db->rowCount();

// Get last inserted ID
$id = $db->lastInsertId();

// Transaction support
$db->beginTransaction();
$db->query('UPDATE accounts SET balance = balance - :amount', ['amount' => 100]);
$db->commit();
```

**Configuration** (`config.php`):

```php
return [
    'database' => [
        'driver'   => $_ENV['Driver'],     // 'mysql'
        'host'     => $_ENV['DB_HOST'],
        'port'     => $_ENV['DB_PORT'],
        'dbname'   => $_ENV['DB_DATABASE'],
        'charset'  => 'utf8mb4',
        'username' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD'],
    ]
];
```

### 4. Authentication (`Core/Authenticator.php`)

Handles **user login and session management**.

**Key Methods**:

```php
// Attempt to authenticate user
$result = $authenticator->attempt($email, $password);
// Returns: true (success), 'blocked' (user blocked), false (failure)

// Login user (creates session)
$authenticator->login($userData);

// Logout user (destroys session)
$authenticator->logout();
```

**Session Structure** (after login):

```php
$_SESSION = [
    'user' => [
        'ID' => 1,
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'john@example.com',
        'UserType' => 1  // 1=Admin, 2=Manager, 3=Accountant, etc.
    ]
];
```

### 5. Validation System (`Core/Validator.php`)

Provides **input validation methods**.

**Available Validators**:

```php
// String validation
Validator::string($value, $min = 1, $max = INF);

// Email validation
Validator::email($value);

// Number validation
Validator::number($value, $min = PHP_INT_MIN, $max = null);

// Date validation
Validator::date($value, $format = 'Y-m-d');

// Greater than validation
Validator::greaterThan($value, $greaterThan);
```

**Usage Example**:

```php
// Validate form input
$email = $_POST['email'];
$password = $_POST['password'];

if (!Validator::email($email)) {
    throw new ValidationException('Invalid email format');
}

if (!Validator::string($password, 8, 255)) {
    throw new ValidationException('Password must be 8-255 characters');
}
```

### 6. Session Management (`Core/Session.php`)

Manages **session lifecycle and user state**.

**Methods**:

```php
// Check if user is authenticated
Session::isAuthenticated();

// Get session value
$value = Session::get('key');

// Set session value
Session::set('key', $value);

// Flash data (available for next request only)
Session::flash('message', 'Success!');

// Destroy session
Session::destroy();
```

### 7. Error Handling (`Core/ValidationException.php`)

Custom exception for **validation errors**.

```php
throw new ValidationException('Field is required');
```

---

## Project Structure

### Directory Organization

```
AIS/
├── Core/
│   ├── App.php                 # Service locator facade
│   ├── Authenticator.php       # User authentication
│   ├── Container.php           # Dependency injection container
│   ├── Database.php            # Database abstraction
│   ├── functions.php           # Global helper functions
│   ├── Migrator.php            # Database migration runner
│   ├── Response.php            # Response handling
│   ├── Router.php              # URL routing
│   ├── SeederRunner.php        # Database seeding
│   ├── Session.php             # Session management
│   ├── ValidationException.php # Custom exception
│   ├── Validator.php           # Input validation
│   └── middleware/             # Route middleware classes
│       ├── Middleware.php      # Base middleware class
│       ├── Auth.php            # Authentication check
│       ├── Admin.php           # Admin access only
│       ├── Accountant.php      # Accountant role
│       ├── Manager.php         # Manager role
│       ├── Salesperson.php     # Salesperson role
│       ├── Salesmanager.php    # Sales manager role
│       ├── Superadmin.php      # Super admin role
│       └── Guest.php           # Guest access only
│
├── Http/
│   ├── Controllers/            # Application controllers
│   │   ├── Dashboard.php       # Dashboard controller
│   │   ├── Login.php           # Login controller
│   │   ├── account/            # Account management
│   │   ├── apis/               # API endpoints
│   │   ├── Configure/          # System configuration
│   │   │   ├── Company.php
│   │   │   ├── Employees.php
│   │   │   └── Settings.php
│   │   ├── Customers/          # CRM and customers
│   │   │   ├── Crm.php
│   │   │   ├── Supplier.php
│   │   │   ├── Group.php
│   │   │   └── Supports.php
│   │   ├── Sales/              # Sales management
│   │   │   ├── publicinvoice/  # Invoice management
│   │   │   ├── Quote/          # Quotations
│   │   │   └── RecurSales/     # Recurring sales
│   │   ├── Stock/              # Inventory management
│   │   │   ├── item/           # Products and categories
│   │   │   ├── purchase/       # Purchase orders
│   │   │   ├── return/         # Returns
│   │   │   └── warehouses.php  # Warehouse management
│   │   ├── profile/            # User profile
│   │   ├── registration/       # User registration
│   │   ├── Sessions/           # Session handling
│   │   └── miscellaneous/      # Other operations
│   └── Forms/
│       └── LoginForm.php       # Login form validation
│
├── database/
│   ├── migrations/             # Database migration files
│   │   ├── 20250819_000001_create_users_table.php
│   │   └── 20250819_000002_import_full_dump.php
│   ├── seeders/                # Database seeders
│   └── dumps/
│       └── schema.sql          # Full database schema
│
├── views/                      # Blade/PHP view templates
│   ├── login.view.php
│   ├── Dashboard.view.php
│   ├── 403.php                 # Forbidden page
│   ├── 404.php                 # Not found page
│   ├── partials/               # Reusable components
│   │   ├── head.php
│   │   ├── navbar.php
│   │   ├── Sidenav.php
│   │   └── ...
│   ├── Sales/
│   ├── Stock/
│   ├── configure/
│   ├── crm/
│   ├── profile/
│   └── account/
│
├── Public/                     # Public assets
│   ├── assets/
│   │   ├── css/                # Stylesheets
│   │   ├── js/                 # JavaScript files
│   │   ├── img/                # Images
│   │   ├── plugins/            # Third-party plugins
│   │   ├── vendor/             # Vendor CSS/JS
│   │   └── myjs/               # Custom scripts
│   ├── uploads/                # User uploads
│   ├── pdf/                    # PDF viewer library
│   ├── invoices/               # Generated invoices
│   ├── purchases/              # Purchase documents
│   ├── Quote/                  # Quote documents
│   ├── returns/                # Return documents
│   └── Trans/                  # Transaction documents
│
├── storage/
│   ├── logs/                   # Application logs
│   └── install.lock            # Installation marker
│
├── vendor/                     # Composer dependencies
├── index.php                   # Application entry point
├── bootstrap.php               # Application bootstrap
├── route.php                   # Route definitions
├── config.php                  # Configuration
├── migrate.php                 # Migration runner
├── seed.php                    # Seeder runner
├── composer.json               # Project manifest
├── phpunit.xml                 # Test configuration
└── .env                        # Environment variables
```

---

## Database Schema

### Core Tables

#### `users` Table
Stores user accounts and authentication data.

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(100),
    lastname VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    user_type INT,          -- 1=Admin, 2=Manager, 3=Accountant, etc.
    block CHAR(1),          -- Y/N: Block user from login
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### `accounts` Table
Chart of accounts for accounting system.

```sql
CREATE TABLE accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE,
    name VARCHAR(100),
    type ENUM('asset', 'liability', 'equity', 'income', 'expense'),
    account_number VARCHAR(50),
    initial_balance DECIMAL(15, 2),
    current_balance DECIMAL(15, 2),
    status ENUM('active', 'inactive'),
    parent_id INT,          -- For hierarchical accounts
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### `categories` Table
Product categories for inventory.

```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    description TEXT,
    status ENUM('active', 'inactive'),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP,
    updated_by INT
);
```

#### `products` Table (Stock/Items)
Product inventory items.

```sql
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE,
    name VARCHAR(150),
    description TEXT,
    category_id INT,
    unit_price DECIMAL(15, 2),
    quantity_in_stock INT,
    reorder_level INT,
    status ENUM('active', 'inactive'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

#### `invoices` Table
Sales invoices and transactions.

```sql
CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) UNIQUE,
    customer_id INT,
    invoice_date DATE,
    due_date DATE,
    subtotal DECIMAL(15, 2),
    tax DECIMAL(15, 2),
    total DECIMAL(15, 2),
    status ENUM('draft', 'sent', 'paid', 'overdue'),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### `clients` Table
Customer records (CRM).

```sql
CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100),
    status ENUM('active', 'inactive'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### `purchases` Table
Purchase orders and vendor transactions.

```sql
CREATE TABLE purchases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    purchase_order_number VARCHAR(50) UNIQUE,
    supplier_id INT,
    purchase_date DATE,
    total DECIMAL(15, 2),
    status ENUM('pending', 'received', 'cancelled'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### `warehouses` Table
Storage location tracking.

```sql
CREATE TABLE warehouses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    location VARCHAR(200),
    capacity INT,
    status ENUM('active', 'inactive'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Module Documentation

### 1. Accounting Module

**Purpose**: Manage company finances, chart of accounts, and financial transactions.

**Key Features**:
- Account hierarchy management
- Balance tracking (debit/credit)
- Multi-currency support
- Transaction recording

**Controllers**:
- `Http/Controllers/Configure/Company.php` - Company settings
- API endpoints in `Http/Controllers/apis/`

**Database Tables**:
- `accounts` - Chart of accounts
- `transactions` - Individual transactions (if exists)
- `account_balances` - Running account balances

**Usage Example**:

```php
// Retrieve account by code
$db->query('SELECT * FROM accounts WHERE code = :code', ['code' => '1000'])
   ->find();

// Update account balance
$db->query(
    'UPDATE accounts SET current_balance = :balance WHERE id = :id',
    ['balance' => $newBalance, 'id' => $accountId]
);
```

### 2. Inventory Management Module

**Purpose**: Track products, stock levels, and warehouse operations.

**Key Features**:
- Multi-warehouse support
- Real-time stock tracking
- Product categorization
- Reorder level alerts
- Stock adjustments

**Controllers**:
- `Http/Controllers/Stock/item/Product.php` - Product management
- `Http/Controllers/Stock/item/Category.php` - Categories
- `Http/Controllers/Stock/item/Stock.php` - Stock levels
- `Http/Controllers/Stock/item/warehouses.php` - Warehouse management
- `Http/Controllers/Stock/purchase/Purchase.php` - Purchase orders
- `Http/Controllers/Stock/return/Returnpurchase.php` - Return processing

**Database Tables**:
- `products` - Product master
- `categories` - Product categories
- `warehouses` - Warehouse locations
- `stock_movements` - Stock transaction history
- `purchases` - Purchase orders
- `purchase_items` - Items in purchase orders

**Key Functions**:

```php
// Get product stock by warehouse
$db->query('SELECT * FROM products WHERE category_id = :cat_id', 
    ['cat_id' => $categoryId])->get();

// Update stock level
$db->query('UPDATE products SET quantity_in_stock = :qty WHERE id = :id',
    ['qty' => $newQty, 'id' => $productId]);

// Record purchase
$db->query('INSERT INTO purchases (purchase_order_number, supplier_id, total) 
          VALUES (:po, :sup, :tot)',
    ['po' => $poNumber, 'sup' => $supplierId, 'tot' => $total]);
```

### 3. Sales Module

**Purpose**: Manage customer invoices, quotes, and recurring sales.

**Key Features**:
- Invoice generation with PDF export
- Recurring sales automation
- Quote management
- Payment integration (Stripe, Paystack)
- Invoice status tracking

**Controllers**:
- `Http/Controllers/Sales/publicinvoice/InvoiceController.php` - Invoice CRUD
- `Http/Controllers/Sales/Quote/QuoteController.php` - Quotations
- `Http/Controllers/Sales/RecurSales/RecurringSales.php` - Recurring sales

**Database Tables**:
- `invoices` - Invoice headers
- `invoice_items` - Individual line items
- `quotes` - Quote records
- `recurring_sales` - Recurring patterns

**Key Routes**:

```php
$router->get("/create", "Sales/new_invoice.php")->only("Salesperson");
$router->get("/manage", "Sales/manage.php")->only("Salesperson");
$router->get("/invoice-view", "Sales/publicinvoice/PublicInvoiceController@show");
```

**PDF Generation**:

```php
// Using DOMPDF library
$html = $this->renderInvoice($invoiceData);
$pdf = new Dompdf();
$pdf->loadHtml($html);
$pdf->render();
$pdf->stream('invoice-' . $invoiceNumber . '.pdf');
```

### 4. Purchase Management Module

**Purpose**: Handle vendor management and purchase operations.

**Key Features**:
- Purchase order creation
- Supplier management
- Receipt and return tracking
- Integration with inventory

**Controllers**:
- `Http/Controllers/Stock/purchase/Purchase.php`
- `Http/Controllers/Stock/return/Returnpurchase.php`
- `Http/Controllers/Customers/Supplier.php`

**Database Tables**:
- `purchases` - Purchase orders
- `purchase_items` - Line items
- `suppliers` - Vendor records
- `returns` - Return transactions

### 5. Customer Relationship Management (CRM)

**Purpose**: Manage customer relationships, support tickets, and interactions.

**Key Features**:
- Customer profiles
- Contact history
- Support tickets
- Customer segmentation (groups)
- Interaction tracking

**Controllers**:
- `Http/Controllers/Customers/Crm.php` - Main CRM
- `Http/Controllers/Customers/Supports.php` - Support tickets
- `Http/Controllers/Customers/Group.php` - Customer groups

**Database Tables**:
- `clients` - Customer records
- `customer_groups` - Segmentation
- `support_tickets` - Tickets
- `interactions` - Contact history

### 6. User Management & Authentication

**Purpose**: Manage users, roles, and permissions.

**Key Features**:
- User registration and authentication
- Role-based access control (RBAC)
- User blocking/suspension
- Session management

**Controllers**:
- `Http/Controllers/Login.php` - Authentication
- `Http/Controllers/profile/` - User profile
- `Http/Controllers/registration/` - Registration

**User Types/Roles**:
- 1: Superadmin - Full system access
- 2: Admin - Administrative functions
- 3: Manager - Management functions
- 4: Accountant - Accounting operations
- 5: Salesperson - Sales operations
- 6: Salesmanager - Sales management
- 7: Guest - Limited/public access

**Database Table**:
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(100),
    lastname VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    user_type INT,  -- 1=Superadmin, 2=Admin, 3=Manager, etc.
    block CHAR(1),  -- Y/N to block user
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## API Reference

### API Endpoints Overview

The application includes several API endpoints for programmatic access:

**Location**: `Http/Controllers/apis/`

**Base URL**: `http://localhost/AIS/api/`

#### Available APIs

| Endpoint | Method | Purpose | Auth |
|----------|--------|---------|------|
| `/api/invoices` | GET | List invoices | Required |
| `/api/invoices/{id}` | GET | Get invoice details | Required |
| `/api/invoices` | POST | Create invoice | Required |
| `/api/products` | GET | List products | Required |
| `/api/customers` | GET | List customers | Required |
| `/api/accounts` | GET | List accounts | Required |

**Example API Usage**:

```php
// GET request to fetch invoices
GET /api/invoices?page=1&limit=20

// POST request to create invoice
POST /api/invoices
Content-Type: application/json

{
    "customer_id": 1,
    "invoice_date": "2025-02-16",
    "items": [
        {
            "product_id": 1,
            "quantity": 5,
            "unit_price": 100
        }
    ]
}
```

---

## Middleware & Authorization

### Middleware System

**Location**: `Core/middleware/`

Middleware processes requests before they reach controllers.

**Base Middleware Class** (`Middleware.php`):

```php
namespace Core\middleware;

abstract class Middleware {
    abstract public function handle();
    
    public function before() {}
    public function after() {}
}
```

### Available Middleware

#### 1. Authentication Middleware (`Auth.php`)
Requires user to be logged in.

```php
// In route definition
$router->get('/dashboard', 'Dashboard.php')->only('auth');
```

#### 2. Role-Based Middleware

Each role has its own middleware:

- **Superadmin** (`Superadmin.php`) - Super administrator access
- **Admin** (`Admin.php`) - Administrative access
- **Manager** (`Manager.php`) - Management access
- **Accountant** (`Accountant.php`) - Accounting operations
- **Salesperson** (`Salesperson.php`) - Sales operations
- **Salesmanager** (`Salesmanager.php`) - Sales management
- **Guest** (`Guest.php`) - Public/unauthenticated access

#### 3. Guest Middleware (`Guest.php`)
Allows only unauthenticated users (e.g., login page).

```php
$router->get('/login', 'Sessions/store.php')->only('guest');
```

### Route Protection Examples

```php
// Only authenticated users
$router->get('/profile', 'profile/index.php')->only('auth');

// Only administrators
$router->get('/admin', 'Admin/index.php')->only('Admin');

// Only salespeople
$router->get('/create-invoice', 'Sales/new_invoice.php')->only('Salesperson');

// Only guests (not logged in)
$router->get('/login', 'Sessions/store.php')->only('guest');

// Only managers
$router->get('/reports', 'Reports/index.php')->only('Manager');
```

### Checking User Role in Controllers

```php
// Get current user
$user = $_SESSION['user'];

// Check user ID
$userId = $user['ID'];

// Check user type
$userType = $user['UserType'];  // 1=Superadmin, 2=Admin, etc.

// Check if user is specific role
if ($userType == 1) {
    // Superadmin operations
}
```

---

## Forms & Validation

### Form Handling

Forms are typically processed in separate PHP files:

**Example Form Processing** (`Http/Controllers/profile/update.php`):

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    
    // Validate input
    if (!Validator::email($email)) {
        $_SESSION['error'] = 'Invalid email format';
        redirect_back();
    }
    
    if (!Validator::string($firstname, 1, 100)) {
        $_SESSION['error'] = 'First name is required';
        redirect_back();
    }
    
    // Update database
    $db->query(
        'UPDATE users SET email = :email, firstname = :firstname WHERE id = :id',
        ['email' => $email, 'firstname' => $firstname, 'id' => $_SESSION['user']['ID']]
    );
    
    $_SESSION['success'] = 'Profile updated successfully';
    redirect('/Profile');
}
```

### Available Validators

```php
// String validation
if (!Validator::string($_POST['name'], 1, 100)) {
    // Field is required and max 100 chars
}

// Email validation
if (!Validator::email($_POST['email'])) {
    // Invalid email
}

// Number validation
if (!Validator::number($_POST['quantity'], 1, 1000)) {
    // Must be number between 1-1000
}

// Date validation
if (!Validator::date($_POST['date'], 'Y-m-d')) {
    // Invalid date format
}

// Greater than validation
if (!Validator::greaterThan($_POST['amount'], 0)) {
    // Amount must be greater than 0
}
```

### Form Class Example (`Http/Forms/LoginForm.php`)

```php
namespace Http\Forms;

use Core\Validator;

class LoginForm {
    public function validate($data) {
        if (!Validator::email($data['email'])) {
            throw new ValidationException('Invalid email');
        }
        
        if (!Validator::string($data['password'], 6)) {
            throw new ValidationException('Password too short');
        }
        
        return true;
    }
}
```

---

## Working with Views

### View Structure

Views are PHP templates located in the `views/` directory.

**View Hierarchy**:
```
views/
├── partials/
│   ├── head.php          # HTML head section
│   ├── navbar.php        # Navigation bar
│   ├── Sidenav.php       # Side navigation
│   └── footer.php        # Footer (optional)
├── login.view.php
├── Dashboard.view.php
├── Sales/
├── Stock/
└── ...
```

### Rendering Views

**In Controllers**:

```php
<?php
// In Http/Controllers/Dashboard.php
$data = [
    'invoices' => $invoicesList,
    'balance' => $totalBalance
];

// Views are typically included directly
include view_path('Dashboard.view.php');
// or
include __DIR__ . '/../../views/Dashboard.view.php';
```

### Passing Data to Views

```php
<?php
// Controller
$invoices = $db->query('SELECT * FROM invoices')->get();
$totalBalance = 5000;

// Then include view and use $invoices and $totalBalance
include view_path('Sales/invoices.view.php');
```

### Using Partials

```php
<?php
// In any view file

// Include header
<?php include __DIR__ . '/../partials/head.php'; ?>

// Include navigation
<?php include __DIR__ . '/../partials/navbar.php'; ?>

// Include sidebar
<?php include __DIR__ . '/../partials/Sidenav.php'; ?>

// Your content here

// Include footer if needed
```

### Session Messages in Views

```php
<!-- Display success message -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success']); ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<!-- Display error message -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error']); ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
```

### Example View Template

```php
<?php include __DIR__ . '/../partials/head.php'; ?>

<div class="container">
    <h1>Invoices</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <table class="table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoices as $invoice): ?>
                <tr>
                    <td><?= htmlspecialchars($invoice['invoice_number']); ?></td>
                    <td><?= htmlspecialchars($invoice['customer_name']); ?></td>
                    <td>$<?= number_format($invoice['total'], 2); ?></td>
                    <td><?= htmlspecialchars($invoice['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
```

---

## Development Guide

### Adding a New Feature

#### Step 1: Create Migration

Create a new migration file in `database/migrations/`:

```php
<?php
// database/migrations/20250216_000003_create_expenses_table.php

use Core\Migrator;

class CreateExpensesTable {
    public function up() {
        // Create table
    }
    
    public function down() {
        // Drop table or rollback
    }
}
```

#### Step 2: Create Controller

Create controller in `Http/Controllers/`:

```php
<?php
namespace Http\Controllers;

use Core\App;
use Core\Database;

class ExpenseController {
    public function index() {
        $db = App::resolve(Database::class);
        $expenses = $db->query('SELECT * FROM expenses')->get();
        
        include view_path('Expenses/index.view.php');
    }
    
    public function store() {
        $db = App::resolve(Database::class);
        
        // Validate and insert
        $amount = $_POST['amount'];
        $description = $_POST['description'];
        
        $db->query(
            'INSERT INTO expenses (amount, description) VALUES (:amount, :description)',
            ['amount' => $amount, 'description' => $description]
        );
        
        $_SESSION['success'] = 'Expense recorded';
        redirect('/expenses');
    }
}
```

#### Step 3: Add Routes

Add routes in `route.php`:

```php
$router->get('/expenses', 'Expenses/index.php')->only('Accountant');
$router->post('/expenses', 'Expenses/store.php')->only('Accountant');
$router->delete('/expenses/:id', [ExpenseController::class, 'delete'])->only('Accountant');
```

#### Step 4: Create Views

Create view templates in `views/Expenses/`:

```php
<?php include __DIR__ . '/../../partials/head.php'; ?>

<div class="container">
    <h1>Expenses</h1>
    
    <form method="post" action="/expenses">
        <input type="text" name="description" required>
        <input type="number" name="amount" required>
        <button type="submit">Add Expense</button>
    </form>
    
    <table>
        <tr>
            <th>Description</th>
            <th>Amount</th>
        </tr>
        <?php foreach ($expenses as $expense): ?>
            <tr>
                <td><?= $expense['description']; ?></td>
                <td><?= $expense['amount']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
```

### Running Migrations

```bash
# Run all pending migrations
php migrate.php

# Or in code
$migrator = new Migrator();
$migrator->run();
```

### Database Transaction Example

```php
<?php
$db = App::resolve(Database::class);

try {
    $db->beginTransaction();
    
    // Deduct from account
    $db->query(
        'UPDATE accounts SET balance = balance - :amount WHERE id = :id',
        ['amount' => 100, 'id' => 1]
    );
    
    // Add to another account
    $db->query(
        'UPDATE accounts SET balance = balance + :amount WHERE id = :id',
        ['amount' => 100, 'id' => 2]
    );
    
    $db->commit();
    
} catch (Exception $e) {
    $db->rollBack();
    throw $e;
}
```

---

## Troubleshooting

### Common Issues & Solutions

#### 1. Database Connection Failed

**Error**: `PDOException: SQLSTATE[HY000] [2002] No such file or directory`

**Solutions**:
```bash
# Ensure MySQL is running
service mysql start  # Linux
net start MySQL80   # Windows

# Check .env configuration
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=reliexbg_aiss
DB_USERNAME=root
DB_PASSWORD=your_password

# Test connection
php -r "require 'vendor/autoload.php'; require 'bootstrap.php';"
```

#### 2. Class Not Found Error

**Error**: `Error: Class 'Http\Controllers\Users' not found`

**Solutions**:
```bash
# Regenerate autoload
composer dump-autoload

# Verify class exists and namespace matches
# File: Http/Controllers/Users.php
# Namespace: namespace Http\Controllers;

# Check file naming (case-sensitive on Linux)
```

#### 3. Routing Issues

**Problem**: Routes not working, getting 404 errors

**Solutions**:
```bash
# Ensure .htaccess is in place and readable
ls -la .htaccess

# Check Apache mod_rewrite is enabled
a2enmod rewrite  # Linux

# Verify AllowOverride in Apache config
<Directory /var/www/html/AIS>
    AllowOverride All
</Directory>

# Restart Apache
service apache2 restart  # Linux
```

#### 4. Permission Denied

**Error**: `Permission denied` on storage/logs/

**Solutions**:
```bash
# Fix permissions (Linux/Mac)
chmod 755 storage/logs
chmod 755 storage

# Fix ownership (if needed)
chown -R www-data:www-data storage
```

#### 5. Session Not Persisting

**Problem**: Session data lost between requests

**Solutions**:
```php
// Ensure session started in index.php
session_start();

// Check session configuration in php.ini
session.auto_start = On
session.save_path = "/tmp"

// Verify $_SESSION is being set correctly
$_SESSION['user'] = [
    'ID' => $user['id'],
    'email' => $user['email']
];
```

#### 6. Blank Page / 500 Error

**Solutions**:
```bash
# Check error logs
tail storage/logs/debug_log.txt

# Enable error display (development only)
# In index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

# Check PHP version
php -v  # Should be 8.3 or higher

# Verify all dependencies installed
composer install
```

#### 7. Payment Gateway Not Working

**Solutions**:
```bash
# Verify API keys in .env
STRIPE_SECRET_KEY=sk_test_...
PAYSTACK_SECRET_KEY=sk_test_...

# Test connection
curl -X GET https://api.stripe.com/v1/balance \
  -u sk_test_your_key:

# Check library installation
composer show | grep stripe
```

### Debug Mode

Enable debugging to see detailed errors:

```php
// In bootstrap.php
if ($_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Check logs
tail -f storage/logs/debug_log.txt
```

---

## Performance Optimization

### Database Query Optimization

```php
// ❌ Bad: N+1 query problem
foreach ($invoices as $invoice) {
    $customer = $db->query('SELECT * FROM clients WHERE id = ?', [$invoice['customer_id']])->find();
}

// ✅ Good: Single query with JOIN
$invoices = $db->query('
    SELECT i.*, c.name as customer_name 
    FROM invoices i 
    JOIN clients c ON i.customer_id = c.id
')->get();
```

### Caching

```php
// Simple caching example
$cacheFile = 'storage/cache/users.json';
$cacheTime = 3600; // 1 hour

if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTime) {
    $users = json_decode(file_get_contents($cacheFile), true);
} else {
    $users = $db->query('SELECT * FROM users')->get();
    file_put_contents($cacheFile, json_encode($users));
}
```

### Asset Optimization

```html
<!-- Minify CSS -->
<link rel="stylesheet" href="Public/assets/css/app.min.css">

<!-- Defer non-critical JavaScript -->
<script src="Public/assets/js/app.js" defer></script>

<!-- Use CDN for libraries -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6/dist/jquery.min.js"></script>
```

---

## Security Best Practices

### Input Validation & Sanitization

```php
// ✅ Always validate user input
if (!Validator::email($_POST['email'])) {
    die('Invalid email');
}

// Sanitize output
echo htmlspecialchars($userInput);

// Use prepared statements (always!)
$db->query('SELECT * FROM users WHERE email = :email', ['email' => $email]);
```

### Password Security

```php
// ✅ Hash passwords
$hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Verify password
if (password_verify($inputPassword, $hashedPassword)) {
    // Correct password
}
```

### CSRF Protection

```php
// Generate token
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;

// In form
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

// Verify token
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token mismatch');
}
```

### SQL Injection Prevention

```php
// ❌ Vulnerable to SQL injection
$sql = "SELECT * FROM users WHERE email = '" . $_GET['email'] . "'";

// ✅ Use prepared statements
$db->query('SELECT * FROM users WHERE email = :email', ['email' => $_GET['email']]);
```

---

## Deployment Guide

### Production Checklist

- [ ] Set `.env` variables for production
- [ ] Disable debug mode: `APP_DEBUG=false`
- [ ] Set `INSTALLED=true` in `.env`
- [ ] Run migrations: `php migrate.php`
- [ ] Set proper file permissions
- [ ] Configure HTTPS/SSL
- [ ] Set up automatic backups
- [ ] Enable error logging
- [ ] Configure email settings for production
- [ ] Test payment gateway integration
- [ ] Verify all routes work
- [ ] Load test the application

---

## Support & Resources

- **GitHub Issues**: Report bugs and request features
- **Email**: adebesindamilare39@gmail.com
- **Documentation**: See README.md for installation instructions

---

**End of Documentation**
