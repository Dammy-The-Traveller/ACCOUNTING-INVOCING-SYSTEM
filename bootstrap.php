<?php 
use Core\App;
use Core\Container;
use Core\Database;
use Dotenv\Dotenv;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
// print_r($_ENV);


$container = new Container();

$container->bind('Core\Database', function(){
    $config = require base_path('config.php');
    return new Database($config['database']);
});


// $db = $container->resolve('Core\Database');

App::setContainer($container);
global $db;
$db = App::resolve(Database::class);

