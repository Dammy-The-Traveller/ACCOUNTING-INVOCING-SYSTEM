<?php include __DIR__ . '/../../partials/head.php'; ?>

<?php
// Check for success message
if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notify = document.getElementById('notify');
            const message = notify?.querySelector('.message');
            const form = document.getElementById('data_form');

            if (!notify || !message) {
                console.error('Notification element or message container not found.');
                return;
            }

            // Set success message
            message.innerHTML = <?= json_encode($_SESSION['success']) ?>;
            notify.classList.add('alert-success');
            notify.classList.remove('alert-danger');
            notify.setAttribute('aria-live', 'assertive');
            notify.style.display = 'block';

            // Hide form if it exists
            if (form) {
                form.style.display = 'none';
            }

            // Auto-hide notification and show form after 9 seconds
            setTimeout(function () {
                notify.style.display = 'none';
                if (form) {
                    form.style.display = 'block';
                }
            }, 9000);
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php
// Check for error message
if (isset($_SESSION['error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notify = document.getElementById('notify');
            const message = notify?.querySelector('.message');
            const form = document.getElementById('data_form');

            if (!notify || !message) {
                console.error('Notification element or message container not found.');
                return;
            }

            // Set error message
            message.innerHTML = <?= json_encode($_SESSION['error']) ?>;
            notify.classList.add('alert-danger');
            notify.classList.remove('alert-success');
            notify.setAttribute('aria-live', 'assertive');
            notify.style.display = 'block';

            // Keep form visible for errors to allow corrections
            if (form) {
                form.style.display = 'block';
            }

            // Auto-hide error notification after 12 seconds
            setTimeout(function () {
                notify.style.display = 'none';
            }, 12000);
        });
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
<?php
$isEdit = isset($product);
?>
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
        <form method="post" id="data_form" class="form-horizontal" action="<?= $isEdit ? '/AIS/stock-update' : '/AIS/stock' ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="product_id" value="<?= $isEdit ? $product['id'] : '' ?>">
            <div class="grid_3 grid_4">
                <h5>Add New Product</h5>
                <hr>
                <input type="hidden" name="act" value="add_product">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"
                           for="product_name">Product Name</label>

                    <div class="col-sm-6">
                        <input type="text" placeholder="Product Name"
                               class="form-control margin-bottom  required" name="product_name" value="<?= $isEdit ? $product['name'] : '' ?>">
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="product_cat">Product Category</label>

                    <div class="col-sm-6">
                        <select name="product_cat" class="form-control">
                            <?php foreach ($categories as $category): ?>
                                <option value='<?= $category['id'] ?>' <?= ($isEdit && $product['category_id'] == $category['id']) ? 'selected' : ''?>><?= $category['name'] ?></option>
                            <?php endforeach; ?>                       
                        </select>


                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"
                           for="product_cat">Warehouse</label>
                    <div class="col-sm-6">
                        <select name="product_warehouse" class="form-control">
                            <?php foreach ($warehouses as $warehouse): ?>
                                <option value='<?= $warehouse['id'] ?>' <?= ($isEdit && $product['warehouse_id'] == $warehouse['id']) ? 'selected' : '' ?>><?= $warehouse['name'] ?></option>
                            <?php endforeach; ?>                       
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"
                           for="product_status">Status</label>
                    <div class="col-sm-6">
                        <select name="product_status" class="form-control">
                            <option value='active' <?= ($isEdit && $product['status'] == 'active') ? 'selected' : '' ?>>Active</option>                        
                            <option value='inactive' <?= ($isEdit && $product['status'] == 'inactive') ? 'selected' : '' ?>>Inactive</option>                        
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"
                           for="product_code">Product Code</label>
                    <div class="col-sm-6">
                        <input type="text" placeholder="Product Code"  class="form-control required" name="product_code" value="<?= $isEdit ? $product['code'] : generateProductIndex() ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 control-label" for="product_price">Product Retail Price</label>
                    <div class="col-sm-6">
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" name="product_price" class="form-control required"
                                   placeholder="0.00" aria-describedby="sizing-addon"
                                   onkeypress="return isNumber(event)" value="<?= $isEdit ? $product['price'] : '' ?>"  >
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Product Wholesale Price</label>
                    <div class="col-sm-6">
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" name="fproduct_price" class="form-control required"
                                   placeholder="0.00" aria-describedby="sizing-addon1"
                                   onkeypress="return isNumber(event)" value="<?= $isEdit ? $product['wholesale_price'] : '' ?>"    >
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Default TAX Rate</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" name="product_tax" class="form-control required"
                                   placeholder="0.00" aria-describedby="sizing-addon1"
                                   onkeypress="return isNumber(event)" value="<?= $isEdit ? $product['tax_percent'] : '' ?>"    ><span
                                    class="input-group-addon">%</span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <small>You can change Tax rate during invoice creation also</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Default Discount Rate</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" name="product_disc" class="form-control required"
                                   placeholder="0.00" aria-describedby="sizing-addon1"
                                   onkeypress="return isNumber(event)" value="<?= $isEdit ? $product['discount'] : '' ?>"><span
                                    class="input-group-addon">%</span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <small>You can change Discount rate during invoice creation also</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Stock Units</label>
                    <div class="col-sm-4">
                        <input type="number" placeholder="Total Items in stock"
                               class="form-control margin-bottom required" name="product_qty"
                               onkeypress="return isNumber(event)" value="<?= $isEdit ? $product['quantity'] : '' ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Alert Quantity</label>
                    <div class="col-sm-4">
                        <input type="number" placeholder="Low Stock Alert Quantity"
                               class="form-control margin-bottom required" name="product_qty_alert"
                               onkeypress="return isNumber(event)" value="<?= $isEdit ? $product['alert_quantity'] : '' ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-8">
                        <textarea placeholder="Description"
                               class="form-control margin-bottom" name="product_desc"
                              ><?= $isEdit ? $product['description'] : '' ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-4">
                               <button type="submit"  class="btn btn-success margin-bottom">
                                <?= $isEdit ? 'Update Product' : 'Add Product' ?>
                               </button>
                     
                    </div>
                </div>
            </div>

        </form>
    </div>
</article>

<!-- BEGIN VENDOR JS-->
<script type="text/javascript">
    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('#sdate').datepicker('setDate', '03-06-2025');
    $('.date30').datepicker('setDate', '03-06-2025');
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
