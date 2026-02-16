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
 <article class="content">
    <div class="card card-block">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <div class="grid_3 grid_4">


            <form method="post" id="data_form" class="form-horizontal" action="<?= $isEdit ? '/AIS/account-update' : '/AIS/account-store' ?>">
 <input required type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                   <input required type="hidden" name="id" value="<?= $isEdit ? $account['id'] : '' ?>">
                <h5> 
                     <?= $isEdit ? 'Update Account' : 'Add New Account' ?>
                </h5>
                <hr>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="accno">Account No</label>

                    <div class="col-sm-6">
                        <input required type="text" placeholder="Account Number"
                               class="form-control margin-bottom required" name="accno" value="<?= $isEdit ? $account['account_number'] : '' ?>">
                    </div>
                </div>

                     <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="accno">Code Account</label>

                    <div class="col-sm-6">
                        <input required type="text" readonly placeholder="Account Code"
                               class="form-control margin-bottom required" name="acccode" value="<?= $isEdit ? $account['code'] : generateInvoiceIndex() ?>">
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="holder">Name</label>

                    <div class="col-sm-6">
                        <input required type="text" placeholder="Name"
                               class="form-control margin-bottom required" name="holder" value="<?= $isEdit ? $account['name'] : '' ?>">
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="intbal"> Intial Balance</label>

                    <div class="col-sm-6">
                        <input required type="number" placeholder="Intial Balance"
                               class="form-control margin-bottom required" name="intbal" value="<?= $isEdit ? $account['initial_balance'] : '0' ?>">
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="acode">Note</label>

                    <div class="col-sm-6">
                        <input required type="text" placeholder="Note"
                               class="form-control margin-bottom" name="acode" value="<?= $isEdit ? $account['note'] : '' ?>">
                    </div>
                </div>

            <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="acurrency">Currency</label>
                <div class="col-sm-6">
                   <select name="mcurrency" name="currency" class="selectpicker form-control" required>
                            <option value="1" <?= $isEdit ? $account['currency_code'] == 1 ? 'selected' : '' : '' ?>>£ (GBP)</option>
                            <option value="2" <?= $isEdit ? $account['currency_code'] == 2 ? 'selected' : '' : '' ?>>€ (EUR)</option>
                   </select>
                </div>
            </div>

              <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="astatus">Status</label>
                <div class="col-sm-6">
                   <select  name="status" class="selectpicker form-control" required>
                            <option value="active" <?= $isEdit ? $account['status'] == 'active' ? 'selected' : '' : '' ?>>Active</option>
                            <option value="inactive" <?= $isEdit ? $account['status'] == 'inactive' ? 'selected' : '' : '' ?>>InActive</option>
                   </select>
                </div>
            </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"></label>

                    <div class="col-sm-4">
                      <button  type="submit" class="btn btn-success margin-bottom">
                        <?= $isEdit ? 'Update' : 'Add' ?>
                      </button>
                    </div>
                </div>


            </form>

                  <?php if(isset($errors['name'])):?>
                <small style="color:red" class= "text-center"><?=$errors['name'] ?></small>
                <?php elseif(isset($errors['account'])):?>
                <small style="color:red" class= "text-center"><?=$errors['account'] ?></small>
                <?php endif; ?>
        </div>
    </div>
</article>

<!-- BEGIN VENDOR JS-->
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
