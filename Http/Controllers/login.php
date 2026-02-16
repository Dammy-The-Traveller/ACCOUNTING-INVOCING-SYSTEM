<?php 
namespace Http\Controllers;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;

class Login{
    public function index(){
        views('login.view.php');
    }
}