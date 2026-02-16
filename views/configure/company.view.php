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
    <div class="">
        <div class="col-md-6">
            <div class="card card-block">
                <div id="notify" class="alert alert-success" style="display:none;">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>

                    <div class="message"></div>
                </div>

                <form method="post" id="product_action" class="form-horizontal">
                    <div class="grid_3 grid_4">

                        <h5>Edit Company Details</h5>
                        <hr>


                        <input type="hidden" name="id" value="1">


                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="name">Company Name</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="Name"
                                       class="form-control margin-bottom  required" name="name"
                                       value="DAMMY TECH">
                            </div>
                        </div>


                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="address"> Address</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="address"
                                       class="form-control margin-bottom  required" name="address"
                                       value="K3 DOVE STREET,">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="city">City</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="city"
                                       class="form-control margin-bottom  required" name="city"
                                       value="ACHIMOTA">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="city">Region</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="city"
                                       class="form-control margin-bottom  required" name="region"
                                       value="GR">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="country">Country</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="Country"
                                       class="form-control margin-bottom  required" name="country"
                                       value="GHANA">
                            </div>
                        </div>

                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="postbox">PostBox</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="PostBox"
                                       class="form-control margin-bottom  required" name="postbox"
                                       value="123">
                            </div>
                        </div>

                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="phone"> Phone</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="phone"
                                       class="form-control margin-bottom  required" name="phone"
                                       value="410-987-89-60">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="email"> Email</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="email"
                                       class="form-control margin-bottom  required" name="email"
                                       value="info@dtt.com">
                            </div>
                        </div>
                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"
                                   for="email">Tax ID </label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="TAX ID"
                                       class="form-control margin-bottom" name="taxid"
                                       value="ABC123EF">
                            </div>
                        </div>


                        <div class="form-group row">

                            <label class="col-sm-2 col-form-label"></label>

                            <div class="col-sm-4">
                                <!-- <input 
                                       value="Update Company"
                                       data-loading-text="Updating..."> -->
                                       <button type="submit" id="company_update" class="btn btn-success margin-bottom">Update Company</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card card-block">
                <div id="notify" class="alert alert-success" style="display:none;">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>

                    <div class="message"></div>
                </div>
                <form method="post" id="product_action" class="form-horizontal">
                    <div class="grid_3 grid_4">

                        <h5>Company Logo</h5>
                        <hr>


                        <input type="hidden" name="id" value="1">
                        <div class="ibox-content no-padding border-left-right">
                            <img loading="lazy" alt="image" id="dpic" class="img-responsive"
                                 src="Public/assets/img/relic.png">
                        </div>

                        <hr>
                        <p><label for="fileupload"></label><input
                                    id="fileupload" type="file"
                                    name="files[]"></p>
                        <pre>Recommended logo size is 500x200px.</pre>


                    </div>
                </form>
            </div>
        </div>
    </div>
</article>

<script>
    $(document).ready(function() {
        // Handle click event on the "Add" button
        $('button.btn-success').click(function(event) {
            // Prevent default form submission (if inside a form)
            event.preventDefault();
            
            $('#product_action').hide();
            // Set the message
            $('#notify .message').text('Success: Update Company action is disabled in demo');
            
            // Show the alert
            $('#notify').show();
            
            // Optional: Auto-hide the alert after 3 seconds
         setTimeout(function() {
                $('#notify').fadeOut('slow', function() {
                    // Show product_action after fade-out completes
                    $('#product_action').show();
                });
            }, 3000);
        });
    });
</script>
<!-- <script type="text/javascript">
    $("#company_update").click(function (e) {
        e.preventDefault();
        var actionurl = baseurl + 'settings/company';
        actionProduct(actionurl);
    });
</script> -->
<!-- <script src="https://billing.ultimatekode.com/neo/assets/myjs/jquery.ui.widget.js"></script>
<script src="https://billing.ultimatekode.com/neo/assets/myjs/jquery.fileupload.js"></script> -->
<!-- <script>
    $(function () {
        'use strict';
        var url = 'https://billing.ultimatekode.com/neo/settings/companylogo?id=1';
        $('#fileupload').fileupload({
            url: url,
            dataType: 'json',
            done: function (e, data) {

                $("#dpic").load(function () {
                    $(this).hide();
                    $(this).fadeIn('slow');
                }).attr('src', 'https://billing.ultimatekode.com/neo/userfiles/company/' + data.result);


            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    });

</script> -->
<!-- BEGIN VENDOR JS-->
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
</body>
</html>
