<?php 
$logged_user_firstname = $_SESSION['user']['firstname'];
$logged_user_lastname = $_SESSION['user']['lastname'];
$logged_user_type = $_SESSION['user']['UserType'];


 if($logged_user_type == 1){
    $logged_user_types = 'Super Admin';
 }elseif($logged_user_type == 2){
$logged_user_types = 'Sales Manager';
 }elseif($logged_user_type == 3){
$logged_user_types = 'Sales Person';
 }elseif($logged_user_type == 4){
$logged_user_types = 'Accountant';
 }elseif($logged_user_type == 5){
$logged_user_types = 'Project Manger';
 }elseif($logged_user_type == 6){
$logged_user_types = 'Stock Manager';
 }else{
$logged_user_types = 'USER';
 }
$logged_user_email = $_SESSION['user']['email']; 
 ?>

<?php include __DIR__ . '/../partials/head.php'; ?>
<body data-open="click" data-menu="vertical-menu" data-col="2-columns"
      class="vertical-layout vertical-menu 2-columns  fixed-navbar"><span id="hdata"
                                                                          data-df="dd-mm-yyyy"
                                                                          data-curr="$"></span>

<!-- navbar-fixed-top-->
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<!-- ////////////////////////////////////////////////////////////////////////////-->


<!-- main menu-->
<?php include __DIR__ . '/../partials/Sidenav.php'; ?>
<!-- / main menu-->
 <article class="content">
    <div>
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <div class="">
            <div class="col-md-4">
                <div class="card card-block"><h5>Update Profile Picture</h5>
                    <hr>
                    <div class="ibox-content no-padding border-left-right">
                        <img loading="lazy" alt="profile picture" id="dpic" class="img-responsive"
                             src="Public/assets/img/example.png">
                    </div>
                    <hr>
                    <p><label for="fileupload">Change Your Picture</label><input
                                id="fileupload" type="file"
                                name="files[]"></p></div>


                <!-- signature -->

                <div class="card card-block"><h5>Update Your Signature</h5>
                    <hr>
                    <div class="ibox-content no-padding border-left-right">
                        <img loading="lazy" alt="sign_pic" id="sign_pic" class="img-responsive"
                             src="Public/assets/img/sign.png">
                    </div>
                    <hr>
                    <p>
                        <label for="sign_fileupload">Change Your Signature</label><input
                                id="sign_fileupload" type="file"
                                name="files[]"></p></div>


            </div>
            <div class="col-md-8">
                <div class="card card-block">
                    <form method="post" id="product_action" class="form-horizontal">
                        <div class="grid_3 grid_4">

                            <h5>Update Your Details (<?= $logged_user_types ?? '' ?>)</h5>
                            <hr>


                            <div class="form-group row">

                                <label class="col-sm-2 col-form-label"
                                       for="name">Name</label>

                                <div class="col-sm-10">
                                    <input type="text" placeholder="Name"
                                           class="form-control margin-bottom  required" name="name"
                                           value="<?= $logged_user_firstname;  $logged_user_lastname ?>">
                                </div>
                            </div>


                            <!-- <div class="form-group row">

                                <label class="col-sm-2 col-form-label"
                                       for="address"> Address</label>

                                <div class="col-sm-10">
                                    <input type="text" placeholder="address"
                                           class="form-control margin-bottom required" name="address"
                                           value="Test Street">
                                </div>
                            </div> -->
                            <!-- <div class="form-group row">

                                <label class="col-sm-2 col-form-label"
                                       for="city">City</label>

                                <div class="col-sm-10">
                                    <input type="text" placeholder="city"
                                           class="form-control margin-bottom required" name="city"
                                           value="Test City">
                                </div>
                            </div> -->
                            <!-- <div class="form-group row">

                                <label class="col-sm-2 col-form-label"
                                       for="country">Country</label>

                                <div class="col-sm-10">
                                    <input type="text" placeholder="Country"
                                           class="form-control margin-bottom" name="country"
                                           value="Test Country">
                                </div>
                            </div> -->

                            <!-- <div class="form-group row">

                                <label class="col-sm-2 col-form-label"
                                       for="postbox"></label>

                                <div class="col-sm-10">
                                    <input type="text" placeholder="Postbox"
                                           class="form-control margin-bottom" name="postbox"
                                           value="123456">
                                </div>
                            </div> -->
                            <!-- <div class="form-group row">

                                <label class="col-sm-2 col-form-label"
                                       for="phone"> Phone</label>

                                <div class="col-sm-10">
                                    <input type="text" placeholder="phone"
                                           class="form-control margin-bottom required" name="phone"
                                           value="12345678">
                                </div>
                            </div> -->
                            <!-- <div class="form-group row">

                                <label class="col-sm-2 col-form-label"
                                       for="phone"> Phone (Alt)</label>

                                <div class="col-sm-10">
                                    <input type="text" placeholder="altphone"
                                           class="form-control margin-bottom" name="phonealt"
                                           value="0123">
                                </div>
                            </div> -->
                            
                            <div class="form-group row">

                                <label class="col-sm-2 col-form-label"
                                       for="email"> Email</label>

                                <div class="col-sm-10">
                                    <input type="text" placeholder="email"
                                           class="form-control margin-bottom  required" name="email"
                                           value="<?= $logged_user_email ?>" disabled>
                                </div>
                            </div>


                            <div class="form-group row">

                                <label class="col-sm-2 col-form-label"></label>

                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-success margin-bottom" >Update</button>
                                
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</article>
<script type="text/javascript">
    $("#profile_update").click(function (e) {
        e.preventDefault();
        var actionurl = baseurl + 'user/update';
        actionProduct(actionurl);
    });
</script>
<script src="Public/assets/myjs/jquery.ui.widget.js"></script>
<!-- The basic File Upload plugin -->
<script src="Public/assets/myjs/jquery.fileupload.js"></script>

<!-- BEGIN VENDOR JS-->
<script type="text/javascript">
    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('#sdate').datepicker('setDate', '01-06-2025');
    $('.date30').datepicker('setDate', '01-06-2025');
</script>
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
<script type="text/javascript">
    $.ajax({

        url: baseurl + 'manager/pendingtasks',
        dataType: 'json',
        success: function (data) {
            $('#tasklist').html(data.tasks);
            $('#taskcount').html(data.tcount);

        },
        error: function (data) {
            $('#response').html('Error')
        }

    });


    var winh = document.body.scrollHeight;
    var sideh = document.getElementById('side').scrollHeight;
    var opx = winh - sideh;
    document.getElementById('rough').style.height = opx + "px";
    $('body').on('click', '.menu-toggle', function () {

        var opx2 = winh - sideh + 180;
        document.getElementById('rough').style.height = opx2 + "px";
    });
</script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const demoBtn = document.getElementById('profile_update');
    const message = "🚫 Success: Details updated successfully! This action is disabled in demo.\nTo use this feature, please purchase the system.\nContact us: info@dtt.com";

    if (demoBtn) {
      demoBtn.addEventListener('click', function (e) {
        e.preventDefault(); 
        alert(message);
      });
    }
  });
</script>
</body>
</html>
