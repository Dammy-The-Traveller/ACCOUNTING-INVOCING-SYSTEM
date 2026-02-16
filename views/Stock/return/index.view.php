<?php 
$isEdit = isset($return);
$isEditingItems = isset($items);
$isEditingSupplier = isset($supplier);
$return = $return ?? [];  // Ensures $return is at least an array
?>
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
<?php if(isset($return) && $isEdit){
if($return['currency'] == 1){
      $currencySymbol = '£';
    } elseif($return['currency'] == 2){
        $currencySymbol = '€';
    } else {
        $currencySymbol = "₵";
    }  
 }
 ?>
<body data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar">
      <span id="hdata" data-df="dd-mm-yyyy" data-curr="$"></span>
<!-- navbar-fixed-top-->
<?php include __DIR__ . '/../../partials/navbar.php'; ?>
<!-- main menu-->
<?php include __DIR__ . '/../../partials/Sidenav.php'; ?>
<!-- / main menu-->
<div class="app-content content container-fluid">
   <div class="content-wrapper">
     <div class="content-header row">
       <div class="card card-block">
         <div id="notify" class="alert alert-success" style="display:none;">
           <a href="#" class="close" data-dismiss="alert">&times;</a>
           <div class="message"></div>
         </div>
         <form method="post" id="data_form" action="<?= $isEdit ? '/AIS/return-update' : '/AIS/return-store' ?>">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
           <input type="hidden" name="id" value="<?= $isEdit ? $return['id'] : '' ?>">
           <div class="row">
             <div class="col-sm-4">
             </div>
             <div class="col-sm-3"></div>
             <div class="col-sm-2"></div>
             <div class="col-sm-3">
             </div>
           </div>
           <div class="row">
             <div class="col-sm-6 cmp-pnl">
               <div id="customerpanel" class="inner-cmp-pnl">
                 <div class="form-group row">
                   <div class="fcol-sm-12">
                     <h3 class="title">
                       Bill From <a href='#' class="btn btn-primary btn-sm rounded" data-toggle="modal" data-target="#addCustomer">
                         Add Supplier </a>
                   </div>
                 </div>
                 <div class="form-group row">
                   <div class="frmSearch col-sm-12"><label for="cst" class="caption">Search Supplier </label>
                     <input type="text" class="form-control" name="cst" id="supplier-box" placeholder="Enter Supplier Name or Mobile Number to search" autocomplete="off" />
                     <div id="supplier-box-result"></div>
                   </div>
                 </div>
                 <div id="customer">
                   <div class="clientinfo">
                     Supplier Details
                     <hr>
                     <input type="hidden" name="supplier_id" id="supplier_id" value="<?= $isEdit ? ($return['supplier_id'] ?? '0') : '0'  ?>">
                     <div id="supplier_name"><?= $isEditingSupplier ? ($supplier['name'] ?? '') : '' ?></div>
                   </div>
                   <div class="clientinfo">
                    <div id="supplier_address1"><?= $isEditingSupplier ? ($supplier['address'] ?? '' ): '' ?></div>
                   </div>
                   <div class="clientinfo">
                     <div type="text" id="supplier_phone"><?= $isEditingSupplier ? ($supplier['phone'] ?? '') : '' ?></div>
                   </div>
                   <hr>Warehouse 
                   <select id="warehouses" name="warehouses" class="selectpicker form-control">
                     <option value="0">Select Warehouse</option>     
                     <?php foreach ($warehouses as $warehouse): ?>
                                <option value='<?= $warehouse['id'] ?>' id="warehouses"
                                <?= ($isEdit && $warehouse['id'] == $return['warehouse_id']) ? 'selected' : '' ?>>
                                <?= $warehouse['name'] ?></option>
                    <?php endforeach; ?>         
                   </select>
                   <hr>Categories
                   <select id="category" name="category" class="selectpicker form-control">
                     <option value="0">Select Category</option>     
                     <?php foreach ($categories as $category): ?>
                                <option value='<?= $category['id'] ?>' id="category" 
                                <?= $isEdit && $category['id'] == $return['category_id'] ? 'selected' : '' ?>
                                ><?= $category['name'] ?></option>
                    <?php endforeach; ?>         
                   </select>
                 </div>
               </div>
             </div>
             <div class="col-sm-6 cmp-pnl">
               <div class="inner-cmp-pnl">
                 <div class="form-group row">
                   <div class="col-sm-12">
                     <h3 class="title"> </h3>
                   </div>
                 </div>
                 <div class="form-group row">
                   <div class="col-sm-6"><label for="invocieno" class="caption">Invoice No</label>
                     <div class="input-group">
                       <div class="input-group-addon"><span class="icon-file-text-o" aria-hidden="true"></span></div>
                       <input type="text" class="form-control" placeholder="Invoice #" name="invocieno" value="<?= $isEdit? $return['invoice_no'] :  generateInvoiceIndex() ?>">
                     </div>
                   </div>
                   <div class="col-sm-6"><label for="invocieno" class="caption">Reference </label>
                     <div class="input-group">
                       <div class="input-group-addon"><span class="icon-bookmark-o" aria-hidden="true"></span></div>
                       <input type="text" class="form-control" placeholder="Reference #" name="refer" value="<?= $isEdit ? $return['reference'] : '' ?>">
                     </div>
                   </div>
                   
                 </div>
                 <div class="form-group row">
                   <div class="col-sm-6"><label for="invociedate" class="caption">Order Date </label>
                     <div class="input-group">
                       <div class="input-group-addon"><span class="icon-calendar4" aria-hidden="true"></span></div>
                       <input type="text" class="form-control required" placeholder="Billing Date" name="invoicedate" data-toggle="datepicker" autocomplete="false" value="<?= $isEdit ? $return['invoice_date'] : '' ?>">
                     </div>
                   </div>
                   <div class="col-sm-6"><label for="invocieduedate" class="caption">Order Due Date </label>
                     <div class="input-group">
                       <div class="input-group-addon"><span class="icon-calendar-o" aria-hidden="true"></span></div>
                       <input type="text" class="form-control required" id="tsn_due" name="invocieduedate" placeholder="Due Date" data-toggle="datepicker" autocomplete="false" value="<?= $isEdit ? $return['due_date'] : '' ?>">
                     </div>
                   </div>
                 </div>
                 <div class="form-group row">
                   <div class="col-sm-6">
                     <label for="taxformat" class="caption">Tax </label>
                     <select class="form-control" name="taxformat" onchange="changeTaxFormat(this.value)" id="taxformat">
                       <option value="on" seleted>&raquo;On</option>
                       <option value="on" <?= $isEdit && $return['taxformat'] == 'on' ? 'selected' : '' ?>>On</option>
                       <option value="off" <?= $isEdit && $return['taxformat'] == 'off' ? 'selected' : '' ?>>Off</option>
                     </select>
                   </div>
                   <div class="col-sm-6">
                     <div class="form-group">
                       <label for="discountFormat" class="caption"> Discount</label>
                       <select class="form-control" name="discountFormat" onchange="changeDiscountFormat(this.value)" id="discountFormat">
                          <option value="%" <?= $isEdit && $return['tax_format'] == '%' ? 'selected' : '' ?>> % Discount After TAX </option>
                 <option value="flat" <?= $isEdit && $return['tax_format'] == 'flat' ? 'selected' : '' ?>>Flat Discount After TAX</option>
                 <option value="b_p" <?= $isEdit && $return['tax_format'] == 'b_p' ? 'selected' : '' ?>> % Discount Before TAX</option>
                 <option value="bflat" <?= $isEdit && $return['tax_format'] == 'bflat' ? 'selected' : '' ?>>Flat Discount Before TAX</option>
                         <!-- <option value="0">Off</option> -->
                       </select>
                     </div>
                   </div>
                 </div>
                 <div class="form-group row">
                   <div class="col-sm-12">
                     <label for="toAddInfo" class="caption"> </label>
                     <textarea class="form-control" name="notes" rows="2" ><?=  $isEdit ? ($return['notes'] ?? '') : ''  ?></textarea>
                   </div>
                 </div>
               </div>
             </div>
           </div>
           <div id="saman-row">
             <table class="table-responsive tfr my_stripe">
               <thead>
                 <tr class="item_header">
                   <th width="30%" class="text-center">Item Name</th>
                   <th width="8%" class="text-center"> Quantity</th>
                   <th width="10%" class="text-center">Rate</th>
                   <th width="10%" class="text-center">Tax(%)</th>
                   <th width="10%" class="text-center">Tax</th>
                   <th width="7%" class="text-center"> Discount</th>
                   <th width="10%" class="text-center">
                     Amount ($)
                   </th>
                   <th width="5%" class="text-center"> Action</th>
                 </tr>
               </thead>
               <tbody>
                <?php if ($isEdit && !empty($items)): ?>
                <?php foreach ($items as $index => $item):?>
                 <tr>
           <td>
        <input type="text" class="form-control text-center product-search" name="product_name[]"  id="productname-<?= $isEdit ? $index : '' ?> placeholder="Enter Product name or Code" autocomplete="off" value="<?= $isEdit ? $item['product_name'] : '' ?>">
      </td>
      <td>
        <input type="text" class="form-control req amnt" name="product_qty[]" id="amount-<?= $index ?>"
               onkeyup="rowTotal('<?= $isEdit ? $index : '' ?>'), billUpyog()" value="<?= $isEdit ? $item['quantity'] : '' ?>">
      </td>
      <td>
        <input type="text" class="form-control req prc" name="product_price[]" id="price-<?= $isEdit ? $index : ''?>"
               onkeyup="rowTotal('<?= $isEdit ? $index : '' ?>'), billUpyog()" value="<?= $isEdit  ? $item['price'] : '' ?>"> 
      </td>
    <td>
  <input type="text" class="form-control vat" name="product_tax[]" id="vat-<?= $isEdit ?  $index : '' ?>"
         onkeyup="rowTotal('<?= $isEdit ?  $index : '' ?>'), billUpyog()" 
         value="<?= $isEdit ? $item['tax_percent'] : '' ?>">
