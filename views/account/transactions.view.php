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
        <div class="grid_3 grid_4">

            <div class="well col-xs-12">
                <div class="row">
                    <div class="text-center">
                        <h5>Transaction Details </h5>
                        <a href="/AIS/trans-generate?id=<?=  $transactionId ?? ''?>" class="btn btn-info btn-xs" target="_blank"  title="Print">
                            <span class="icon-print"></span>
                        </a>                    
                    </div>
                    <hr>
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <address>
                            <strong>DAMMY TECH</strong><br>K3 Dove Street, 
                            <br><br>   Phone : +233-598-238-797<br>    Email : info@dtt.com                       
                         </address>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                        <address>
                            <strong><?= $customer['name'] ?? 'ADMIN' ?></strong><br>
                           <?= $customer['address'] ?? 'NOT REGISTERED' ?><br>
                            <?= $customer['city'] ?? 'NOT REGISTERED' ?><br>
                             Phone: <?= $customer['phone'] ?? 'NOT REGISTERED' ?><br>   
                             Email: <?= $customer['email'] ?? 'NOT REGISTERED' ?>                       
                         </address>
                    </div>

                </div>

                <div class="row">
                    <hr>
                    <div class="col-xs-6 col-sm-6 col-md-6">
                    <p><?= $transaction['type'] ?? '' ?> : $<?= $transaction['amount'] ?? '0.00' ?> </p>
                    <p>Type : <?= $transaction['type'] ?? '' ?></p>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                 <p>Date : <?= isset($transaction['created_at']) ? date('Y-m-d', strtotime($transaction['created_at'])) : '' ?></p>
                    <p> ID : TRN#<?= $transaction['id'] ?? '0' ?></p>
                    <p>Category :<?= $category['name'] ?? '' ?></p>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 ">
                    <p>Note : <?= $transaction['description'] ?? 'no notes' ?></p>
                </div>
          </div>

                </div>
            </div>

</article>
<!-- BEGIN VENDOR JS-->
<script type="text/javascript">
    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('#sdate').datepicker('setDate', '12-07-2025');
    $('.date30').datepicker('setDate', '12-07-2025');
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
