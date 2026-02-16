<?php
namespace Core\Middleware;
use Core\middleware\Auth;
use Core\middleware\Guest;
use Core\middleware\Superadmin;
use Core\middleware\Admin;
use Core\middleware\Accountant;
use Core\middleware\Salesmanager;
use Core\middleware\Manager;
use Core\middleware\Salesperson;
class Middleware{
    public const MAP= [
       "guest"=> Guest::class,
       "auth"=> Auth::class,
       "admin"=> Admin::class,
         "superAdmin"=> Superadmin::class,
         "accountant"=> Accountant::class,
          "SalesManager"=> Salesmanager::class,
           "Salesperson"=> Salesperson::class,
           "Manager"=> Manager::class,
    ];
    public static function resolve($key){
        if(!$key){
            return;
        }

        $middleware = static::MAP[$key] ?? false;
        if(!$middleware){
            throw new \Exception("No mathcing middleware found for key '{$key}'.");
        }
        (new $middleware())->handle();
    } 
}