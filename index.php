<?php
error_reporting(E_ALL);
use Core\Session;
use Core\ValidationException;
session_start();
const BASE_PATH = __DIR__ . '/';
require BASE_PATH . 'vendor/autoload.php';

// --- INSTALL GUARD ---
$basePath = __DIR__;
$envPath = $basePath . '/.env';
$lockPath = $basePath . '/storage/install.lock';

// Tiny .env reader
function env_value($key, $default = null) {
    $env = __DIR__ . '/.env';
    if (!file_exists($env)) return $default;
    $lines = file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        if (strpos($line, '=') === false) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        if ($k === $key) return $v;
    }
    return $default;
}

$installedFlag = env_value('INSTALLED', 'false');
$isInstalled = (file_exists($lockPath) || $installedFlag === 'true');
$uri = $_SERVER['REQUEST_URI'] ?? '/';

// Redirect to install if not installed and not already on install route
if (!$isInstalled && strpos($uri, '/AIS/install') !== 0) {
    header('Location: /AIS/install');
    exit;
}

// Redirect away from install if already installed
if ($isInstalled && preg_match('#^/AIS/install#', $uri)) {
    header("Location: /AIS/");
    exit;
}

// Load core dependencies
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require base_path("{$class}.php");
});

require BASE_PATH . 'Core/functions.php';
// Only include bootstrap.php for non-install routes when installed
if ($isInstalled && strpos($uri, '/AIS/install') !== 0) {
    require base_path('bootstrap.php');
}
// Initialize router early
$router = new \Core\Router();
$routes = require base_path('route.php');
//$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
 $uri = str_replace(['/AIS/', '/index.php'], '/', parse_url($_SERVER['REQUEST_URI'])['path']);
$method = isset($_POST['_method']) ? $_POST['_method'] : $_SERVER['REQUEST_METHOD'];



try {
    $router->route($uri, $method);
} catch (ValidationException $exception) {
    Session::flash('errors', $exception->errors);
    Session::flash('old', $exception->old);
    return redirect($router->previousUrl());
}

Session::unflash();