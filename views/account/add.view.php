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
     <form method="post" id="data_form" class="form-horizontal" method="/AIS/addTrans">
       <div class="grid_3 grid_4">
         <h5>Add New Transaction</h5>
         <hr>
         <div class="form-group row">
           <div class="frmSearch"><label for="cst" class="caption col-sm-2 col-form-label">Search Payer <small>(Optional)</small>
             </label>
             <div class="col-sm-6"><input type="text" class="form-control" name="cst" id="customer-box" placeholder="Enter Customer Name or Mobile Number to search" autocomplete="off" />
            
               <div id="customer-box-result"></div>
             </div>
           </div>
         </div>
         <div id="customerpanel" class="form-group row">
           <label for="toBizName" class="caption col-sm-2 col-form-label">C/o <span style="color: red;">*</span></label>
           <div class="col-sm-6">
            <input type="hidden" name="payer_id" id="customer_id" >
             <input type="text" class="form-control required" name="payer_name" id="customer_name" >
           </div>
         </div>
         <div class="form-group row">
           <label class="col-sm-2 col-form-label" for="pay_cat">Account</label>
           <div class="col-sm-6">
             <select name="pay_acc" class="form-control">
                  <?php foreach ($accounts as $account): ?>
                                <option value='<?= $account['id'] ?>' id="account" 
                                <?= $isEdit && $account['id'] == $transaction['account_id'] ? 'selected' : '' ?>
                                ><?= $account['account_number'] ?>-<?= $account['name'] ?></option>
                    <?php endforeach; ?>    
             </select>
           </div>
         </div>
         <input type="hidden" name="act" value="add_product">
         <div class="form-group row">
           <label class="col-sm-2 col-form-label" for="date">Date</label>
           <div class="col-sm-6">
             <input type="text" class="form-control required" name="date" data-toggle="datepicker" autocomplete="false">
           </div>
         </div>
         <div class="form-group row">
           <label class="col-sm-2 col-form-label" for="amount">Amount</label>
           <div class="col-sm-6">
             <input type="number" placeholder="Amount" class="form-control margin-bottom  required" name="amount">
           </div>
         </div>
         <div class="form-group row">
           <label class="col-sm-2 control-label" for="product_price">Type</label>
           <div class="col-sm-6">
             <div class="input-group">
               <select name="pay_type" class="form-control">
                 <option value="credit" selected>Income</option>
                 <option value="debit">Expense</option>
               </select>
             </div>
           </div>
         </div>
         <div class="form-group row">
           <label class="col-sm-2 col-form-label" for="pay_cat">Category</label>
           <div class="col-sm-6">
             <select name="pay_cat" class="form-control">
               <!-- <option value='Sales'>Sales</option>
               <option value='Purchase'>Purchase</option>
               <option value='Expense'>Expense</option>
               <option value='Salary'>Salary</option> -->

                     <?php foreach ($categories as $category): ?>
                                <option value='<?= $category['id'] ?>' id="category" 
                                <?= $isEdit && $category['id'] == $transaction['category_id'] ? 'selected' : '' ?>
                                ><?= $category['name'] ?></option>
                    <?php endforeach; ?>    
             </select>
           </div>
         </div>
         <div class="form-group row">
           <label class="col-sm-2 control-label" for="product_price">Method </label>
           <div class="col-sm-6">
             <div class="input-group">
               <select name="paymethod" class="form-control">
                 <option value="Cash" selected>Cash</option>
                 <option value="Card">Card</option>
                 <option value="Cheque">Cheque</option>
               </select>
             </div>
           </div>
         </div>
         <div class="form-group row">
           <label class="col-sm-2 col-form-label">Note</label>
           <div class="col-sm-6">
             <input type="text" placeholder="Note" class="form-control" name="note">
           </div>
         </div>
         <div class="form-group row">
           <label class="col-sm-2 col-form-label"></label>
           <div class="col-sm-4">
             <button type="submit" class="btn btn-success margin-bottom">Add transaction</button>
           </div>
         </div>
       </div>
     </form>
   </div>
 </article>

 <script>
document.addEventListener("DOMContentLoaded", () => {
    const customerBox = document.getElementById("customer-box");
    const resultBox = document.getElementById("customer-box-result");

    customerBox.addEventListener("keyup", function () {
        const query = this.value.trim();
        if (query.length < 2) {
            resultBox.innerHTML = "";
            return;
        }

        fetch(`/AIS/api/fetch_client_data?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                resultBox.innerHTML = "";
                if (data.length === 0) {
                    resultBox.innerHTML = "<p class='text-muted p-2'>No customers found</p>";
                    return;
                }

                data.forEach(customer => {
                    const div = document.createElement("div");
                    div.classList.add("customer-suggestion", "p-2", "border-bottom");
                    div.style.cursor = "pointer";
                    div.innerHTML = `<strong>${customer.name}</strong> - ${customer.phone}<br><small>${customer.email}</small>`;
                    div.addEventListener("click", () => selectCustomer(customer));
                    resultBox.appendChild(div);
                });
            });
    });

    function selectCustomer(customer) {
        document.getElementById("customer_id").value = customer.customer_code;
        document.getElementById("customer_name").value = customer.name; // ✅ Set value not innerHTML

        // Optional: If you have extra display fields
        if (document.getElementById("customer_address1")) {
            document.getElementById("customer_address1").textContent = customer.address || '';
        }
        if (document.getElementById("customer_phone")) {
            document.getElementById("customer_phone").innerHTML =
                `<strong>PHONE:</strong> ${customer.phone}<br><strong>EMAIL:</strong> ${customer.email}`;
        }

        document.getElementById("customer-box").value = customer.name;
        resultBox.innerHTML = "";
    }
});

</script>

<!-- BEGIN VENDOR JS-->
<script type="text/javascript">

    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('#sdate').datepicker('setDate', '13-07-2025');
    $('.date30').datepicker('setDate', '13-07-2025');


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
