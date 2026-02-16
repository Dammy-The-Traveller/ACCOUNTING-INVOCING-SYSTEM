<?php
require __DIR__ . '/bootstrap.php';
use Core\SeederRunner;

$base = __DIR__;
$seedersDir = $base . '/database/seeders';

$config = require base_path('config.php');
$dbconf = $config['database'];
$dsn = "mysql:host={$dbconf['host']};port={$dbconf['port']};dbname={$dbconf['dbname']};charset={$dbconf['charset']}";
$pdo = new PDO($dsn, $dbconf['username'], $dbconf['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$runner = new SeederRunner($pdo, $seedersDir);
$results = $runner->runAll();
echo implode(PHP_EOL, $results) . PHP_EOL;
