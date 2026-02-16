<?php include __DIR__ . '/../../../partials/head.php'; ?>
<?php $warehouse_id = $_GET['id']; ?>
<body data-open="click" data-menu="vertical-menu" data-col="2-columns"
      class="vertical-layout vertical-menu 2-columns  fixed-navbar"><span id="hdata"
                                                                          data-df="dd-mm-yyyy"
                                                                          data-curr="$"></span>

<!-- navbar-fixed-top-->
<?php include __DIR__ . '/../../../partials/navbar.php'; ?>

<!-- ////////////////////////////////////////////////////////////////////////////-->


<!-- main menu-->
<?php include __DIR__ . '/../../../partials/Sidenav.php'; ?>
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
        <div class="grid_3 grid_4 table-responsive animated fadeInRight">
            <h5>Products</h5>

            <hr>
            <table id="productstable" class="display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Stock</th>
                    <th>Code</th>
                    <th>Warehouse</th>
                    <th> Price</th>
                    <th>Status</th>
                    <th>Settings</th>


                </tr>
                </thead>
                <tbody>
                </tbody>

                <tfoot>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Stock</th>
                    <th>Code</th>
                    <th>Warehouse</th>
                    <th> Price</th>
                    <th>Status</th>
                    <th>Settings</th>

                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</article>

<script type="text/javascript">
    $(document).ready(function () {
       $('#productstable').DataTable({
            'processing': true,
            'serverSide': true,
            'stateSave': true,
            'order': [],
            'ajax': {
                'url': "/AIS/warehouse-product-list-ajax?id=<?= $warehouse_id ?>",
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
//   $('#productstable').on('click', '.delete-object', function (e) {
//     e.preventDefault();
//     const id = $(this).data('object-id');

//     console.log(id);
//     if (confirm("Are you sure you want to delete this warehouse?")) {
//       $.ajax({
//         url: `/AIS/stock-warehouses-delete?id=${id}`,
//         type: 'GET',
//         success: function (response) {
//           alert(response.message || "Warehouse deleted successfully");
//           $('#productstable').DataTable().ajax.reload(null, false);
//         },
//         error: function (xhr) {
//           alert("An error occurred while deleting.");
//         }
//       });
//     }
//   });


$('#productstable').on('click', '.delete-object', function (e) {
    e.preventDefault();
    const id = $(this).data('object-id');

    if (confirm('Are you sure you want to delete this warehouse?')) {
        $.get('/AIS/stock-warehouses-delete', { id: id }, function(response) {
            if (response.status === 'success') {
                alert(response.message);
                location.reload();
            } else {
                alert(response.message);
            }
        }, 'json');
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
