<?php $account ?>
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
        <div class="row animated fadeInRight">
            <div class="col-md-8">
                <div class="card card-block">
                    <div class="ibox-title">
                        <h5>Details</h5>
                        <div class="card sameheight-item stats" data-exclude="xs" style="height: 323px;">
                            <div class="card-block">

                                <div class="row row-sm stats-container">
                                    <div class="col-xs-12 col-sm-6 stat-col">

                                        <div class="stat">
                                            <div class="name"> Account No</div>
                                            <div class="value"> <?= $account['account_number'] ?? '' ?></div>

                                        </div>
                                        <hr>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 stat-col">

                                        <div class="stat">
                                            <div class="name"> Name</div>
                                            <div class="value"><?= $account['name'] ?? '' ?> </div>

                                        </div>
                                        <hr>
                                    </div>

                                    <div class="col-xs-12 col-sm-6 stat-col">

                                        <div class="stat">
                                            <div class="name">Balance</div>
                                            <div class="value"> <?= $account['current_balance'] ?? '' ?></div>

                                        </div>
                                        <hr>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 stat-col">

                                        <div class="stat">
                                            <div class="name"> Opening Date</div>
                                            <div class="value"> <?= $account['created_at'] ?? '' ?></div>

                                        </div>
                                        <hr>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 stat-col">

                                        <div class="stat">
                                            <div class="name"> Note</div>
                                            <div class="value"> <?= $account['note'] ?? 'No notes' ?></div>

                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</article><!-- BEGIN VENDOR JS-->
<script type="text/javascript">

    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('#sdate').datepicker('setDate', '11-07-2025');
    $('.date30').datepicker('setDate', '11-07-2025');


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
