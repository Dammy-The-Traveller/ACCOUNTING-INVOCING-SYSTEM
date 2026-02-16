<?php
return [
    'database' => [
        'driver'   => $_ENV['Driver'],
        'host'     => $_ENV['DB_HOST'],
        'port'     => $_ENV['DB_PORT'],
        'dbname'   => $_ENV['DB_DATABASE'],
        'charset'  => 'utf8mb4',
        'collation'=> 'utf8mb4_general_ci',
        'username' =>$_ENV['DB_USERNAME'],
        'password' =>$_ENV['DB_PASSWORD'],
    ]
];