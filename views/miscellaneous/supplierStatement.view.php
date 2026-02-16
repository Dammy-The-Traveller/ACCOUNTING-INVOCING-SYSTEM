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
            <h6>Supplier Account Statement</h6>
            <hr>

            <div class="row sameheight-container">
                <div class="col-md-6">
                    <div class="card card-block sameheight-item">

                        <form action="/AIS/SupplierStatement" method="post" role="form">

                    <div class="form-group row">
                   <div class="frmSearch">
                    <label for="cst" class="col-sm-3 col-form-label">Search Supplier </label>
                    <div class="col-sm-6">
                         <input type="text" class="form-control" name="cst" id="supplier-box" placeholder="Enter Supplier Name or Mobile Number to search" autocomplete="off" />
                            <div id="supplier-box-result"></div>
                    </div>
                   </div>
                 </div>
         <div id="customerpanel" class="form-group row">
           <label for="toBizName" class="col-sm-3 col-form-label">C/o <span style="color: red;">*</span></label>
           <div class="col-sm-6">
            <input type="hidden" name="payer_id" id="supplier_id" >
             <input type="text" class="form-control required" name="payer_name" id="supplier_name" >
           </div>
         </div>
                            <!-- <div class="form-group row">
                                <label class="col-sm-3 col-form-label"
                                       for="pay_cat">Customer</label>

                                <div class="col-sm-9">
                                    <select name="customer" class="form-control" id="customer_statement">
                                    </select>
                                </div>

                            </div> -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label"
                                       for="pay_cat">Type</label>

                                <div class="col-sm-9">
                                    <select name="trans_type" class="form-control">
                                        <option value='All'>All Transactions</option>
                                        <option value='debit'>Debit</option>
                                        <option value='credit'>Credit</option>
                                    </select>


                                </div>
                            </div>
                            <div class="form-group row">

                                <label class="col-sm-3 control-label"
                                       for="sdate">From Date</label>

                                <div class="col-sm-4">
                                    <input type="text" class="form-control required"
                                           placeholder="Start Date" name="sdate" id="sdate"
                                           data-toggle="datepicker" autocomplete="false">
                                </div>
                            </div>
                            <div class="form-group row">

                                <label class="col-sm-3 control-label"
                                       for="edate">To Date</label>

                                <div class="col-sm-4">
                                    <input type="text" class="form-control required"
                                           placeholder="End Date" name="edate"
                                           data-toggle="datepicker" autocomplete="false">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="pay_cat"></label>

                                <div class="col-sm-4">
                                  
                                <button type="submit" class="btn btn-primary btn-md">
                                    View
                                </button>

                                </div>
                            </div>
							<div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="pay_cat"></label>

                                <div class="col-sm-4">
                                   Print & Export Statement is available in BACKUP & EXPORT-IMPORT section.  


                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
</article>


<script>
  document.addEventListener("DOMContentLoaded", () => {
    const customerBox = document.getElementById("supplier-box");
    const resultBox = document.getElementById("supplier-box-result");
   
    customerBox.addEventListener("keyup", function () {
        const query = this.value.trim();
        if (query.length < 2) {
            resultBox.innerHTML = "";
            return;
        }

        fetch(`/AIS/api/fetch_supplier_data?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                resultBox.innerHTML = "";
                if (data.length === 0) {
                    resultBox.innerHTML = "<p class='text-muted'>No suppliers found</p>";
                    return;
                }

                data.forEach(customer => {
                    const div = document.createElement("div");
                    div.classList.add("supplier-suggestion", "p-2", "border-bottom", "cursor-pointer");
                    div.innerHTML = `<strong>${customer.name}</strong> - ${customer.phone}<br><small>${customer.email}</small>`;
              div.addEventListener("click", () => selectCustomer(customer));
                    resultBox.appendChild(div);
                });
            });
    });

   function selectCustomer(customer) {
        document.getElementById("supplier_id").value = customer.supplier_code;
        document.getElementById("supplier_name").value = customer.name;  
         document.getElementById("supplier-box").value = customer.name;
        resultBox.innerHTML = "";
    }
});
</script>
<!-- BEGIN VENDOR JS-->
<script type="text/javascript">

    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('#sdate').datepicker('setDate', '14-07-2025');
    $('.date30').datepicker('setDate', '14-07-2025');


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
