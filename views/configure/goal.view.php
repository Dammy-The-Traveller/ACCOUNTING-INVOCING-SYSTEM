<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notify = document.getElementById('notify');
            const message = notify.querySelector('.message');
           const form = document.getElementById('data_form');
            message.innerHTML = <?= json_encode($_SESSION['success']) ?>;
            notify.style.display = 'block';
          setTimeout(function() {
            notifyDiv.style.display = 'none';
            form.style.display = 'block';
        }, 3000);
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
        <div class="grid_3 grid_4">


            <form method="post" id="data_form" class="form-horizontal">

                <h5>Set Goals                    <small>(in $)</small>
                </h5>
                <hr>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="income">Income</label>

                    <div class="col-sm-5">
                        <input type="text" placeholder="Income"
                               class="form-control margin-bottom  required" name="income"
                               value="30000">
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="expense">Expenses</label>

                    <div class="col-sm-5">
                        <input type="text" placeholder="Expenses"
                               class="form-control margin-bottom  required" name="expense"
                               value="18000">
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="sales">Sales</label>

                    <div class="col-sm-5">
                        <input type="text" placeholder="Sales"
                               class="form-control margin-bottom  required" name="sales"
                               value="80000">
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="netincome">Net Income</label>

                    <div class="col-sm-5">
                        <input type="text" placeholder="Net Income"
                               class="form-control margin-bottom  required" name="netincome"
                               value="100000">
                    </div>
                </div>


                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"></label>

                    <div class="col-sm-4">
                        <!-- <input 
                               value="Update" data-loading-text="Adding..."> -->
                      <button type="submit" id="time_update"  class="btn btn-success margin-bottom">Update</button>
                    </div>
                </div>


            </form>
        </div>
    </div>
</article><!-- BEGIN VENDOR JS-->
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
        $('#time_update').click(function(event) {
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
