<!DOCTYPE html>
<html lang="en" data-textdirection="ltr" class="loading">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Login</title>
    <link rel="apple-touch-icon" sizes="60x60" href="Public/assets/images/ico/apple-icon-60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="Public/assets/images/ico/apple-icon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="Public/assets/images/ico/apple-icon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="Public/assets/images/ico/apple-icon-152.png">
    <link rel="shortcut icon" type="image/x-icon" href="Public/assets/images/ico/favicon.ico">
    <link rel="shortcut icon" type="image/png" href="Public/assets/images/ico/favicon-32.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="Public/assets/ltr/bootstrap.css">
    <!-- font icons-->
    <link rel="stylesheet" type="text/css" href="Public/assets/fonts/icomoon.css">
    <link rel="stylesheet" type="text/css"
          href="Public/assets/fonts/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="Public/assets/vendors/css/extensions/pace.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="Public/assets/ltr/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="Public/assets/ltr/app.css">
    <link rel="stylesheet" type="text/css" href="Public/assets/ltr/colors.css">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css"
          href="Public/assets/ltr/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css"
          href="Public/assets/ltr/core/menu/menu-types/vertical-overlay-menu.css">
    <link rel="stylesheet" type="text/css"
          href="Public/assets/css/login-register.css">
    <script src="Public/assets/js/core/libraries/jquery.min.js" type="text/javascript"></script>
    <script src="Public/assets/vendors/js/ui/tether.min.js" type="text/javascript"></script>
    <script src="Public/assets/js/core/libraries/bootstrap.min.js" type="text/javascript"></script>

    <script type="text/javascript">var baseurl = 'Public/';</script>

</head>

<body data-open="click" data-menu="vertical-menu" data-col="1-column"
      class="vertical-layout vertical-menu 1-column  blank-page blank-page">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section class="flexbox-container">
                <div class="col-md-4 offset-md-4 col-xs-10 offset-xs-1  box-shadow-2 p-0">

                    <div class="card border-grey border-lighten-3 m-0">
                        <div class="card-header no-border">
                            <div class="card-title text-xs-center">
                                <div class="p-1">
                                    <img loading="lazy" width="100%"
                                            src="https://billing.ultimatekode.com/neo/userfiles/company/logo.png"
                                            alt="Logo"></div>
                            </div>
                            <h6 class="card-subtitle line-on-side text-muted text-xs-center font-small-3 pt-2">
                                <span>Support Ticket</span>
                            </h6>
                        </div>
                        <div class="card-body collapse in">
                            <div class="card-block">
                                <form class="form-horizontal form-simple"
                                      action="/AIS/store-support" method="POST">
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control form-control-lg input-lg" name="name" placeholder="Your Name" required>
                                        <div class="form-control-position">
                                            <i class="icon-head"></i>
                                        </div>
                                    </fieldset>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="email" class="form-control form-control-lg input-lg"
                                               name="email" placeholder="Enter your email" required>
                                        <div class="form-control-position">
                                            <i class="icon-envelope"></i>
                                        </div>
                                    </fieldset>
                                     <fieldset class="form-group position-relative has-icon-left">
                                        <input type="test" class="form-control form-control-lg input-lg"
                                               name="sub" placeholder="Enter Subject" required>
                                        <div class="form-control-position">
                                            <i class="icon-user"></i>
                                        </div>
                                    </fieldset>
                                     <fieldset class="form-group position-relative has-icon-left">
                                        <textarea name="message" class="form-control form-control-lg input-lg" placeholder="Type your message here" required></textarea>
                                        <!-- <input type="text" class="form-control form-control-lg input-lg"
                                               name="email" placeholder="Enter Password" > -->
                                        <div class="form-control-position">
                                            <i class="icon-comment"></i>
                                        </div>
                                    </fieldset>
                                    <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="icon-unlock2"></i>Send </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div><!-- BEGIN VENDOR JS-->

<script src="Public/assets/myjs/jquery-ui.js"></script>
<script src="Public/assets/vendor/js/ui/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/unison.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/blockUI.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/jquery.matchHeight-min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/screenfull.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/extensions/pace.min.js" type="text/javascript"></script>
<script src="Public/assets/myjs/jquery.dataTables.min.js"></script>
<script src="Public/assets/myjs/custom.js?v=3.3"></script>
<script src="Public/assets/myjs/basic.js?v=3.3"></script>
<script src="Public/assets/myjs/control.js?v=3.3"></script>
<script src="Public/assets/js/core/app.js?v=3.3" type="text/javascript"></script>
<script src="Public/assets/js/core/app-menu.js" type="text/javascript"></script>
<!-- BEGIN VENDOR JS-->
</body>
</html