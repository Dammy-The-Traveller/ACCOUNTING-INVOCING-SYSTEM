<?php 
namespace Http\Controllers\Configure;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;

class Settings{
    public function index(){
        views('configure/company.view.php');
    }

      public function DateTime(){
        views('configure/datetime.view.php');
    }

          public function goal(){
        views('configure/goal.view.php');
    }
             public function email(){
        views('configure/email.view.php');
    }
    
    public function security(){
        views('configure/recaptcha.view.php');
    }


}