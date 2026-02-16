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

<body data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar">
    <span id="hdata" data-df="dd-mm-yyyy" data-curr="$"></span>
<!-- navbar-fixed-top-->
<?php include __DIR__ . '/../partials/navbar.php'; ?>
<!-- ////////////////////////////////////////////////////////////////////////////-->


<!-- main menu-->
<?php include __DIR__ . '/../partials/Sidenav.php'; ?>
    <!-- /main menu content-->
    <!-- main menu footer-->
    <!-- include includes/menu-footer-->
    <!-- main menu footer-->
    <div id="rough"></div>
</div>
<!-- / main menu-->
 <article class="content content items-list-page">
    <div class="card card-block">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <div class="grid_3 grid_4">
          <h5 class="title">
                 <a href="/AIS/AddEmployee"
                                                               class="btn btn-primary btn-sm rounded">
                    Add new                </a>
            </h5>
            <hr>
            <div class="table-responsive">
    <table id="emptable" class="display" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>



                    </tr>
                    </thead>
                    <tbody>
                    </tbody>

                    <tfoot>
                    <tr>
                         <th>#</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</article>


<script type="text/javascript">
    $(document).ready(function () {
        $('#emptable').DataTable({
            'processing': true,
            'serverSide': true,
            'stateSave': true,
            'order': [],
            'ajax': {
                'url': "/AIS/EmployeesAjaxList",
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
<div id="delete_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Deactive Employee</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to deactive this account ? <br><strong> It will disable this account access to
                        user.</strong></p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="object-id" value="">
                <input type="hidden" id="action-url" value="employee/disable_user">
                <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete-confirm">Deactive</button>
                <button type="button" data-dismiss="modal" class="btn">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div id="pop_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Delete</h4>
            </div>

            <div class="modal-body">
                <form id="form_model">


                    <div class="modal-body">
                        <p>Are you sure you want to delete this employee? <br><strong> It may interrupt old invoices,
                                disable account is a better option.</strong></p>
                    </div>
                    <div class="modal-footer">


                        <input type="hidden" class="form-control required"
                               name="empid" id="empid" value="">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"> Close</button>
                 
                        <button type="button" class="btn btn-primary"
                                id="submit_model">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- BEGIN VENDOR JS-->



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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deactiveButton = document.getElementById('delete-confirm');
    const deleteButton = document.getElementById('submit_model');
    const notifyDiv = document.getElementById('notify');
    const messageDiv = notifyDiv.querySelector('.message');
    const notifyClose = notifyDiv.querySelector('.close');
    // const dataForm = document.getElementById('data_form');
    const deleteModal = document.getElementById('delete_model');
    const popModal = document.getElementById('pop_model');

    // Handle Deactive button click
    deactiveButton.addEventListener('click', function(event) {
        event.preventDefault();
        
        messageDiv.textContent = 'Success: This action is disabled in demo';
        notifyDiv.style.display = 'block';
        // Manually close the modal
        deleteModal.classList.remove('show');
        deleteModal.style.display = 'none';
        document.body.classList.remove('modal-open');
        document.querySelector('.modal-backdrop')?.remove();
        // // Fade out alert and show data_form
        setTimeout(function() {
            notifyDiv.style.display = 'none';
           
        }, 3000);
    });

    // Handle Delete button click
    deleteButton.addEventListener('click', function(event) {
        event.preventDefault();
        // dataForm.style.display = 'none';
        messageDiv.textContent = 'Success: This action is disabled in demo';
        notifyDiv.style.display = 'block';
        // Manually close the modal
        popModal.classList.remove('show');
        popModal.style.display = 'none';
        document.body.classList.remove('modal-open');
        document.querySelector('.modal-backdrop')?.remove();
        // Fade out alert and show data_form
        setTimeout(function() {
            notifyDiv.style.display = 'none';
         
        }, 3000);
    });

    // Handle alert close button
    notifyClose.addEventListener('click', function(event) {
        event.preventDefault();
        notifyDiv.style.display = 'none';
        dataForm.style.display = 'block';
    });
});
</script>
</body>
</html>