</td>
<td class="text-center" id="texttaxa-<?=$isEdit ?  $index : '' ?>">
  <?= $isEdit ? $item['tax_amount'] : '' ?>
</td>
<td>
  <input type="text" class="form-control discount" name="product_discount[]" id="discount-<?=$isEdit ?  $index : '' ?>"
         onkeyup="rowTotal('<?= $isEdit ?  $index : ''?>'), billUpyog()" 
         value="<?= $isEdit ? $item['discount'] : '' ?>">
</td>
<td>
  <strong><span class="ttlText" id="result-<?= $isEdit ?  $index : '' ?>">
    <?= $isEdit ? $item['subtotal'] : '' ?>
  </span></strong>
</td>
<td></td>

<input type="hidden" name="taxa[]" id="taxa-<?=$isEdit ?  $index : '' ?>" 
       value="<?= $isEdit ? $item['tax_amount'] : '' ?>">

<input type="hidden" name="disca[]" id="disca-<?= $isEdit ?  $index : '' ?>" 
       value="<?= $isEdit ? $item['discount'] : '' ?>">

<input type="hidden" class="ttInput" name="product_subtotal[]" id="total-<?= $isEdit ?  $index : '' ?>" 
       value="<?= $isEdit ? $item['subtotal'] : '' ?>">

