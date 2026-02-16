<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notify = document.getElementById('notify');
            const message = notify.querySelector('.message');
           const form = document.getElementById('data_form');
            message.innerHTML = <?= json_encode($_SESSION['success']) ?>;
            notify.style.display = 'block';
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
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
    <div class="card card-block">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <form method="post" id="product_action" class="form-horizontal">
            <div class="grid_3 grid_4">

                <h5>Edit Email Configuration</h5>
                <hr>


                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="host">Host</label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="host"
                               class="form-control margin-bottom  required" name="host"
                               value="smtp.com">
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="port">Port</label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="port"
                               class="form-control margin-bottom  required" name="port"
                               value="587">
							   <small>Port 587 recommended with TLS</small>
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="auth">Auth</label>

                    <div class="col-sm-6">
                        <select name="auth" class="form-control">
                             <option value="true">--True--
                                
                            </option>                            <option value="true">True

                            </option>
                            <option value="false">False

                            </option>
                        </select>

                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="auth_type">Auth Type</label>

                    <div class="col-sm-6">
                        <select name="auth_type" class="form-control">
                             <option value="none">--none--
                                
                            </option>                            <option value="none">None

                            </option>
                            <option value="tls">TLS

                            </option>
                            <option value="ssl">SSL

                            </option>
                        </select>

                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="username">Username</label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="username"
                               class="form-control margin-bottom  required" name="username"
                               value="info@dtt.com">
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="password">Password</label>

                    <div class="col-sm-6">
                        <input type="password" placeholder="password"
                               class="form-control mb-3  required" name="password"
                               value="123456">
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="sender">Sender Email</label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="email"
                               class="form-control mb-3  required" name="sender"
                               value="info@dtt.com">
                    </div>
                </div>


                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"></label>

                    <div class="col-sm-6">
                        <input type="submit" 
                               value="Update" data-loading-text="Updating...">
							   <button type="submit" id="email_update" class="btn btn-success margin-bottom">Update</button>
                    </div><div class="col-sm-12"><span id="email_update_m"></span></div>
                </div>

            </div>
        </form>
		
        <pre class="mt-3">
            Note: #Refer to documentation to configure email templates.
        </pre>
    </div>
</article>

<script type="text/javascript">
    $("#email_update").click(function (e) {
	 
  $('#email_update_m').html('Please wait...<br>In case of <strong>incorrect</strong> settings, it may take time and application may hang for some period, due to multiple retries to SMTP host. If your settings are <strong>valid</strong> than you will get response within 5 to 15 sec. Status messsage will appear soon on top of this page.');	
        e.preventDefault();
		
        var actionurl = baseurl + 'settings/email';
		 actionProduct(actionurl);
       

    });
</script><!-- BEGIN VENDOR JS-->
<script type="text/javascript">

    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('#sdate').datepicker('setDate', '15-06-2025');
    $('.date30').datepicker('setDate', '15-06-2025');


</script>
<script src="Public/assets/myjs/jquery-ui.js"></script>
<script src="Public/assets/vendor/js/ui/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/unison.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/blockUI.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/jquery.matchHeight-min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/screenfull.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/extensions/pace.min.js" type="text/javascript"></script>
<script src="Public/assets/myjs/jquery.dataTables.min.js"></script>
<script type="text/javascript">var dtformat = $('#hdata').attr('data-df');
    var currency = $('#hdata').attr('data-curr');
    ;</script>
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
    $(document).ready(function() {
        // Handle click event on the "Add" button
        $('#email_update').click(function(event) {
            // Prevent default form submission (if inside a form)
            event.preventDefault(); 
            // Set the message
            $('#notify .message').text('Success: Update action is disabled in demo');
            // Show the alert
            $('#notify').show();
            // Optional: Auto-hide the alert after 3 seconds
         setTimeout(function() {
                $('#notify').fadeOut('slow');
            }, 3000);
        });
    });
</script>

</body>
</html>
