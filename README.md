# DAMMY TECH AIS - Accounting & Inventory System

<img width="1884" height="883" alt="image" src="https://github.com/user-attachments/assets/f7705273-5e1a-430a-8651-ed1b8709bba4" />

A comprehensive **Accounting and Inventory Management System** built with PHP. This application provides complete tools for managing your business finances, inventory, sales, and customer relationships with an intuitive web-based interface.

##  Features

### Core Modules
- **Accounting Module**: Chart of accounts, account management, balance tracking
- **Inventory Management**: Product catalog, stock tracking, warehouse management, inventory adjustments
- **Sales Management**: Invoice creation, recurring sales, quote generation, sales tracking
- **Purchase Management**: Purchase orders, supplier management, return management
- **Customer Relationship Management (CRM)**: Customer management, groups, support tickets
- **User Management**: Role-based access control with multiple user types (Admin, Accountant, Sales Manager, Salesperson, etc.)
- **Reporting & Analytics**: Financial reports, inventory reports, sales dashboards

### Technical Features
- **Role-Based Access Control**: Different permission levels for Superadmin, Admin, Manager, Accountant, Salesperson
- **Database Migrations**: Automatic schema management with migration system
- **PDF Generation**: Generate invoices, quotes, and reports as PDF files
- **QR Code Support**: QR code generation for products and transactions
- **Payment Integration**: Stripe and Paystack payment gateway support
- **Email Support**: PHP Mailer integration for notifications
- **Session Management**: Secure session handling and authentication
- **Input Validation**: Built-in form validation system
- **Error Handling**: Comprehensive error logging and debugging

##  Technology Stack

- **Language**: PHP 8.3
- **Framework**: Custom MVC Framework
- **Database**: MySQL 8.0+
- **Front-end**: HTML5, CSS3, JavaScript
- **Key Libraries**:
  - Illuminate Collections (Laravel)
  - DOMPDF (PDF generation)
  - Stripe (Payment processing)
  - PHPMailer (Email sending)
  - QR Code generation (Endroid & Bacon)
  - Symfony VarDumper (Debugging)

##  Requirements

Before installing, ensure you have:

- **PHP 8.3** or higher
- **MySQL 8.0** or higher (or compatible database)
- **Composer** (PHP package manager)
- **Web Server**: Apache with mod_rewrite enabled (or Nginx)
- **WAMP/LAMP/LEMP Stack** (recommended for development)

##  Installation Guide

### Step 1: Clone or Download the Repository

```bash
git clone https://github.com/Dammy-The-Traveller/ACCOUNTING-INVOCING-SYSTEM
cd AIS
```

Or download the ZIP file and extract it to your web server directory (e.g., `C:\wamp64\www\AIS` for WAMP).

### Step 2: Install Dependencies

```bash
composer install
```

This will install all required PHP packages listed in `composer.json`.

### Step 3: Configure Environment Variables

Create a `.env` file in the project root (copy from `.env.example`):

```bash
cp .env.example .env
```

Or manually create `.env` with the following configuration:

```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=reliexbg_aiss
DB_USERNAME=root
DB_PASSWORD=your_password
Driver=mysql
APP_NAME="DAMMY TECH AIS App"
APP_URL=http://localhost/AIS
APP_KEY=753cd94d7e6acd76e755d14b17a89e9801d620b014a878fb0e30edc9851a233e
INSTALLED=false

STRIPE_SECRET_KEY=your_stripe_key_here
PAYSTACK_SECRET_KEY=your_paystack_key_here
SMTP_USERNAME=your_email@domain.com
SMTP_PASSWORD=your_email_password
NEWS_API_KEY=your_news_api_key_here

DB_CONNECTION=mysql
```

### Step 4: Set Up Database

#### Option A: Using the Built-in Installer (Recommended)

1. Navigate to `http://localhost/AIS/install` in your browser
2. The installer will guide you through:
   - Environment configuration
   - Database connection setup
   - Running migrations
   - Seeding initial data

#### Option B: Manual Database Setup

1. Create a new MySQL database:

```sql
CREATE DATABASE reliexbg_aiss CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

2. Run migrations:

```bash
php migrate.php
```

3. Seed sample data (optional):

```bash
php seed.php
```

### Step 5: Configure Web Server

#### For Apache (WAMP):

Ensure `.htaccess` support is enabled and the document root is set to the project directory.

#### For Development:

Use PHP's built-in server:

```bash
php -S localhost:8000
```

Then access the application at `http://localhost:8000`

### Step 6: Access the Application

Navigate to `http://localhost/AIS` (or your configured URL) in your web browser.

**Default Credentials** (after installation):
- Username: `admin`
- Password: `password` (change after first login)

## Project Structure