<input type="hidden" class="pdIn" name="pid[]" id="pid-<?= $isEdit ?  $index : ''?>" 
       value="<?= $isEdit ? $item['product_id'] : '' ?>">
                 </tr>
                 <tr>
                <td colspan="8">
        <textarea id="dpid-<?=$isEdit ?  $index : ''?>" class="form-control" name="product_description[]"><?= $isEdit ?  $item['product_description'] : ''?></textarea><br>
      </td>
    </tr>
                 <?php endforeach; ?>
                <?php else: ?>
                  
                  <tr>
           <td><input type="text" class="form-control text-center product-search" name="product_name[]" placeholder="Enter Product name or Code" id='productname-0'>
           </td>
           <td>
            <input type="text" class="form-control req amnt" name="product_qty[]" id="amount-0" onkeypress="return isNumber(event)" onkeyup="rowTotal('0'), billUpyog()" autocomplete="off" value="1">
          </td>
           <td>
            <input type="text" class="form-control req prc" name="product_price[]" id="price-0" onkeypress="return isNumber(event)" onkeyup="rowTotal('0'), billUpyog()" autocomplete="off">
          </td>
           <td>
            <input type="text" class="form-control vat " name="product_tax[]" id="vat-0" onkeypress="return isNumber(event)" onkeyup="rowTotal('0'), billUpyog()" autocomplete="off">
          </td>
           <td class="text-center" id="texttaxa-0">0</td>
           <td><input type="text" class="form-control discount" name="product_discount[]" onkeypress="return isNumber(event)" id="discount-0" onkeyup="rowTotal('0'), billUpyog()" autocomplete="off"></td>
           <td><span class="currenty">$</span>
             <strong><span class='ttlText' id="result-0">0</span></strong>
           </td>
           <td class="text-center">
           </td>
           <input type="hidden" name="taxa[]" id="taxa-0" value="0">
           <input type="hidden" name="disca[]" id="disca-0" value="0">
           <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-0" value="0">
           <input type="hidden" class="pdIn" name="pid[]" id="pid-0" value="0">
         </tr>

            <tr>
           <td colspan="8"><textarea id="dpid-0" class="form-control" name="product_description[]" placeholder="Enter Product description" autocomplete="off"></textarea><br></td>
         </tr>
              
                  <?php endif; ?> 
                 <tr class="last-item-row">
                   <td class="add-row">
                     <button type="button" class="btn btn-success" aria-label="Left Align" data-toggle="tooltip" data-placement="top" title="Add product row" id="addproduct">
                       <i class="icon-plus-square"></i> Add Row </button>
                   </td>
                   <td colspan="7"></td>
                 </tr>
                 <tr class="sub_c" style="display: table-row;">
  <td colspan="6" align="right">
    <input type="hidden" value="<?= $isEdit ? ($return['subtotal'] ?? '0') : '0' ?>" id="subttlform" name="subtotal">
    <strong> Total Tax</strong>
  </td>
  <td align="left" colspan="2">
    <span class="currenty lightMode"><?= $isEdit ? ($currencySymbol ?? '') : '' ?></span>
    <span id="taxr" class="lightMode"><?= $isEdit ? ($items[0]['tax_amount'] ?? '0') : '0' ?></span>
  </td>
