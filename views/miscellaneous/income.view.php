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
    <div id="notify" class="alert alert-success" style="display:none;">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <div class="message"></div>
    </div>

    <div class="card card-block">
        <div class="grid_3 grid_4">
            <h6>Income Statement</h6>
            <hr>
            <div class="row sameheight-container">
                <div class="col-md-6">
                    <div class="card card-block sameheight-item">
                        <p id="TotalIncome">Total Income: <span class="value">0</span></p>
                        <!-- <p id="ThisMonthIncome">This Month Income: <span class="value">0</span></p>
                        <p id="param1">Param1: <span class="value">0</span></p>
                        <p id="param2">Param2: <span class="value">0</span></p> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card card-block">
        <div class="grid_3 grid_4">
            <form method="post" id="product_action" class="form-horizontal">
                <h6>Custom Range</h6>
                <hr>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="pay_acc">Account</label>
                    <div class="col-sm-6">
                        <select name="pay_acc" class="form-control">
                            <?php foreach ($accounts as $account): ?>
                                <option value='<?= $account['id'] ?>' id="account"><?= $account['account_number'] ?>-<?= $account['name'] ?></option>
                            <?php endforeach; ?>  
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label" for="sdate">From Date</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" placeholder="Start Date" name="sdate" id="sdate" data-toggle="datepicker">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 control-label" for="edate">To Date</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" placeholder="End Date" name="edate" id="edate" data-toggle="datepicker">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"></label>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-success">Calculate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</article>

<script>
$(document).ready(function () {
    $('#product_action').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '/AIS/income',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function () {
                $('#notify').hide();
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('#TotalIncome .value').text(response.total_income);
                    // $('#ThisMonthIncome .value').text(response.this_month_income);
                    // $('#param1 .value').text(response.param1);
                    // $('#param2 .value').text(response.param2);

                    $('#notify .message').text('Calculation completed successfully!');
                    $('#notify').show();
                } else {
                    $('#notify').removeClass('alert-success').addClass('alert-danger');
                    $('#notify .message').text(response.message || 'Error occurred.');
                    $('#notify').show();
                }
            },
            error: function () {
                $('#notify').removeClass('alert-success').addClass('alert-danger');
                $('#notify .message').text('Server error occurred.');
                $('#notify').show();
            }
        });
    });
});
</script>

<!-- BEGIN VENDOR JS-->
<script type="text/javascript">

    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('#sdate').datepicker('setDate', '14-07-2025');
    $('.date30').datepicker('setDate', '14-07-2025');


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

</body>
</html>
