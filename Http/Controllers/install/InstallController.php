<?php
namespace Http\Controllers\install;
use Core\Database;

class InstallController
{
    public function requirements()
    {
        $checks = [];

        // --- PHP Version ---
        $checks['php_version'] = [
            'status' => version_compare(PHP_VERSION, '8.0.0', '>='),
            'current' => PHP_VERSION
        ];

        // --- Extensions ---
        $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json'];
        foreach ($extensions as $ext) {
            $checks['extensions'][$ext] = extension_loaded($ext);
        }

        // --- Folder Permissions ---
        $writablePaths = ['storage', 'Public/assets'];
        foreach ($writablePaths as $path) {
            $checks['permissions'][$path] = is_writable($path);
        }

        // --- Database Connectivity ---
        try {
            $config = require base_path('config.php'); // adjust path if different
            $db = new Database($config['database']);   // ✅ pass config to Database
            $db->query("SELECT 1");
            $checks['database'] = true;
        } catch (\Throwable $e) {
            $checks['database'] = false;
            $checks['database_error'] = $e->getMessage();
        }

        // Render requirements view
        return views('install/requirements.view.php', [
            'checks' => $checks
        ]);
    }

    public function database()
    {
        $configFile = __DIR__ . '/../../../config.php'; // adjust path
        $existing = [];

        if (file_exists($configFile)) {
            $existing = include $configFile;
        }

        views("install/database.view.php", [
            'existing' => $existing
        ]);
    }

public function saveDatabase()
{
    $host = $_POST['db_host'] ?? 'localhost';
    $name = $_POST['db_name'] ?? '';
    $user = $_POST['db_user'] ?? '';
    $pass = $_POST['db_pass'] ?? '';
    $driver = $_POST['db_driver'] ?? 'mysql';
    $port = $_POST['db_port'] ?? '3306';
    $charset = $_POST['db_charset'] ?? 'utf8mb4';
    $collation = $_POST['db_collation'] ?? 'utf8mb4_general_ci';

    try {
        // connect without db first
        $pdo = new \PDO("{$driver}:host={$host};port={$port}", $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);

        // create DB if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET {$charset} COLLATE {$collation}");
        $pdo->exec("USE `{$name}`");

        // run migrations
        $this->runMigrations($pdo);

        // save config.php
        $configContent = <<<PHP
<?php
return [
    'database' => [
        'driver'   => '{$driver}',
        'host'     => '{$host}',
        'port'     => '{$port}',
        'dbname'   => '{$name}',
        'charset'  => '{$charset}',
        'collation'=> '{$collation}',
        'username' => '{$user}',
        'password' => '{$pass}',
    ]
];
PHP;
        file_put_contents(base_path('config.php'), $configContent);

        $_SESSION['db_setup_ok'] = true;
        header("Location: /AIS/install-database?success=1");
        exit;
    } catch (\PDOException $e) {
        header("Location: /AIS/install-database?error=" . urlencode($e->getMessage()));
        exit;
    }
}

/**
 * Run migrations (auto-create tables)
 */
private function runMigrations(\PDO $pdo)
{
    // Load SQL from file if exists
    $schemaFile = __DIR__ .'/../install/schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        $pdo->exec($sql);
    } else {
        // fallback: create minimal tables inline
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100),
                email VARCHAR(150) UNIQUE,
                password VARCHAR(255),
                role VARCHAR(50) DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                key_name VARCHAR(100) UNIQUE,
                key_value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;
        ");
    }
}

}
