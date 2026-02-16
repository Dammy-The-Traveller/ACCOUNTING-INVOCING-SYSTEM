<?php
$isEdit = isset($account) && !empty($account['id']);
?>
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

 <div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-header">
            <div class="card-header">
                <h4 class="card-title">Company Statistics
                    <!-- <a class="float-xs-right" href="https://billing.ultimatekode.com/neo/reports/refresh_data"><i
                                class="icon-refresh2"></i></a> -->
                            </h4>


            </div>
        </div>
        <div class="content-body"><!-- stats -->

            <!--/ stats -->
            <!--/ project charts -->
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <div class="card">
                        <div class="card-header no-border">
                            <h6 class="card-title">Income,Expense & Sales in last 12 months</h6>

                        </div>

                        <div class="card-body">


                            <div id="invoices-sales-chart"></div>

                        </div>

                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <div class="card">
                        <div class="card-header no-border">
                            <h6 class="card-title">Invoices & Sold Products in last 12 months</h6>

                        </div>

                        <div class="card-body">


                            <div id="invoices-products-chart"></div>

                        </div>

                    </div>
                </div>

            </div>
            <!--/ project charts -->
            <!-- Recent invoice with Statistics -->
            <div class="row match-height">

                <div class="col-xl-12 col-lg-12 ">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">All Time Detailed Statistics</h4>
                            <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="reload"><i class="icon-reload"></i></a></li>
                                    <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-hover mb-1">
                                    <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Income</th>
                                        <th>Expenses</th>
                                        <th>Sales</th>
                                        <th>Invoices</th>
                                        <th>Sold Products</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                <td class="text-truncate">July, 2025</td>
                                <td class="text-truncate"> 5000.00</td>
                            
                                <td class="text-truncate">4457.12</td>
                                 <td class="text-truncate">15542.29</td>
                                  <td class="text-truncate">153</td>
                                   <td class="text-truncate">823</td>
                               
                            </tr><tr>
                                <td class="text-truncate">June, 2025</td>
                                <td class="text-truncate"> 30712.93</td>
                            
                                <td class="text-truncate">11491.20</td>
                                 <td class="text-truncate">37044.81</td>
                                  <td class="text-truncate">14</td>
                                   <td class="text-truncate">28</td>
                               
                            </tr><tr>
                                <td class="text-truncate">May, 2025</td>
                                <td class="text-truncate"> 13030.00</td>
                            
                                <td class="text-truncate">2340.00</td>
                                 <td class="text-truncate">13220.00</td>
                                  <td class="text-truncate">134</td>
                                   <td class="text-truncate">312</td>
                               
                            </tr><tr>
                                <td class="text-truncate">April, 2025</td>
                                <td class="text-truncate"> 8000.00</td>
                            
                                <td class="text-truncate">8457.12</td>
                                 <td class="text-truncate">15542.29</td>
                                  <td class="text-truncate">153</td>
                                   <td class="text-truncate">823</td>
                               
                            </tr><tr>
                                <td class="text-truncate">March, 2025</td>
                                <td class="text-truncate"> 30712.93</td>
                            
                                <td class="text-truncate">8491.20</td>
                                 <td class="text-truncate">37044.81</td>
                                  <td class="text-truncate">14</td>
                                   <td class="text-truncate">28</td>
                               
                            </tr><tr>
                                <td class="text-truncate">February, 2025</td>
                                <td class="text-truncate"> 1244.00</td>
                            
                                <td class="text-truncate">41564.00</td>
                                 <td class="text-truncate">7680.70</td>
                                  <td class="text-truncate">3</td>
                                   <td class="text-truncate">333</td>
                               
                            </tr><tr>
                                <td class="text-truncate">January, 2025</td>
                                <td class="text-truncate"> 4354.00</td>
                            
                                <td class="text-truncate">56.00</td>
                                 <td class="text-truncate">33164.20</td>
                                  <td class="text-truncate">14</td>
                                   <td class="text-truncate">44</td>
                               
                            </tr><tr>
                                <td class="text-truncate">December, 2024</td>
                                <td class="text-truncate"> 13030.00</td>
                            
                                <td class="text-truncate">1840.00</td>
                                 <td class="text-truncate">13220.00</td>
                                  <td class="text-truncate">134</td>
                                   <td class="text-truncate">312</td>
                               
                            </tr><tr>
                                <td class="text-truncate">November, 2024</td>
                                <td class="text-truncate"> 6000.00</td>
                            
                                <td class="text-truncate">1457.12</td>
                                 <td class="text-truncate">15542.29</td>
                                  <td class="text-truncate">153</td>
                                   <td class="text-truncate">823</td>
                               
                            </tr><tr>
                                <td class="text-truncate">October, 2024</td>
                                <td class="text-truncate"> 30712.93</td>
                            
                                <td class="text-truncate">8491.20</td>
                                 <td class="text-truncate">37044.81</td>
                                  <td class="text-truncate">14</td>
                                   <td class="text-truncate">28</td>
                               
                            </tr><tr>
                                <td class="text-truncate">September, 2024</td>
                                <td class="text-truncate"> 544.00</td>
                            
                                <td class="text-truncate">56564.00</td>
                                 <td class="text-truncate">7680.70</td>
                                  <td class="text-truncate">3</td>
                                   <td class="text-truncate">333</td>
                               
                            </tr><tr>
                                <td class="text-truncate">August, 2024</td>
                                <td class="text-truncate"> 4354.00</td>
                            
                                <td class="text-truncate">56.00</td>
                                 <td class="text-truncate">33164.20</td>
                                  <td class="text-truncate">14</td>
                                   <td class="text-truncate">44</td>
                               
                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Recent invoice with Statistics -->
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#invoices-sales-chart').empty();
    Morris.Bar({
        element: 'invoices-sales-chart',
        data: [
            { x: '2024-08-31', y: 4354, z: 56},{ x: '2024-09-30', y: 544, z: 56564},{ x: '2024-10-31', y: 30712, z: 8491},{ x: '2024-11-30', y: 6000, z: 1457},{ x: '2024-12-31', y: 13030, z: 1840},{ x: '2025-01-31', y: 4354, z: 56},{ x: '2025-02-28', y: 1244, z: 41564},{ x: '2025-03-31', y: 30712, z: 8491},{ x: '2025-04-30', y: 8000, z: 8457},{ x: '2025-05-31', y: 13030, z: 2340},{ x: '2025-06-30', y: 30712, z: 11491},{ x: '2025-07-31', y: 5000, z: 4457},
        ],
        xkey: 'x',
        ykeys: ['y', 'z'],
        labels: ['Income', 'expense'],
        hideHover: 'auto',
        resize: true,
        barColors: ['#34cea7', '#ff6e40'],
    });


    $('#invoices-products-chart').empty();

    Morris.Line({
        element: 'invoices-products-chart',
        data: [
            { x: '2024-08-31', y: 44, z: 14},{ x: '2024-09-30', y: 333, z: 3},{ x: '2024-10-31', y: 28, z: 14},{ x: '2024-11-30', y: 823, z: 153},{ x: '2024-12-31', y: 312, z: 134},{ x: '2025-01-31', y: 44, z: 14},{ x: '2025-02-28', y: 333, z: 3},{ x: '2025-03-31', y: 28, z: 14},{ x: '2025-04-30', y: 823, z: 153},{ x: '2025-05-31', y: 312, z: 134},{ x: '2025-06-30', y: 28, z: 14},{ x: '2025-07-31', y: 823, z: 153},
        ],
        xkey: 'x',
        ykeys: ['y', 'z'],
        labels: ['Products', 'Invoices'],
        hideHover: 'auto',
        resize: true,
        lineColors: ['#34cea7', '#ff6e40'],
    });


</script><!-- BEGIN VENDOR JS-->
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
