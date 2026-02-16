<?php include __DIR__ . '/../partials/head.php'; ?>
<body data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar">
    <span id="hdata" data-df="dd-mm-yyyy" data-curr="$"></span>

<!-- navbar-fixed-top-->
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<!-- navbar-fixed-top end-->


<!-- SideNav STart -->
<?php include __DIR__ . '/../partials/Sidenav.php'; ?>
<!-- / SideNav end -->
 
<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="">
                <div class=" animated fadeInRight">
                    <div class="col-md-4">
                        <div class="card card-block">

                            <div>
                                <div class="ibox-content no-padding border-left-right">
                                    <img loading="lazy" alt="image" class="img-responsive"
                                         src="Public/assets/img/logo/example.png">
                                </div>
                                <hr>
                                <div class="ibox-content profile-content">
                                    <h4><strong><?= htmlspecialchars($user['firstname']) ?> <?= htmlspecialchars($user['lastname']) ?></strong></h4>
                                    <p><i class="icon-map-marker"></i> DAMMY TECH</p>

                                    <div class="row m-t-lg">
                                        <div class="col-md-12">
                                            <strong> Address: </strong>
                                        </div>

                                    </div>
                                    <div class="row m-t-lg">
                                        <div class="col-md-12">
                                            <strong>City                                                : </strong>K3 Dove Street                                </div>

                                    </div>
                                    <div class="row m-t-lg">
                                        <div class="col-md-12">
                                            <strong>Region                                                : </strong>  Accra                                      </div>

                                    </div>
                                    <div class="row m-t-lg">
                                        <div class="col-md-12">
                                            <strong>Country                                                : </strong>GHANA                                        </div>

                                    </div>
                                    <div class="row m-t-lg">
                                        <div class="col-md-12">
                                            <strong>PostBox                                                : </strong>123456                                        </div>

                                    </div>
                                    <hr>
                                    <div class="row m-t-lg">
                                        <div class="col-md-12">
                                            <strong> Phone:</strong> 0205139225                                </div>

                                    </div>
                                    <div class="row m-t-lg">
                                        <div class="col-md-12">
                                            <strong>Email: </strong><?= htmlspecialchars($user['email']) ?>             </div>

                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card card-block">
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="hero-widget well well-sm">
                                            <div class="icon">
                                                <i class="icon-file-text-o"></i>
                                            </div>
                                            <div class="text">

                                                <label class="text-muted">Invoices</label>
                                            </div>
                                            <div class="options">
                                                <a href="/AIS/manage"
                                                   class="btn btn-primary btn-lg"><i class="fa fa-eye"></i> View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="hero-widget well well-sm">
                                            <div class="icon">
                                                <i class="icon-book"></i>
                                            </div>
                                            <div class="text">

                                                <label class="text-muted">Transactions</label>
                                            </div>
                                            <div class="options">
                                                <a href="/AIS/transactions"
                                                   class="btn btn-primary btn-lg"><i
                                                            class="fa fa-eye"></i> View                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-sm-6">
                                        <div class="hero-widget well well-sm">
                                            <div class="icon">
                                                <i class="icon-user"></i>
                                            </div>
                                            <div class="text">

                                                <label class="text-muted">Account</label>
                                            </div>
                                            <div class="options">
                                                <a href="/AIS/profile-edit"
                                                   class="btn btn-primary btn-lg"><i class="icon-pencil"></i> Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="hero-widget well well-sm">
                                            <div class="icon">
                                                <i class="icon-key"></i>
                                            </div>
                                            <div class="text">
                                                <label class="text-muted">Password</label>
                                            </div>
                                            <div class="options">
                                                <a href="/AIS/passwordUpdate"
                                                   class="btn btn-primary btn-lg"><i
                                                            class="icon-edit"></i>Change                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">

                                    <div class="col-sm-12">
                                        <div class="hero-widget well well-sm">


                                            <p class="text-muted">Your Signature</p>

                                            <img loading="lazy" alt="image" class="img-responsive"
                                                 src="Public/assets/img/sign.png">
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BEGIN VENDOR JS-->
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
