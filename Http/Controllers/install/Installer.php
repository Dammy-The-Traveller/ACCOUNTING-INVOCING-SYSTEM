<?php
namespace Http\Controllers\install;
use PDO;
use Exception;
use Core\Migrator;
use Core\SeederRunner;

class Installer
{
    private string $seedersDir;
    private string $envPath;
    private string $lockPath;
    private string $migrationsDir;

    public function __construct()
    {
        $this->envPath = __DIR__ . '/../../../.env';
        $this->lockPath = __DIR__ . '/../../../storage/install.lock';
        $this->migrationsDir = __DIR__ . '/../../../database/migrations/';
        $this->seedersDir = __DIR__ . '/../../../database/seeders/'; // Initialize seedersDir

        if (file_exists($this->lockPath)) {
            die("Installation already completed. To reinstall, delete storage/install.lock");
        }
    }

    public function welcome()
    {
        echo $this->view('
            <h1>Welcome to CVC Installer</h1>
            <p>This wizard will guide you through installation.</p>
            <form method="post" action="/AIS/install-check">
                <button type="submit">Start Requirements Check</button>
            </form>
        ');
    }

 public function requirements()
{
    $checks = [
        'php_version' => [
            'status' => version_compare(PHP_VERSION, '8.0.0', '>='),
            'current' => PHP_VERSION
        ],
        'extensions' => [],
        'permissions' => [],
        'migrations' => [
            'status' => is_dir($this->migrationsDir),
            'message' => is_dir($this->migrationsDir) ? 'Migrations directory found' : "Migrations directory not found: {$this->migrationsDir} (you can still install if you import SQL manually)"
        ],
        'mysql_server' => [
            'status' => false,
            'message' => ''
        ]
    ];

    // Extensions
    $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json'];
    foreach ($extensions as $ext) {
        $checks['extensions'][$ext] = extension_loaded($ext);
    }

    // Writable paths
    $writable = [
        'storage' => dirname($this->envPath) . '/storage',
        'storage/logs' => dirname($this->envPath) . '/storage/logs',
        'database' => dirname($this->envPath) . '/database',
        '.env (create/write)' => $this->envPath,
    ];
    foreach ($writable as $label => $path) {
        if (is_dir($path)) {
            $checks['permissions'][$label] = is_writable($path);
        } else {
            $parent = dirname($path);
            $checks['permissions'][$label] = is_writable($parent);
        }
    }

    // Test MySQL server connection (without database)
    try {
        $pdo = new PDO('mysql:host=localhost;port=3306', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $checks['mysql_server']['status'] = true;
        $checks['mysql_server']['message'] = 'MySQL server is accessible';
    } catch (\Exception $e) {
        $checks['mysql_server']['status'] = false;
        $checks['mysql_server']['message'] = 'Cannot connect to MySQL server: ' . $e->getMessage();
    }

    // Check for errors to decide whether to halt or proceed
    $errors = [];
    if (!$checks['php_version']['status']) {
        $errors[] = 'PHP 8.0+ is required.';
    }
    foreach ($checks['extensions'] as $ext => $loaded) {
        if (!$loaded) {
            $errors[] = "Extension missing: {$ext}";
        }
    }
    foreach ($checks['permissions'] as $label => $writable) {
        if (!$writable) {
            $errors[] = "Path not writable: {$label}";
        }
    }
    if (!$checks['mysql_server']['status']) {
        $errors[] = $checks['mysql_server']['message'];
    }

    if ($errors) {
        echo $this->view('<h2>Requirements Check</h2>' .
            '<div style="color:#b00020;"><strong>Errors:</strong><ul><li>' . implode('</li><li>', $errors) . '</li></ul></div>' .
            '<a href="/AIS/install">Back</a>'
        );
        return;
    }

    return views('install/requirements.view.php', [
        'checks' => $checks,
        'errors' => $errors
    ]);
}

    public function dbForm()
    {
        echo $this->view('
<div class="container mt-5">
            <h2>Database Configuration</h2>
            <p>Please provide your database credentials.</p>
            <form method="POST" action="/AIS/install-db" class="mt-4">
        <div class="mb-3">
            <label>Host</label>
            <input type="text" name="db_host" class="form-control" value="localhost" required>
        </div>
        <div class="mb-3">
            <label>Database Name</label>
            <input type="text" name="db_name" class="form-control" value="reliexbg_aiss" required>
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="db_user" class="form-control" value="root" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="db_pass" class="form-control" value="">
        </div>
        <div class="mb-3">
            <label>Driver</label>
            <input type="text" name="db_driver" class="form-control" value="mysql" required>
        </div>
        <div class="mb-3">
            <label>Port</label>
            <input type="text" name="db_port" class="form-control" value="3306" required>
        </div>
        <div class="mb-3">
            <label>App URL</label>
            <input type="text" name="app_url" class="form-control" value="' . htmlspecialchars($this->guessAppUrl()) . '" required>
        </div>
        <div class="mb-3">
            <label>App Name</label>
            <input name="app_name" value="DAMMY TECH AIS App" required class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>');
    }

    public function saveDb()
    {
        $dbHost = trim($_POST['db_host'] ?? '');
        $dbPort = trim($_POST['db_port'] ?? '3306');
        $dbName = trim($_POST['db_name'] ?? '');
        $dbUser = trim($_POST['db_user'] ?? '');
        $dbPass = (string)($_POST['db_pass'] ?? '');
        $appName = trim($_POST['app_name'] ?? 'CVC App');
        $appUrl = trim($_POST['app_url'] ?? '');
        $driver = trim($_POST['db_driver'] ?? 'mysql');

        if (!$dbHost || !$dbPort || !$dbName || !$dbUser || !$appUrl || !$driver) {
            echo $this->view('<p style="color:#b00020;">All fields (except DB Password) are required.</p><a href="/AIS/install-db">Back</a>');
            return;
        }

        try {
            $pdo = new PDO("mysql:host={$dbHost};port={$dbPort}", $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
            $pdo->exec("USE `$dbName`");
        } catch (Exception $e) {
            echo $this->view('<p style="color:#b00020;">DB connection failed: ' . htmlspecialchars($e->getMessage()) . '</p><a href="/AIS/install-db">Back</a>');
            return;
        }

        $this->updateEnv([
            'APP_NAME' => $this->quoteIfNeeded($appName),
            'APP_URL' => $this->quoteIfNeeded($appUrl),
            'APP_KEY' => $this->randomKey(),
            'DB_HOST' => $dbHost,
            'DB_PORT' => $dbPort,
            'DB_DATABASE' => $dbName,
            'DB_USERNAME' => $dbUser,
            'DB_PASSWORD' => $dbPass,
            'DB_CONNECTION' => $driver,
            'INSTALLED' => 'false',
        ]);

        echo $this->view('
            <h2>Connection is OK</h2>
            <form method="post" action="/AIS/install-run">
                <button type="submit">Run Migrations</button>
            </form>
        ');
    }

    public function runMigrations()
    {
        [$pdo, $err] = $this->pdoFromEnv();
        if ($err) {
            echo $this->view('<p style="color:#b00020;">' . htmlspecialchars($err) . '</p><a href="/AIS/install-db">Back</a>');
            return;
        }

        $messages = [];
        if (is_dir($this->migrationsDir)) {
            $files = glob($this->migrationsDir . '/*.php') ?: [];
            sort($files);

            foreach ($files as $file) {
                try {
                    $migration = require $file;
                    if (isset($migration['up']) && is_callable($migration['up'])) {
                        // Check if table exists to avoid duplicate creation
                        $tableName = $this->extractTableName($migration['up']);
                        if ($tableName && $this->tableExists($pdo, $tableName)) {
                            $messages[] = basename($file) . ' ... skipped (table already exists)';
                            continue;
                        }
                        $migration['up']($pdo);
                        $messages[] = basename($file) . ' ... OK';
                    } else {
                        $messages[] = basename($file) . ' ... skipped (no up function)';
                    }
                } catch (Exception $e) {
                    echo $this->view('<p style="color:#b00020;">Error in ' 
                        . htmlspecialchars(basename($file)) 
                        . ': ' . htmlspecialchars($e->getMessage()) . '</p>');
                    return;
                }
            }
        } else {
            $messages[] = 'No migrations folder found. Skipped.';
        }

        try {
            $migrator = new Migrator($pdo, $this->migrationsDir);
            $m = $migrator->runPending();
            $messages = array_merge($messages, $m);

            if (is_dir($this->seedersDir)) {
                $seeder = new SeederRunner($pdo, $this->seedersDir);
                $s = $seeder->runAll();
                $messages = array_merge($messages, $s);
            } else {
                $messages[] = 'No seeders folder found. Skipped.';
            }
        } catch (Exception $e) {
            $messages[] = 'Seeding skipped or failed: ' . $e->getMessage();
        }

        echo $this->view('
            <h2>Migrations Completed</h2>
            <pre>' . htmlspecialchars(implode("\n", $messages)) . '</pre>
            <form method="post" action="/AIS/install-finish">
                <button type="submit">Finish Installation</button>
            </form>
        ');
    }

    public function finish()
    {
        $env = $this->setEnvValue(file_get_contents($this->envPath), 'INSTALLED', 'true');
        file_put_contents($this->envPath, $env);

        if (!is_dir(dirname($this->lockPath))) {
            @mkdir(dirname($this->lockPath), 0777, true);
        }
        file_put_contents($this->lockPath, 'installed at ' . date('c'));

        echo $this->view('
            <h2>Installation Complete</h2>
            <p>You can now <a href="/AIS/">go to the application</a>.</p>
            <p><strong>Security tip:</strong> delete the <code>/Http/Controllers/install</code> folder after verifying everything works.</p>
        ');
    }

    // ---------- helpers ----------

    private function view(string $html): string
    {
        include __DIR__ . '/../../../views/partials/head.php';
        return '<!doctype html><html><head><meta charset="utf-8"><title>Installer</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>body{font-family:system-ui,Arial,sans-serif;max-width:720px;margin:40px auto;padding:0 16px}
        input{display:block;margin:6px 0 16px;padding:8px;width:100%;max-width:420px}
        button{padding:10px 16px;cursor:pointer}</style></head>
        <body data-open="click" data-menu="vertical-menu" data-col="2-columns"
              class="vertical-layout vertical-menu 2-columns fixed-navbar"><span id="hdata"
                                                                              data-df="dd-mm-yyyy"
                                                                              data-curr="$"></span>' . $html . '</body></html>';
    }

    private function guessAppUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . '/AIS';
    }

    private function randomKey(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    private function setEnvValue(string $envText, string $key, string $value): string
    {
        $pattern = '/^' . preg_quote($key, '/') . '\s*=.*$/m';
        if (preg_match($pattern, $envText)) {
            return preg_replace($pattern, $key . '=' . $value, $envText);
        }
        return rtrim($envText) . PHP_EOL . $key . '=' . $value . PHP_EOL;
    }

    private function pdoFromEnv(): array
    {
        $env = $this->parseEnv();
        foreach (['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME'] as $k) {
            if (empty($env[$k])) return [null, "Missing {$k} in .env"];
        }
        $dsn = "mysql:host={$env['DB_HOST']};port={$env['DB_PORT']};dbname={$env['DB_DATABASE']};charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, $env['DB_USERNAME'], $env['DB_PASSWORD'] ?? '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            return [$pdo, null];
        } catch (Exception $e) {
            return [null, $e->getMessage()];
        }
    }

    private function parseEnv(): array
    {
        $out = [];
        if (!file_exists($this->envPath)) return $out;
        $lines = file($this->envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') === false) continue;
            [$k, $v] = array_map('trim', explode('=', $line, 2));
            $out[$k] = $v;
        }
        return $out;
    }

    private function quoteIfNeeded(string $value): string
    {
        $value = trim($value, '"');
        if (preg_match('/\s/', $value)) {
            return '"' . addslashes($value) . '"';
        }
        return $value;
    }

    public function updateEnv(array $data)
    {
        $envPath = __DIR__ . '/../../../.env';
        $content = file_exists($envPath) ? file_get_contents($envPath) : '';

        foreach ($data as $key => $value) {
            $value = $this->quoteIfNeeded($value);
            $pattern = "/^" . preg_quote($key, '/') . "=.*/m";
            $replacement = $key . '=' . $value;

            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, $replacement, $content);
            } else {
                $content .= PHP_EOL . $replacement;
            }
        }

        file_put_contents($envPath, trim($content) . PHP_EOL);
    }

    private function tableExists(PDO $pdo, string $tableName): bool
    {
        try {
            $result = $pdo->query("SELECT 1 FROM `$tableName` LIMIT 1");
            return $result !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function extractTableName(callable $upFunction): ?string
    {
        // This is a simplistic approach; adjust based on your migration structure
        $reflection = new \ReflectionFunction($upFunction);
        $source = file($reflection->getFileName());
        $lines = array_slice($source, $reflection->getStartLine() - 1, $reflection->getEndLine() - $reflection->getStartLine() + 1);
        $code = implode('', $lines);

        if (preg_match('/CREATE TABLE\s+`?(\w+)`?/i', $code, $matches)) {
            return $matches[1];
        }
        return null;
    }
}