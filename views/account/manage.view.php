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
      class="vertical-layout vertical-menu 2-columns  fixed-navbar"><span id="hdata" data-df="dd-mm-yyyy" data-curr="$"></span>

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
        <div class="grid_3 grid_4 animated fadeInRight">
            <h5>Accounts</h5>
            <div class="row">

                <div class="col-xl-6 col-lg-6 col-xs-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <div class="media">
                                    <div class="media-body text-xs-left">
                                        <h3 class="green"><?= $totalBalance ?? '' ?><span
                                                    id="dash_0"></span></h3>
                                        <span id="dash_">Balance</span>
                                    </div>
                                    <div class="media-right media-middle">
                                        <i class="icon-moneybag green font-large-2 float-xs-right"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-xs-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-block">
                                <div class="media">
                                    <div class="media-body text-xs-left">
                                        <h3 class="cyan" id="dash_1"><?= $accountCount ?? '' ?></h3>
                                        <span>Accounts</span>
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
            <hr>
            <div class="table-responsive">
                <table id="acctable" class="table table-hover mb-1" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Account No</th>
                        <th>Name</th>
                        <th>Balance</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>               
                    </tbody>
                    
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Account No</th>
                        <th>Name</th>
                        <th>Balance</th>
                        <th>Actions</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
   
</article>

<div id="delete_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Delete Account</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this account ? <br> <strong> It will delete all transactions in this  account also.</strong></p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="object-id" value="">
                <button type="button" data-dismiss="modal" class="btn btn-primary"
                        id="delete-confirm">Delete</button>
                <button type="button" data-dismiss="modal"
                        class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#acctable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "/AIS/account-manage-ajaxList",
                "type": "GET"
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false,
                },
            ],
						"order": [[ 2, "desc" ]]


        });
        miniDash();
    });
</script>

<script>
    // When confirming delete in modal
$('#delete-confirm').on('click', function () {
    const id = $('#object-id').val();
    if (!id) {
        alert("No account selected for deletion.");
        return;
    }

    $.ajax({
        url: `/AIS/account-manage-delete?id=${encodeURIComponent(id)}`,
        type: 'GET',
        dataType: 'json', // Ensure response is parsed as JSON
        cache: false,     // Prevent browser from caching the GET request
        success: function (response) {
            $('#delete_model').modal('hide');

            if (response.status === 'success') {
                alert(response.message || "Account deleted successfully");

                if ($.fn.DataTable.isDataTable('#acctable')) {
                    $('#acctable').DataTable().ajax.reload(null, false);
                } else {
                    $(`#row-${id}`).remove();
                }
            } else {
                alert(response.message || "Failed to delete account");
                if ($.fn.DataTable.isDataTable('#acctable')) {
                    $('#acctable').DataTable().ajax.reload(null, false);
                }
            }
        },
        error: function (xhr, status, error) {
            $('#delete_model').modal('hide');
            alert("An error occurred while deleting: " + error);
            if ($.fn.DataTable.isDataTable('#acctable')) {
                $('#acctable').DataTable().ajax.reload(null, false);
            }
        }
    });
});

</script>

<!-- BEGIN VENDOR JS-->
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
