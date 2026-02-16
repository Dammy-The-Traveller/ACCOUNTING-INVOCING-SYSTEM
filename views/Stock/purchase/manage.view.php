
<?php include __DIR__ . '/../../partials/head.php'; ?>
<body data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar">
    <span id="hdata"
                                                                          data-df="dd-mm-yyyy"
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
        <div class="grid_3 grid_4 animated fadeInRight table-responsive">
            <h5>Purchase Orders</h5>

            <hr>
            <table id="invoices" class="table-striped" cellspacing="0" width="100%">
                <thead>
                <tr> <th>No</th>
                    <th>Order #</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th class="no-sort">Settings</th>
                </tr>
                </thead>
                <tbody>
                </tbody>

                <tfoot>
                <tr> <th>No</th>
                    <th>Order #</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th class="no-sort">Settings</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>


</article>
<!-- <div id="delete_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Delete Order</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this order?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="object-id" value="">
                <input type="hidden" id="action-url" value="purchase/delete_i">
                <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete-confirm">Delete</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div> -->
<script type="text/javascript">
    $(document).ready(function () {

        var table = $('#invoices').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": "/AIS/purchase-ajaxlist",
                "type": "GET"
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false,
                },
            ],

        });

    });
</script>
<script>
$(document).ready(function () {
  $('#invoices').on('click', '.delete-objects', function (e) {
    e.preventDefault();
    const id = $(this).data('object-id');

    if (confirm("Are you sure you want to delete this purchase?")) {
      $.ajax({
        url: `/AIS/purchase-delete?id=${id}`,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
          if (response.status === 'success') {
            alert(response.message);
            $('#invoices').DataTable().ajax.reload(null, false);
          } else {
            alert("Error: " + response.message);
          }
        },
        error: function (xhr) {
          alert("An error occurred while deleting: " + xhr.responseText);
        }
      });
    }
  });
});
</script>


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
