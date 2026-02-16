<!DOCTYPE html>
<html lang="en" data-textdirection="ltr" class="loading">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Dashboard</title >    
    <link rel="shortcut icon" type="image/x-icon" href="Public/assets/images/logo/favicon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="Public/assets/ltr/bootstrap.css">
    <!-- font icons-->
    <link rel="stylesheet" type="text/css" href="Public/assets/fonts/icomoon.css">
    <link rel="stylesheet" type="text/css"
          href="Public/assets/fonts/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="Public/assets/vendor/css/extensions/pace.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css"
          href="Public/assets/ltr/bootstrap-extended.css?v=3.3">
    <link rel="stylesheet" type="text/css" href="Public/assets/ltr/app.css?v=3.3">
    <link rel="stylesheet" type="text/css" href="Public/assets/ltr/colors.css">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css"
          href="Public/assets/ltr/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css"
          href="Public/assets/ltr/core/menu/menu-types/vertical-overlay-menu.css">
    <link rel="stylesheet" type="text/css"
          href="Public/assets/ltr/core/colors/palette-gradient.css">
    <link rel="stylesheet" href="Public/assets/custom/datepicker.min.css?v=3.3">
    <link rel="stylesheet" href="Public/assets/custom/jquery.dataTables.css?v=3.3">
    <link rel="stylesheet" href="Public/assets/custom/summernote-bs4.css?v=3.3">
    <link rel="stylesheet" href="Public/assets/custom/select2.min.css?v=3.3">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->

    <link rel="stylesheet" type="text/css" href="Public/assets/ltr/style.css?v=3.3">
    <link rel="stylesheet" type="text/css" href="Public/assets/ltr/custom.css?v=3.3">
    <link rel="stylesheet" href="Public/assets/custom/style.css?v=3.3">
    <!-- END Custom CSS-->

    <script src="Public/assets/js/core/libraries/jquery.min.js" type="text/javascript"></script>

    <script src="Public/assets/vendor/js/ui/tether.min.js" type="text/javascript"></script>
    <script src="Public/assets/js/core/libraries/bootstrap.min.js" type="text/javascript"></script>


    <script src="Public/assets/portjs/raphael.min.js" type="text/javascript"></script>
    <script src="Public/assets/portjs/morris.min.js" type="text/javascript"></script>


    <script src="Public/assets/myjs/datepicker.min.js?v=3.3"></script>
    <script src="Public/assets/myjs/summernote-bs4.min.js?v=3.3"></script>
    <script src="Public/assets/myjs/select2.min.js?v=3.3"></script>
    <!-- Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
<script>
  let logoutTimer;
function resetLogoutTimer() {
    clearTimeout(logoutTimer);
    logoutTimer = setTimeout(function () {
        alert("You have been logged out due to inactivity.");
        window.location.href = "/logout";
    }, 21600000); // Reset 6-hour timer
}
// Reset timer on user activity
document.addEventListener("mousemove", resetLogoutTimer());
document.addEventListener("keydown", resetLogoutTimer());
document.addEventListener("click", resetLogoutTimer());
// Start the timer initially
resetLogoutTimer();
</script>
</head>



