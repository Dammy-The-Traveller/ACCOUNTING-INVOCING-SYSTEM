<?php include __DIR__ . '/../../partials/head.php'; ?>
<body data-open="click" data-menu="vertical-menu" data-col="2-columns"
      class="vertical-layout vertical-menu 2-columns  fixed-navbar"><span id="hdata"
                                                                          data-df="yyyy-mm-dd"
                                                                          data-curr="$"></span>

<!-- navbar-fixed-top-->
<?php include __DIR__ . '/../../partials/navbar.php'; ?>

<!-- ////////////////////////////////////////////////////////////////////////////-->


<!-- main menu-->
<?php include __DIR__ . '/../../partials/Sidenav.php'; ?>
    <!-- /main menu content-->
    <!-- main menu footer-->
    <!-- include includes/menu-footer-->
    <!-- main menu footer-->
    <div id="rough"></div>
<!-- / main menu-->
 <article class="content">
    <div class="card card-block">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <div class="grid_3 grid_4 animated fadeInRight">
            <h5>Recurring Invoices</h5>
            <div class="row">

                <div class="col-xl-4 col-lg-6 col-xs-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <div class="media">
                                    <div class="media-body text-xs-left">
                                        <h3 class="green"><span id="dash_0"><?= $Recurring?></span></h3>
                                        <span>Recurring</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="icon-rocket green font-large-2 float-xs-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-xs-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <div class="media">
                                    <div class="media-body text-xs-left">
                                        <h3 class="red"><span id="dash_2"><?= $totalStopped?></span></h3>
                                        <span id="dash_">Stopped</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="icon-blocked red font-large-2 float-xs-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-xs-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <div class="media">
                                    <div class="media-body text-xs-left">
                                        <h3 class="cyan"><?= $totalRecurring ?></h3>
                                        <span>Total</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="icon-stats-bars22 cyan font-large-2 float-xs-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table id="rec_invoices" class="table-striped" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th class="no-sort">Payment Status</th>
                    <th class="no-sort">Settings</th>


                </tr>
                </thead>
                <tbody>
                </tbody>

                <tfoot>
                <tr>
                    <th>No</th>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Due Date</th>
                    <th>Recurring</th>
                    <th>Total</th>
                    <th class="no-sort">Status</th>
                    <th class="no-sort">Settings</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <input type="hidden" id="dashurl" value="rec_invoices/rec_stats">
</article>

<script type="text/javascript">
    $(document).ready(function () {
       $('#rec_invoices').DataTable({
            'processing': true,
            'serverSide': true,
            'stateSave': true,
            'order': [],
            'ajax': {
                'url': "/AIS/rec-ajaxlist",
                'type': 'GET'
            },
            'columnDefs': [
                {
                    'targets': [0],
                    'orderable': false,
                },
            ],
        });
    });
</script>
<script>
    $(document).ready(function () {
  $('#rec_invoices').on('click', '.delete-objects', function (e) {
    e.preventDefault();
    const id = $(this).data('object-id');

    console.log(id);
    if (confirm("Are you sure you want to delete this invoice?")) {
      $.ajax({
        url: `/AIS/rec-delete?id=${id}`,
        type: 'GET',
        success: function (response) {
          alert(response.message || "Invoice deleted successfully");
          $('#rec_invoices').DataTable().ajax.reload(null, false);
        },
        error: function (xhr) {
          alert("An error occurred while deleting.");
        }
      });
    }
  });
});


</script>
<!-- BEGIN VENDOR JS-->
<?php require __DIR__ . '/../../partials/footer.php'; ?>
</body>
</html>