</tr>

<tr class="sub_c" style="display: table-row;">
  <td colspan="6" align="right">
    <strong> Total Discount</strong>
  </td>
  <td align="left" colspan="2">
    <span class="currenty lightMode"><?= $isEdit ? ($currencySymbol ?? '') : '' ?></span>
    <span id="discs" class="lightMode"><?= $isEdit ? ($items[0]['discount'] ?? '0') : '0' ?></span>
  </td>
</tr>

<tr class="sub_c" style="display: table-row;">
  <td colspan="6" align="right"><strong> Shipping</strong></td>
  <td align="left" colspan="2">
    <input type="text" class="form-control shipVal" onkeypress="return isNumber(event)" placeholder="Value" name="shipping" autocomplete="off" onkeyup="updateTotal()" value="<?= $isEdit ? ($return['shipping'] ?? '0') : '0' ?>">
  </td>
</tr>

<tr class="sub_c" style="display: table-row;">
  <td colspan="6" align="right">
    <strong> Grand Total (<span class="currenty lightMode">$</span>)</strong>
  </td>
  <td align="left" colspan="2">
    <input type="text" name="total" class="form-control" id="invoiceyoghtml" readonly="" value="<?= $isEdit ? ($return['grand_total'] ?? '0') : '0' ?>">
  </td>
</tr>

<tr class="sub_c" style="display: table-row;">
  <td colspan="2">
    Payment Terms
    <select name="pterms" class="selectpicker form-control">
      <option value="1" <?= $isEdit ? (($return['payment_terms'] ?? '') == 1 ? 'selected' : '') : '' ?>>Due On Receipt</option>
      <option value="2" <?= $isEdit ? (($return['payment_terms'] ?? '') == 2 ? 'selected' : '') : '' ?>>Net 15</option>
      <option value="3" <?= $isEdit ? (($return['payment_terms'] ?? '') == 3 ? 'selected' : '') : '' ?>>Net 30</option>
    </select>
  </td>
  <td colspan="2">
    <div>
      <label>Update Stock</label>
      <div class="input-group">
        <label class="display-inline-block custom-control custom-radio ml-1">
          <input type="radio" name="update_stock" class="custom-control-input" value="yes" <?= $isEdit ? (($return['update_stock'] ?? '') == 'yes' ? 'checked' : '') : '' ?>>
          <span class="custom-control-indicator"></span>
          <span class="custom-control-description ml-0">Yes</span>
        </label>
        <label class="display-inline-block custom-control custom-radio">
          <input type="radio" name="update_stock" class="custom-control-input" value="no" <?= $isEdit ? (($return['update_stock'] ?? '') == 'no' ? 'checked' : '') : '' ?>>
          <span class="custom-control-indicator"></span>
          <span class="custom-control-description ml-0">No</span>
        </label>
      </div>
    </div>
  </td>
  <td align="right" colspan="4">
    <button type="submit" class="btn btn-success sub-btn" data-loading-text="<?= $isEdit ? 'Updating...' : 'Creating...' ?>">
      <?= $isEdit ? 'Update Order' : 'Generate Order' ?>
    </button>
  </td>
