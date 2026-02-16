<?php
require __DIR__ . '/bootstrap.php';
use Core\Migrator;

$base = __DIR__;
$migrationsDir = $base . '/database/migrations';

// Build PDO from your config.php (or env)
$config = require base_path('config.php');
$dbconf = $config['database'];
$dsn = "mysql:host={$dbconf['host']};port={$dbconf['port']};dbname={$dbconf['dbname']};charset={$dbconf['charset']}";
$pdo = new PDO($dsn, $dbconf['username'], $dbconf['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$migrator = new Migrator($pdo, $migrationsDir);
$results = $migrator->runPending();
echo implode(PHP_EOL, $results) . PHP_EOL;
