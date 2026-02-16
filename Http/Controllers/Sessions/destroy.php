<?php 
use Core\Authenticator;
$auths = new Authenticator();
$auths->logout();
header("Location:/AIS/");
exit();