<?php include __DIR__ . '/../../../partials/head.php';
$isEdit = isset($warehouses);?>

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
        <form method="post" id="data_form" class="form-horizontal" action="/AIS/stock-transfer-store">
            <div class="grid_3 grid_4">
                <h5>Stock Transfer</h5>
                <hr>
                <input type="hidden" name="act" value="add_product">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"
                           for="product_cat">Transfer From</label>
                    <div class="col-sm-6">
                        <select id="wfrom" name="from_warehouse" class="form-control">
                        <?php foreach ($warehouses as $warehouse):
                             ?>
                                <option value='<?= $warehouse['id'] ?>' ><?= $warehouse['name'] ?></option>
                            <?php endforeach; ?>                     
                        </select>
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="pay_cat">Products</label>

                    <div class="col-sm-8">
                        <select id="product_id" name="products_l[]" class="form-control required select-box" multiple="multiple"> 
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Quantity</label>
                    <div class="col-sm-6">
               <input type="number" name="quantity" class="form-control" required min="1">
               </div>
                 </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"
                           for="product_cat">Warehouse</label>
                    <div class="col-sm-6">
                        <select name="to_warehouse" class="form-control">
                            <?php foreach ($warehouses as $warehouse):
                             ?>
                                <option value='<?= $warehouse['id'] ?>' ><?= $warehouse['name'] ?></option>
                            <?php endforeach; ?>                       
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-4">
                     
                       <button type="submit" class="btn btn-success margin-bottom">Stock Transfer</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</article>


<script type="text/javascript">
    // $("#products_l").select2();
    // $("#wfrom").on('change', function(){
    // var tips=$('#wfrom').val();
    // $("#products_l").select2({

    //     tags: [],
    //     ajax: {
    //         url: '/AIS/stock-transfer-products?wid='+tips,
    //         dataType: 'json',
    //         type: 'GET',
    //         quietMillis: 50,
    //         data: function (customer) {
    //             return {
    //                 customer: customer
    //             };
    //         },
    //         processResults: function (data) {
    //             return {
    //                 results: $.map(data, function (item) {
    //                     return {
    //                         text: item.product_name,
    //                         id: item.pid
    //                     }
    //                 })
    //             };
    //         },
    //     }
    // }); });

    $('#product_id').select2({
    placeholder: 'Search product...',
    minimumInputLength: 1,
    ajax: {
        url: '/AIS/stock-transfer-products',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term, // search term
                warehouse_id: $('#wfrom').val() // pass warehouse ID
            };
        },
        processResults: function (data) {
            return {
                results: data.map(function(item) {
                    return {
                        id: item.id,
                        text: item.name
                    };
                })
            };
        },
        cache: true
    }
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
