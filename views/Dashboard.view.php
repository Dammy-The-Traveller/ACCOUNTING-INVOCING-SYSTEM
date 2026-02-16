<?php require('partials/head.php'); ?>
<body data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns fixed-navbar">
  
<span id="hdata" data-df="dd-mm-yyyy" data-curr="$"></span>

<!-- navbar-fixed-top-->
<?php require('partials/navbar.php'); ?>
<!-- navbar-fixed-top-->

<!-- Side menu-->
<?php require('partials/Sidenav.php'); ?>
<!-- / SIde menu-->
 <?php require('fetchInvoices.view.php'); ?>
 <script type="text/javascript">
    var dataVisits = [
        { x: '2025-06-30', y: 0},{ x: '2025-06-29', y: 428},{ x: '2025-06-28', y: 1400},{ x: '2025-06-27', y: 400},{ x: '2025-06-26', y: 900},{ x: '2025-06-25', y: 620},{ x: '2025-06-24', y: 100},{ x: '2025-06-23', y: 437},{ x: '2025-06-22', y: 708},{ x: '2025-06-21', y: 40},{ x: '2025-06-20', y: 80},{ x: '2025-06-19', y: 226},    ];
    var dataVisits2 = [
        { x: '2025-06-29', y: 1000},{ x: '2025-06-28', y: 50},{ x: '2025-06-27', y: 18},{ x: '2025-06-26', y: 50},{ x: '2025-06-25', y: 90},{ x: '2025-06-24', y: 55},];

</script>

