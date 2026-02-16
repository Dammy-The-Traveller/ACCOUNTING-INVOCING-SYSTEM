<!DOCTYPE html>
<html lang="zxx" class="js">
<head>
  <meta charset="utf-8">
  <meta name="author" content="Softnio">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
  <link rel="shortcut icon" href="Public/assets/images/favicon.png">
  <title>Error 403</title>
  <link rel="stylesheet" href="Public/assets/css/dashlite9b70.css?ver=3.3.0">
  <link id="skin-default" rel="stylesheet" href="Public/assets/css/theme9b70.css?ver=3.3.0">
</head>
<?php
$logged_user_type = $_SESSION['user']['UserType'] ?? 0;
if($logged_user_type === 1 || $logged_user_type === 2 || $logged_user_type === 5){
$uri = 'dashboard';
}elseif($logged_user_type === 3){
  $uri = 'manage';
}elseif($logged_user_type === 4){
  $uri = 'account-manage';
}else{
  $uri ='dashboard';
}
?>
<body class="nk-body ui-rounder npc-default pg-error">
  <div class="nk-app-root">
    <div class="nk-main ">
      <div class="nk-wrap nk-wrap-nosidebar">
        <div class="nk-content ">
          <div class="nk-block nk-block-middle wide-xs mx-auto">
            <div class="nk-block-content nk-error-ld text-center">
              <h1 class="nk-error-head">403</h1>
              <h3 class="nk-error-title">Oops! Why you’re here?</h3>
              <p class="nk-error-text">We are very sorry for inconvenience.It looks like you are forbidden to access this page</p><a href="/<?=$uri;?>" class="btn btn-lg btn-primary mt-2">Back To Home</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</html>