```
AIS/
├── Core/                    # Core framework files
│   ├── App.php             # Application bootstrap
│   ├── Database.php        # Database connection handler
│   ├── Router.php          # URL routing
│   ├── Validator.php       # Input validation
│   ├── Session.php         # Session management
│   ├── Authenticator.php   # Authentication logic
│   ├── Container.php       # Dependency injection
│   ├── middleware/         # Route middlewares
│   │   ├── Auth.php        # Authentication middleware
│   │   ├── Admin.php       # Admin access middleware
│   │   └── ...             # Other role-based middlewares
│   └── functions.php       # Helper functions
├── Http/
│   ├── Controllers/        # Application controllers
│   │   ├── Dashboard.php
│   │   ├── Login.php
│   │   ├── Sales/          # Sales management
│   │   ├── Stock/          # Inventory management
│   │   ├── Customers/      # CRM
│   │   ├── Configure/      # Settings & configuration
│   │   ├── account/        # Account management
│   │   └── ...
│   └── Forms/              # Form validation classes
├── views/                  # View templates
│   ├── login.view.php
│   ├── Dashboard.view.php
│   ├── Sales/
│   ├── Stock/
│   └── ...
├── database/
│   ├── migrations/         # Database migration files
│   ├── seeders/            # Data seeders
│   └── dumps/              # Database schema exports
├── Public/                 # Public assets
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── img/
│   │   └── plugins/
│   ├── uploads/            # User uploads
│   ├── invoices/           # Generated invoices
│   └── pdf/                # PDF libraries
├── storage/                # Application storage
│   └── logs/               # Application logs
├── vendor/                 # Composer dependencies
├── config.php              # Configuration file
├── bootstrap.php           # Bootstrap file
├── route.php               # Route definitions
├── index.php               # Application entry point
├── migrate.php             # Migration runner
├── seed.php                # Database seeder runner
└── composer.json           # Project dependencies
```

##  Configuration

### Database Configuration

Edit the `.env` file to configure your database connection:

```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Application Configuration

Key settings can be modified in `config.php`:

```php
return [
    'database' => [
        'driver'   => 'mysql',
        'host'     => 'localhost',
        'port'     => 3306,
        // ... other settings
    ]
];
```

### Payment Gateway Setup

Update `.env` with your payment provider credentials:

```
STRIPE_SECRET_KEY=sk_test_your_stripe_key
PAYSTACK_SECRET_KEY=sk_test_your_paystack_key
```

### Email Configuration

Configure SMTP settings in `.env`:

```
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
```

##  Security

- **Authentication**: Secure login system with session management
- **Authorization**: Role-based access control (RBAC)
- **Input Validation**: All user inputs are validated and sanitized
- **HTTPS**: Use HTTPS in production
- **Environment Variables**: Sensitive data stored in `.env` (not committed to version control)
- **SQL Injection Protection**: Parameterized queries with prepared statements

## Testing

Run the test suite using PHPUnit:

```bash
vendor/bin/pest
```

Tests are located in the `tests/` directory and configured in `phpunit.xml`.

##  Database Migrations

### Run All Migrations

```bash
php migrate.php
```

### Available Migrations

- `20250819_000001_create_users_table.php` - Creates user authentication tables
- `20250819_000002_import_full_dump.php` - Imports complete database schema

Migrations are located in `database/migrations/`

##  Database Seeding

Seed sample data for development:

```bash
php seed.php
```

Seeders are located in `database/seeders/`

##  Troubleshooting

### Common Issues

**1. "Class not found" Error**
- Solution: Run `composer install` to install dependencies
- Clear PHP autoload cache if needed

**2. Database Connection Failed**
- Check `.env` file has correct database credentials
- Ensure MySQL service is running
- Verify database exists and user has proper permissions

**3. Permission Denied**
- Ensure `storage/logs/` directory is writable: `chmod 755 storage/logs/`
- Check file permissions on the project root

**4. Blank Page / 500 Error**
- Check `storage/logs/debug_log.txt` for error messages
- Enable error reporting by checking `index.php` error_reporting setting
- Verify PHP 8.3+ is installed

**5. Routes Not Working**
- Enable `.htaccess` support in Apache
- Check `AllowOverride All` in Apache configuration
- Restart Apache/web server

##  Debug Mode

View detailed debug logs in the debug log file:

```bash
tail storage/logs/debug_log.txt
```

##  API Endpoints

The application includes API endpoints for external integrations located in `Http/Controllers/apis/`

##  Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is proprietary software. All rights reserved.

##  Author

**Dammy The Traveller**
- Email: adebesindamilare39@gmail.com

##  Support

For issues, bug reports, or questions:
- Create an issue on GitHub
- Contact the author via email

## 📋 Checklist for First-Time Setup

- [ ] Install PHP 8.3+
- [ ] Install Composer
- [ ] Install/Setup MySQL 8.0+
- [ ] Clone/Download repository
- [ ] Run `composer install`
- [ ] Create and configure `.env` file
- [ ] Create MySQL database
- [ ] Run database migrations
- [ ] Access application at configured URL
- [ ] Login with default credentials
- [ ] Change default password
- [ ] Configure payment gateways (if needed)
- [ ] Configure email settings (if needed)

##  Version History

- **v1.0.0** (August 2025) - Initial Release
  - Complete accounting module
  - Inventory management system
  - Sales and purchase management
  - CRM functionality
  - Multi-user support with role-based access

---

**Last Updated**: February 2026
