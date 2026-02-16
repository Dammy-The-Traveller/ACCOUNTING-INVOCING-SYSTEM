<?php include __DIR__ . '/../../partials/head.php'; ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<body data-open="click" data-menu="vertical-menu" data-col="2-columns"
      class="vertical-layout vertical-menu 2-columns  fixed-navbar"><span id="hdata"
                                                                          data-df="dd-mm-yyyy"
                                                                          data-curr="$"></span>

<!-- navbar-fixed-top-->
<?php include __DIR__ . '/../../partials/navbar.php'; ?>

<!-- ////////////////////////////////////////////////////////////////////////////-->


<!-- main menu-->
<?php include __DIR__ . '/../../partials/Sidenav.php'; ?>
<!-- / main menu-->
 <article class="content">
    <div class="card card-block">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <div class="grid_3 grid_4 animated fadeInRight table-responsive">
            <h5>Quotes</h5>

            <hr>
            <table id="quotes" class="table-striped" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Quote#</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th class="no-sort">Status</th>
                    <th class="no-sort">Settings</th>


                </tr>
                </thead>
                <tbody>
                </tbody>

                <tfoot>
                <tr>
                    <th>No</th>
                    <th>Quote#</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th class="no-sort">Status</th>
                    <th class="no-sort">Settings</th>

                </tr>
                </tfoot>
            </table>
        </div>
    </div>


</article>
<!-- <div id="quote_delete_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Delete</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this quote?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="object-id" value="">
                <input type="hidden" id="action-url" value="/AIS/quotes-delete">
                <button type="button" class="btn btn-primary" id="quote-delete-confirm">Delete</button>
                <button type="button" class="btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div> -->

<script type="text/javascript">
 $(document).ready(function () {
       $('#quotes').DataTable({
            'processing': true,
            'serverSide': true,
            'stateSave': true,
            'order': [],
            'ajax': {
                'url': "/AIS/quote-ajaxlist",
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
  $('#quotes').on('click', '.delete-quote', function (e) {
    e.preventDefault();
    const id = $(this).data('object-id');

    console.log(id);
    if (confirm("Are you sure you want to delete this quote?")) {
      $.ajax({
        url: `/AIS/quotes-delete?id=${id}`,
        type: 'GET',
        success: function (response) {
          alert(response.message || "Quote deleted successfully");
          $('#quotes').DataTable().ajax.reload(null, false);
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
