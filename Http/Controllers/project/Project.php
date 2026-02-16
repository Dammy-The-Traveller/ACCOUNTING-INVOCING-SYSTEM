<?php 
namespace Http\Controllers\project;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;

class Project{
    public function index(){

         views('project/index.view.php');
    }

        public function manage(){

         views('project/manage.view.php');
    }

           public function todo(){

         views('project/to_do.view.php');
    }
}