<!-- Main menu-->
<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-header row"><div class="alert alert-info"><div class="message"><strong>Info</strong> Check our other application, with CRM, HRM and POS  <a href="https://dtt.com/" class="btn btn-grey btn-lg"><span class="icon-file-text2" aria-hidden="true"></span> Explore  </a></div></div>
        </div>
        <div class="content-body"><!-- stats -->
           <div class="row">
                <div class="col-xl-3 col-md-6 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <div class="media">
                                    <div class="media-body text-xs-left">
                                        <h3 class="pink"><?= htmlspecialchars($todayInvoices) ?></h3>
                                        <span>Today Invoices</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="icon-file-text2 pink font-large-2 float-xs-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <div class="media">
                                    <div class="media-body text-xs-left">
                                        <h3 class="teal"><?= htmlspecialchars($monthInvoices) ?></h3>
                                        <span>This Month Invoices</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="icon-paste teal font-large-2 float-xs-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <div class="media">
                                    <div class="media-body text-xs-left">
                                        <h3 class="deep-orange"><?= htmlspecialchars($todayCurrencySymbol) ?><?= number_format($todaySales, 2) ?> </h3>
                                        <span>Today Sales</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="icon-coin-dollar deep-orange font-large-2 float-xs-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <div class="media">
                                    <div class="media-body text-xs-left">
                                        <h3 class="cyan"><?= htmlspecialchars($todayCurrencySymbol) ?><?= number_format($monthSales, 2) ?> </h3>
                                        <span>This Month Sales</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="icon-briefcase2 cyan font-large-2 float-xs-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ stats -->
            <!--/ project charts -->
            <div class="row">
                <div class="col-xl-8 col-lg-12">
                    <div class="card">
                        <div class="card-header no-border">
                            <h6 class="card-title">Graphical Presentation of invoices and sales done in last 30 days.</h6>

                        </div>

                        <div class="card-body">


                            <div id="invoices-sales-chart"></div>

                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-xs-3 text-xs-center">
                                    <span class="text-muted">Today Income</span>
                                    <h4 class="block font-weight-normal">$ 0.00</h4>
                                    <progress class="progress progress-xs mt-2 progress-success" value="100"
                                              max="100"></progress>
                                </div>
                                <div class="col-xs-3 text-xs-center">
                                    <span class="text-muted">Today Expenses</span>
                                    <h4 class="block font-weight-normal">$ 0.00</h4>
                                    <progress class="progress progress-xs mt-2 progress-warning" value="100"
                                              max="100"></progress>
                                </div>
                                <div class="col-xs-3 text-xs-center">
                                    <span class="text-muted">Today Sold Products</span>
                                    <h4 class="block font-weight-normal">9</h4>
                                    <progress class="progress progress-xs mt-2 progress-light-blue" value="100"
                                              max="100"></progress>
                                </div>

                                <div class="col-xs-3 text-xs-center">
                                    <span class="text-muted">Total Revenue</span>
                                    <h4 class="block font-weight-normal">$ 4,077.54</h4>
                                    <progress class="progress progress-xs mt-2 progress-indigo" value="100"
                                              max="100"></progress>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6">
                    <div class="card card-inverse bg-info">

                        <div class="card-header">
                            <div class="header-block">
                                <h4 class="title">
                                    Income vs Expenses                                </h4></div>
                        </div>
                        <div class="card-body">
                            <div id="salesbreakdown" class="card sameheight-item sales-breakdown"
                                 data-exclude="xs,sm,lg">
                                <div class="dashboard-sales-breakdown-chart" id="dashboard-sales-breakdown-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ project charts -->
            <!-- Recent invoice with Statistics -->
            <div class="row match-height">
                <div class="col-xl-4 col-lg-6">

                    <div class="card">
                        <div class="card-header ">
                            <h4 class="card-title">July 2025 Targets</h4>

                        </div>
                        <div class="card-body">
                            <div class="media">
                                <div class="p-1 text-xs-center bg-light-blue media-left media-middle">
                                    <i class="icon-clubs font-large-2 white"></i>
                                </div>
                                <div class="p-1 media-body">
                                    <h5 class="light-blue">   Income</h5>
                                    <h5 class="text-bold-400">$ 5,341.10/$ 30,000.00</h5>
                                    <progress class="progress progress-striped progress-light-blue mt-1 mb-0"
                                              value="17.80" max="100"></progress>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="media">
                                <div class="p-1 text-xs-center bg-orange media-left media-middle">
                                    <i class="icon-list-alt font-large-2 white"></i>
                                </div>
                                <div class="p-1 media-body">
                                    <h5 class="orange"> Expenses</h5>
                                    <h5 class="text-bold-400">$ 1,263.56/$ 18,000.00</h5>
                                    <progress class="progress progress-striped progress-orange mt-1 mb-0"
                                              value="7.02" max="100"></progress>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="media">
                                <div class="p-1 text-xs-center bg-success media-left media-middle">
                                    <i class="icon-bar-chart font-large-2 white"></i>
                                </div>
                                <div class="p-1 media-body">
                                    <h5 class="success"> Sales</h5>
                                    <h5 class="text-bold-400">$ 3,424.23/$ 80,000.00</h5>
                                    <progress class="progress progress-striped progress-success mt-1 mb-0"
                                              value="4.28" max="100"></progress>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="media">
                                <div class="p-1 text-xs-center bg-pink media-left media-middle">
                                    <i class="icon-money font-large-2 white"></i>
                                </div>
                                <div class="p-1 media-body">
                                    <h5 class="pink"> Net Income</h5>
                                    <h5 class="text-bold-400">$ 4,077.54/$ 100,000.00</h5>
                                    <progress class="progress progress-striped progress-pink mt-1 mb-0"
                                              value="4.08" max="100"></progress>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-12 ">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Recent Invoices <a
                                        href="/AIS/create"
                                        class="btn btn-primary btn-sm rounded">Add Sale</a> <a
                                        href="/AIS/manage"
                                        class="btn btn-success btn-sm rounded">Manage Invoices</a>
                            </h4>
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
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Invoices#</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Due</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody class="table-bordered">
                                  <?php foreach ($invoices as $invoice): ?>
                <tr>
                    <td><?= htmlspecialchars($invoice['id']) ?></td>
                    <td><?= htmlspecialchars($invoice['customer_name'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars(ucfirst($invoice['status'])) ?></td>
                    <td><?= htmlspecialchars(date('Y-m-d', strtotime($invoice['due_date']))) ?></td>
                    <td><?= number_format($invoice['grand_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($invoices)): ?>
                <tr>
                    <td colspan="5">No invoices found</td>
                </tr>
            <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Recent invoice with Statistics -->
            <div class="row match-height">
                <div class="col-xl-8 col-md-8 col-sm-12">


                    <div class="card" style="height: 250px;" id="transactions">
                        <div class="card-header">
                            <h4 class="card-title">Cashflow</h4>
                        </div>
                        <div class="card-body">
                            <div class="card-block">
                                <p>Graphical Presentation of income and expenses done in last 30 days.</p>
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="base-tab1" data-toggle="tab" aria-controls="tab1"
                                           href="#sales"
                                           aria-expanded="true">Income</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="base-tab2" data-toggle="tab" aria-controls="tab2"
                                           href="#transactions1"
                                           aria-expanded="false">Expenses</a>
                                    </li>


                                </ul>
                                <div class="tab-content pt-1">
                                    <div role="tabpanel" class="tab-pane active" id="sales" aria-expanded="true"
                                         data-toggle="tab">
                                        <div id="dashboard-income-chart"></div>

                                    </div>
                                    <div class="tab-pane" id="transactions1" data-toggle="tab" aria-expanded="false">
                                        <div id="dashboard-expense-chart"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="col-xl-4 col-md-4 col-sm-12">
                    <div class="card">
                        <!-- <div class="card-header">
                            <h4 class="card-title">Task Manager  <a
                                        href="https://billing.ultimatekode.com/neo/manager/todo"><i
                                            class="icon-arrow-right deep-orange"></i></a></h4>
                        </div>
                        <div class="card-body pt-1">


                            <div class="form-group"><div class="input-group"><label class="display-inline-block custom-control custom-radio ml-1">
													<input value="1" type="checkbox" class="checkbox custom-control-input">
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description ml-0">Install Tivoli Business Systems Manager and appropriate patches on test or QA servers</span>
												</label></div><hr></div>
                        <div class="form-group"><div class="input-group"><label class="display-inline-block custom-control custom-radio ml-1">
													<input value="2" type="checkbox" class="checkbox custom-control-input">
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description ml-0">Install event enablement on the Tivoli Enterprise Console server.I</span>
												</label></div><hr></div>
                        <div class="form-group"><div class="input-group"><label class="display-inline-block custom-control custom-radio ml-1">
													<input value="3" type="checkbox" class="checkbox custom-control-input" checked>
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description ml-0">Install console machines and prerequisite software.</span>
												</label></div><hr></div>
                        <div class="form-group"><div class="input-group"><label class="display-inline-block custom-control custom-radio ml-1">
													<input value="4" type="checkbox" class="checkbox custom-control-input">
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description ml-0">Order the server hardware for production as well as test/quality assurance</span>
												</label></div><hr></div>
                        <div class="form-group"><div class="input-group"><label class="display-inline-block custom-control custom-radio ml-1">
													<input value="5" type="checkbox" class="checkbox custom-control-input">
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description ml-0">Create configuration level objects for the test LPAR.</span>
												</label></div><hr></div>
                        <div class="form-group"><div class="input-group"><label class="display-inline-block custom-control custom-radio ml-1">
													<input value="6" type="checkbox" class="checkbox custom-control-input">
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description ml-0">Database backup and maintenance</span>
												</label></div><hr></div>
                        

                        </div> -->
                    </div>
                </div>
            </div>
            <!--stock-->
            <div class="row match-height">

                <div class="col-xl-8 col-lg-12 ">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Recent <a
                                        href="/AIS/transactions"
                                        class="btn btn-primary btn-sm rounded">Transactions</a>
                            </h4>
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
                                        <th>Date#</th>
                                        <th>Account</th>
                                        <th>Debit</th>
                                        <th>Credit</th>

                                        <th>Method</th>
                                    </tr>
                                    </thead>
                               <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($transaction['created_at']))) ?></td>
                    <td><?= htmlspecialchars($transaction['account_name'] ?? 'Unknown') ?></td>
                    <td><?= $transaction['type'] === 'debit' ? number_format($transaction['amount'], 2) : '0.00' ?></td>
                    <td><?= $transaction['type'] === 'credit' ? number_format($transaction['amount'], 2) : '0.00' ?></td>
                    <td><?= htmlspecialchars($transaction['payment_method']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="5">No transactions found</td>
                </tr>
            <?php endif; ?>
        </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6">

                    <div class="card">
                        <div class="card-header ">
                            <h4 class="card-title">Stock Alert</h4>

                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">

                                
                            </ul>

                        </div>
                    </div>



                </div>
            </div>

        </div>
    </div>
</div>
<!-- Main menu End-->


<script type="text/javascript">
    $('#invoices-sales-chart').empty();

    Morris.Line({
        element: 'invoices-sales-chart',
        data: [
            { y: '2025-07-01', a: 3424, b: 4},{ y: '2025-06-28', a: 1059, b: 3},{ y: '2025-06-27', a: 4794, b: 1},{ y: '2025-06-26', a: 1274, b: 2},{ y: '2025-06-25', a: 222, b: 1},{ y: '2025-06-24', a: 3304, b: 1},{ y: '2025-06-23', a: 0, b: 1},{ y: '2025-06-22', a: 7555, b: 2},{ y: '2025-06-20', a: 855, b: 1},{ y: '2025-06-18', a: 23, b: 2},{ y: '2025-06-16', a: 2836, b: 2},{ y: '2025-06-15', a: 1519, b: 2},
        ],
        xkey: 'y',
        ykeys: ['a', 'b'],
        labels: ['Sales', 'Invoices'],
        hideHover: 'auto',
        resize: true,
        lineColors: ['#34cea7', '#ff6e40', '#3e8ce7'],
    });


    function drawIncomeChart(dataVisits) {

        $('#dashboard-income-chart').empty();

        Morris.Area({
            element: 'dashboard-income-chart',
            data: dataVisits,
            xkey: 'x',
            ykeys: ['y'],
            ymin: 'auto 40',
            labels: ['Amount'],
            xLabels: "day",
            hideHover: 'auto',
            yLabelFormat: function (y) {
                // Only integers
                if (y === parseInt(y, 10)) {
                    return y;
                }
                else {
                    return '';
                }
            },
            resize: true,
            lineColors: [
                '#34cea7',
            ],
            pointFillColors: [
                '#ff6e40',
            ],
            fillOpacity: 0.4,
        });


    }

    function drawExpenseChart(dataVisits2) {

        $('#dashboard-expense-chart').empty();

        Morris.Area({
            element: 'dashboard-expense-chart',
            data: dataVisits2,
            xkey: 'x',
            ykeys: ['y'],
            ymin: 'auto 0',
            labels: ['Amount'],
            xLabels: "day",
            hideHover: 'auto',
            yLabelFormat: function (y) {
                // Only integers
                if (y === parseInt(y, 10)) {
                    return y;
                }
                else {
                    return '';
                }
            },
            resize: true,
            lineColors: [
                '#ff6e40',
            ],
            pointFillColors: [
                '#34cea7',
            ]
        });


    }

    drawIncomeChart(dataVisits);

    drawExpenseChart(dataVisits2);


    $('#dashboard-sales-breakdown-chart').empty();

    Morris.Donut({
        element: 'dashboard-sales-breakdown-chart',
        data: [{label: "Income", value: 5341 },
            {label: "Expenses", value: 1263 }
        ],
        resize: true,
        colors: ['#34cea7', '#ff6e40'],
        gridTextSize: 6,
        gridTextWeight: 400
    });

    $('a[data-toggle=tab').on('shown.bs.tab', function (e) {
        window.dispatchEvent(new Event('resize'));
      
    });


</script>
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
</body>
</html>
