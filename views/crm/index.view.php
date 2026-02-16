<?php
$isEdit = isset($customer) && !empty($customer['id']);
$isEditinggroup= isset($group);

?>
<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notify = document.getElementById('notify');
            const message = notify.querySelector('.message');
           const form = document.getElementById('data_form');
            message.innerHTML = <?= json_encode($_SESSION['success']) ?>;
            notify.style.display = 'block';
            form.style.display = 'none';
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
        <form method="post" id="data_form" class="form-horizontal" action="<?= $isEdit ? '/AIS/customer-update' : '/AIS/customer-store' ?>">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                   <input type="hidden" name="id" value="<?= $isEdit ? ($customer['id'] ?? '') : '' ?>">
                   <input type="hidden" name="cus_code" value="<?= $isEdit ? ($customer['customer_code'] ?? '') : generateCustomerIndex() ?>">
            <div class="row">

                <h5>Add New Customer</h5>
                <hr>
                <div class="col-md-6">
                    <h5>Billing Address</h5>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="name">Name</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Name"
                                   class="form-control margin-bottom  required" name="name" id="mcustomer_name" value="<?= $isEdit ? htmlspecialchars($customer['name'] ?? '') : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="name">Company</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Company"
                                   class="form-control margin-bottom " name="company" value="<?= $isEdit ? $customer['company'] : '' ?>">
                        </div>
                    </div>

                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="phone"> Phone</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="phone"
                                   class="form-control margin-bottom required" name="phone" id="mcustomer_phone" value="<?= $isEdit ? $customer['phone'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label" for="email">Email</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="email"
                                   class="form-control margin-bottom required" name="email" id="mcustomer_email" value="<?= $isEdit ? $customer['email'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="address"> Address</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="address"
                                   class="form-control margin-bottom required" name="address" id="mcustomer_address1" value="<?= $isEdit ? $customer['address'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="city">City</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="city"
                                   class="form-control margin-bottom" name="city" id="mcustomer_city" value="<?= $isEdit ? $customer['city'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="region">Region</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Region"
                                   class="form-control margin-bottom" name="region" id="region" value="<?= $isEdit ? $customer['region'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="country">Country</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Country"
                                   class="form-control margin-bottom" name="country" id="mcustomer_country" value="<?= $isEdit ? $customer['country'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="postbox">PostBox</label>

                        <div class="col-sm-6">
                            <input type="text" placeholder="PostBox"
                                   class="form-control margin-bottom" name="postbox" id="postbox" value="<?= $isEdit ? $customer['postbox'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="postbox">TAX ID</label>

                        <div class="col-sm-6">
                            <input type="text" placeholder="TAX ID"
                                   class="form-control margin-bottom" name="taxid" value="<?= $isEdit ? $customer['taxid'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="customergroup">Customer group</label>

                        <div class="col-sm-10">
                            <select name="customergroup" class="form-control">
                                <?php foreach ($groups as $group): ?>
                                <option value='<?= $group['id'] ?>' id="group"
                                <?= ($isEdit && $group['id'] == $customer['group_id']) ? 'selected' : '' ?>><?= $group['name'] ?> </option>
                                 <?php endforeach; ?>                              
                                </select>
                        </div>
                    </div>
                </div>

                <!--ship-->

                <div class="col-md-6">
                    <h5>Shipping Address</h5>
                    <div class="form-group row">

                        <div class="input-group">
                            <label class="display-inline-block custom-control custom-radio ml-1">
                               <input type="checkbox" name="customer1" class="custom-control-input" id="copy_address"
                        value="yes" <?= $isEdit && $customer['shipping_billing'] == 'yes' ? 'checked' : '' ?>>

                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description ml-0">Same As Billing</span>
                            </label>

                        </div>

                        <div class="col-sm-10">
                            Please leave Shipping Address blank if you do not want to print it on the invoice.                        
                        </div>
                    </div>

                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="name_s">Name</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Name"
                                   class="form-control margin-bottom" name="name_s" id="mcustomer_name_s" value="<?= $isEdit ? $customer['shipping_name'] : '' ?>">
                        </div>
                    </div>


                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="phone_s"> Phone</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="phone"
                                   class="form-control margin-bottom" name="phone_s" id="mcustomer_phone_s" value="<?= $isEdit ? $customer['shipping_phone'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label" for="email_s">Email</label>

                        <div class="col-sm-10">
                            <input type="email" placeholder="email"
                                   class="form-control margin-bottom" name="email_s" id="mcustomer_email_s" value="<?= $isEdit ? $customer['shipping_email'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="address"> Address</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="address_s"
                                   class="form-control margin-bottom" name="address_s"
                                   id="mcustomer_address1_s" value="<?= $isEdit ? $customer['shipping_address'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="city_s">City</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="city"
                                   class="form-control margin-bottom" name="city_s" id="mcustomer_city_s" value="<?= $isEdit ? $customer['shipping_city'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="region_s">Region</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Region"
                                   class="form-control margin-bottom" name="region_s" id="region_s" value="<?= $isEdit ? $customer['shipping_region'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="country_s">Country</label>

                        <div class="col-sm-10">
                            <input type="text" placeholder="Country"
                                   class="form-control margin-bottom" name="country_s" id="mcustomer_country_s" value="<?= $isEdit ? $customer['shipping_country'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">

                        <label class="col-sm-2 col-form-label"
                               for="postbox">PostBox</label>

                        <div class="col-sm-6">
                            <input type="text" placeholder="PostBox"
                                   class="form-control margin-bottom" name="postbox_s" id="postbox_s" value="<?= $isEdit ? $customer['shipping_postbox'] : '' ?>">
                        </div>
                    </div>


                </div>
            </div>
            <div class="form-group row">

                <label class="col-sm-2 col-form-label"></label>

                <div class="col-sm-4">
                    <button type="submit"  class="btn btn-success margin-bottom" 
                            data-loading-text="Processing...">
                        <?= $isEdit ? 'Update Customer' : 'Add Customer' ?>
                    </button>
                </div>
            </div>
    </div>
    </form>

      <?php if(isset($errors['name'])):?>
                <small style="color:red" class= "text-center"><?=$errors['name'] ?></small>
                <?php elseif(isset($errors['email'])):?>
                <small style="color:red" class= "text-center"><?=$errors['email'] ?></small>
                <?php elseif(isset($errors['phone'])):?>
                <small style="color:red" class= "text-center"><?=$errors['phone'] ?></small>
                <?php elseif(isset($errors['address'])):?>
                <p style="color:red" class= "text-center"><?=$errors['address'] ?></p>
                <?php elseif(isset($errors['shipping_name'])):?>
                <p style="color:red" class= "text-center"><?=$errors['shipping_name'] ?></p>
                <?php endif; ?>
    </div>
</article>

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