</tr>

             
               </tbody>
             </table>
           </div>
           <input type="hidden" value="return/action" id="action-url">
           <input type="hidden" value="puchase_search" id="billtype">
           <input type="hidden" value="0" name="counter" id="ganak">
           <input type="hidden" value="$" id="currency">
           <input type="hidden" value="yes" name="tax_handle" id="tax_status">
           <input type="hidden" value="yes" name="applyDiscount" id="discount_handle">
         </form>
       </div>
     </div>
   </div>
 </div>

 <?php require('modal.view.php'); ?>
<!-- BEGIN VENDOR JS-->
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
        document.getElementById("supplier_id").value = customer.id;
        document.getElementById("supplier_name").innerHTML =`<strong> ${customer.name}</strong>`;
        document.getElementById("supplier_address1").innerHTML =`<strong> ${customer.address}</strong>`;
        document.getElementById("supplier_phone").innerHTML =
        `<strong>PHONE:</strong> ${customer.phone}<br><strong>EMAIL:</strong> ${customer.email}`;

        document.getElementById("supplier-box").value = customer.name;
        document.getElementById("supplier-box-result").innerHTML = "";
    }
});
</script>

<script>
let rowCount = 1; 

document.getElementById("addproducts").addEventListener("click", function () {
    const table = document.querySelector(".tfr tbody");
    
    const rowId = rowCount;

    const rowHTML = `
<tr>
    <td><input type="text" class="form-control text-center" name="product_name[]" id="productname-${rowId}" placeholder="Enter Product name or Code"></td>
    <td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-${rowId}" value="1"
        onkeyup="rowTotal('${rowId}'); billUpyog();" autocomplete="off"></td>
    <td><input type="text" class="form-control req prc" name="product_price[]" id="price-${rowId}"
        onkeyup="rowTotal('${rowId}'); billUpyog();" autocomplete="off"></td>
    <td><input type="text" class="form-control vat" name="product_tax[]" id="vat-${rowId}"
        onkeyup="rowTotal('${rowId}'); billUpyog();" autocomplete="off"></td>
    <td class="text-center" id="texttaxa-${rowId}">0</td>
    <td><input type="text" class="form-control discount" name="product_discount[]" id="discount-${rowId}"
        onkeyup="rowTotal('${rowId}'); billUpyog();" autocomplete="off"></td>
    <td><span class="currenty">$</span> <strong><span class='ttlText' id="result-${rowId}">0</span></strong></td>
    <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>

    <input type="hidden" name="taxa[]" id="taxa-${rowId}" value="0">
    <input type="hidden" name="disca[]" id="disca-${rowId}" value="0">
    <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-${rowId}" value="0">
    <input type="hidden" class="pdIn" name="pid[]" id="pid-${rowId}" value="0">
</tr>
<tr><td colspan="8">
    <textarea id="dpid-${rowId}" class="form-control" name="product_description[]" placeholder="Enter Product description" autocomplete="off"></textarea><br>
</td></tr>`;

    // Append to table before .last-item-row
    const lastRow = document.querySelector(".last-item-row");
    lastRow.insertAdjacentHTML('beforebegin', rowHTML);

    rowCount++;
});

