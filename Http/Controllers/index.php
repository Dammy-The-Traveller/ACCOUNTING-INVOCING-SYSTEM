<?php 
use Core\Session;
views('index.view.php',[
    'errors'=> Session::get('errors'),
]);