// Remove row event delegation
document.querySelector(".tfr tbody").addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-row")) {
        const row = e.target.closest("tr");
        const nextRow = row.nextElementSibling; // product description row
        row.remove();
        if (nextRow && nextRow.querySelector("textarea")) nextRow.remove();
        billUpyog(); // recalculate
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Delegate event for all current & future product name inputs
    document.querySelector(".product-search").addEventListener("input", function (e) {
        const input = e.target;
        const warehouseId = document.getElementById("warehouses").value;
        const categoryId = document.getElementById("category").value;
        // console.log(warehouseId);
        if (!input.name.startsWith("product_name")) return;

        const rowId = input.id.split("-")[1];
        let resultBox = document.getElementById(`product-box-result-${rowId}`);

        // If box doesn't exist yet, create it
        if (!resultBox) {
            resultBox = document.createElement("div");
            resultBox.id = `product-box-result-${rowId}`;
            resultBox.classList.add("bg-white", "border", "p-2", "w-100", "mt-1", "d-none");
            input.parentElement.appendChild(resultBox);
        }

        const query = input.value.trim();
        if (query.length < 2) {
            resultBox.innerHTML = "";
            return;
        }

        fetch(`/AIS/api/product_details?query=${encodeURIComponent(query)}&warehouse_id=${warehouseId}&category_id=${categoryId}`)
            .then(res => res.json())
            .then(data => {
                resultBox.innerHTML = "";
                if (data.length === 0) {
                    resultBox.innerHTML = `<p class="text-muted">No products found in this warehouse</p>`;
                    return;
                }

                data.forEach(product => {
                    const div = document.createElement("div");
                    div.classList.add("product-suggestion", "p-2", "border-bottom");
                    div.innerHTML = `<strong>${product.name}</strong> (${product.code})<br><small>$${product.price} | Tax: ${product.tax_percent}%</small>`;
                    div.addEventListener("click", () => selectProduct(product, rowId));
                    resultBox.appendChild(div);
                });
            });
    });

    function rowTotal(id) {
    const qty = parseFloat(document.getElementById(`amount-${id}`).value) || 0;
    const price = parseFloat(document.getElementById(`price-${id}`).value) || 0;
    const taxRate = parseFloat(document.getElementById(`vat-${id}`).value) || 0;
    const discountRate = parseFloat(document.getElementById(`discount-${id}`).value) || 0;

    const base = qty * price;
    const taxAmount = (taxRate / 100) * base;
    const discountAmount = (discountRate / 100) * base;
    const total = base + taxAmount - discountAmount;

    // Update DOM
    document.getElementById(`texttaxa-${id}`).innerText = taxAmount.toFixed(2);
    document.getElementById(`taxa-${id}`).value = taxAmount.toFixed(2);
    document.getElementById(`disca-${id}`).value = discountAmount.toFixed(2);
    document.getElementById(`total-${id}`).value = total.toFixed(2);
    document.getElementById(`result-${id}`).innerText = total.toFixed(2);
}

function billUpyog() {
    let subtotal = 0, totalTax = 0, totalDiscount = 0;

    const totals = document.querySelectorAll('.ttInput');
    const taxas = document.querySelectorAll('input[name="taxa[]"]');
    const discas = document.querySelectorAll('input[name="disca[]"]');

    totals.forEach(t => subtotal += parseFloat(t.value) || 0);
    taxas.forEach(t => totalTax += parseFloat(t.value) || 0);
    discas.forEach(d => totalDiscount += parseFloat(d.value) || 0);

    const shipping = parseFloat(document.querySelector('input[name="shipping"]').value) || 0;
    const grandTotal = subtotal + shipping;

    // Set values in DOM
    document.getElementById("subttlform").value = subtotal.toFixed(2);
    document.getElementById("taxr").innerText = totalTax.toFixed(2);
    document.getElementById("discs").innerText = totalDiscount.toFixed(2);
    document.getElementById("invoiceyoghtml").value = grandTotal.toFixed(2);
}

    function selectProduct(product, rowId) {
        document.getElementById(`productname-${rowId}`).value = product.name;
        document.getElementById(`price-${rowId}`).value = product.price;
        document.getElementById(`vat-${rowId}`).value = product.tax_percent;
        document.getElementById(`discount-${rowId}`).value = product.discount;
        document.getElementById(`dpid-${rowId}`).value = product.description;
        document.getElementById(`pid-${rowId}`).value = product.id;

        const resultBox = document.getElementById(`product-box-result-${rowId}`);
        if (resultBox) resultBox.remove();

        // Recalculate totals
        rowTotal(rowId);
        billUpyog();
    }
});
</script>
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
</body>
</html>
