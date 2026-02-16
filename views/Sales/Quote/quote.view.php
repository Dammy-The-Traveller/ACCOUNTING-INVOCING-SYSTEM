
<?php
// Initialize variables with fallbacks
$isEdit = isset($quotes) && is_array($quotes) && !empty($quotes);
$isEditingCustomer = isset($customer) && is_array($customer) && !empty($customer);
$isEditingItems = isset($quoteItems) && is_array($quoteItems) && !empty($quoteItems);
$isEditingProducts = $isEditingItems; // Same as quoteItems
$quotes = $isEdit ? $quotes : [];
$customer = $isEditingCustomer ? $customer : ['name' => '', 'address' => '', 'phone' => '', 'email' => ''];
$quoteItems = $isEditingItems ? $quoteItems : [];
$warehouses = isset($warehouses) && is_array($warehouses) ? $warehouses : [];
$currencySymbol = '$'; // Default currency symbol

// Set currency symbol
if ($isEdit && !empty($quotes['currency'])) {
    switch ($quotes['currency']) {
        case 1:
            $currencySymbol = '£';
            break;
        case 2:
            $currencySymbol = '€';
            break;
        default:
            $currencySymbol = '$';
    }
}

// Fallback for generateInvoiceIndex if undefined
if (!function_exists('generateInvoiceIndex')) {
    function generateInvoiceIndex() {
        return 'QT-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}
?>

<?php include __DIR__ . '/../../partials/head.php'; ?>
<?php if (!empty($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notify = document.getElementById('notify');
            const message = notify.querySelector('.message');
            const form = document.getElementById('data_form');
            message.innerHTML = <?= json_encode($_SESSION['success']) ?>;
            notify.style.display = 'block';
            form.style.display = 'none';
            setTimeout(function() {
                notify.style.display = 'none';
                form.style.display = 'block';
            }, 3000);
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<body data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns fixed-navbar">
    <span id="hdata" data-df="yyyy-mm-dd" data-curr="<?= htmlspecialchars($currencySymbol) ?>"></span>

    <!-- Navbar -->
    <?php include __DIR__ . '/../../partials/navbar.php'; ?>

    <!-- Sidenav -->
    <?php include __DIR__ . '/../../partials/Sidenav.php'; ?>

    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="card card-block">
                    <div id="notify" class="alert alert-success" style="display:none;">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <div class="message"></div>
                    </div>
                    <form method="post" id="data_form" action="<?= $isEdit ? '/AIS/quote-update' : '/AIS/quote-store' ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="id" value="<?= $isEdit ? htmlspecialchars($quotes['id'] ?? 0) : '' ?>">
                        <div class="row">
                            <div class="col-sm-6 cmp-pnl">
                                <div id="customerpanel" class="inner-cmp-pnl">
                                    <div class="form-group row">
                                        <div class="fcol-sm-12">
                                            <h3 class="title">
                                                Bill To <a href='#' class="btn btn-primary btn-sm rounded" data-toggle="modal" data-target="#addCustomer">
                                                    Add Client
                                                </a>
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="frmSearch col-sm-12">
                                            <label for="cst" class="caption">Search Client</label>
                                            <input type="text" class="form-control" name="cst" id="customer-box" placeholder="Enter Customer Name or Mobile Number to search" autocomplete="off" />
                                            <div id="customer-box-result"></div>
                                        </div>
                                    </div>
                                    <div id="customer">
                                        <div class="clientinfo">
                                            Client Details
                                            <hr>
                                            <input type="hidden" name="customer_id" id="customer_id" value="<?= $isEdit ? htmlspecialchars($quotes['customer_id'] ?? 0) : '' ?>">
                                            <div id="customer_name"><?= $isEditingCustomer ? htmlspecialchars($customer['name']) : '' ?></div>
                                        </div>
                                        <div class="clientinfo">
                                            <div id="customer_address1"><?= $isEditingCustomer ? htmlspecialchars($customer['address']) : '' ?></div>
                                        </div>
                                        <div class="clientinfo">
                                            <div id="customer_phone"><?= $isEditingCustomer ? htmlspecialchars($customer['phone']) : '' ?></div>
                                        </div>
                                        <hr>
                                        <div id="customer_pass"></div>
                                        <label for="warehouses">Warehouse</label>
                                        <select id="warehouses" class="selectpicker form-control">
                                            <?php if (empty($warehouses)): ?>
                                                <option value="0">No warehouses available</option>
                                            <?php else: ?>
                                                <?php foreach ($warehouses as $warehouse): ?>
                                                    <option value="<?= htmlspecialchars($warehouse['id'] ?? 0) ?>"><?= htmlspecialchars($warehouse['name'] ?? '') ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 cmp-pnl">
                                <div class="inner-cmp-pnl">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <h3 class="title">Quote Properties</h3>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label for="invocieno" class="caption">Quote Number</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-file-text-o" aria-hidden="true"></span></div>
                                                <input type="text" class="form-control" placeholder="Quote #" name="invocieno" value="<?= $isEdit ? htmlspecialchars($quotes['quote_number'] ?? '') : generateInvoiceIndex() ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="refer" class="caption">Reference</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-bookmark-o" aria-hidden="true"></span></div>
                                                <input type="text" class="form-control" placeholder="Reference #" name="refer" value="<?= $isEdit ? htmlspecialchars($quotes['reference'] ?? '') : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label for="invociedate" class="caption">Quote Date</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-calendar4" aria-hidden="true"></span></div>
                                                <input type="text" class="form-control required" placeholder="Billing Date" name="invoicedate" data-toggle="datepicker" autocomplete="false" value="<?= $isEdit ? htmlspecialchars($quotes['quote_date'] ?? '') : date('Y-m-d') ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="invocieduedate" class="caption">Quote Validity</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-calendar-o" aria-hidden="true"></span></div>
                                                <input type="text" class="form-control required" id="tsn_due" name="invocieduedate" placeholder="Due Date" data-toggle="datepicker" autocomplete="false" value="<?= $isEdit ? htmlspecialchars($quotes['due_date'] ?? '') : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label for="taxformat" class="caption">Tax</label>
                                            <select class="form-control" name="taxformat" onchange="changeTaxFormat(this.value)" id="taxformat">
                                                <option value="on" <?= $isEdit && ($quotes['tax_format'] ?? '') == 'on' ? 'selected' : 'selected' ?>>On</option>
                                                <option value="off" <?= $isEdit && ($quotes['tax_format'] ?? '') == 'off' ? 'selected' : '' ?>>Off</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="discountFormat" class="caption">Discount</label>
                                            <select class="form-control" onchange="changeDiscountFormat(this.value)" id="discountFormat">
                                                <option value="%" <?= $isEdit && ($quotes['discount_format'] ?? '') == '%' ? 'selected' : 'selected' ?>>% Discount After TAX</option>
                                                <option value="flat" <?= $isEdit && ($quotes['discount_format'] ?? '') == 'flat' ? 'selected' : '' ?>>Flat Discount After TAX</option>
                                                <option value="b_p" <?= $isEdit && ($quotes['discount_format'] ?? '') == 'b_p' ? 'selected' : '' ?>>% Discount Before TAX</option>
                                                <option value="bflat" <?= $isEdit && ($quotes['discount_format'] ?? '') == 'bflat' ? 'selected' : '' ?>>Flat Discount Before TAX</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label for="toAddInfo" class="caption">Quote Note</label>
                                            <textarea class="form-control" name="notes" rows="2"><?= $isEdit ? htmlspecialchars($quotes['notes'] ?? '') : '' ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="toAddInfo" class="caption">Proposal Message</label>
                                <textarea class="summernote" name="propos" id="contents" rows="2"><?= $isEdit ? htmlspecialchars($quotes['proposal'] ?? '') : '' ?></textarea>
                            </div>
                        </div>
                        <div id="saman-row">
                            <table class="table-responsive tfr my_stripe">
                                <thead>
                                    <tr class="item_header">
                                        <th width="30%" class="text-center">Item Name</th>
                                        <th width="8%" class="text-center">Quantity</th>
                                        <th width="10%" class="text-center">Rate</th>
                                        <th width="10%" class="text-center">Tax(%)</th>
                                        <th width="10%" class="text-center">Tax</th>
                                        <th width="7%" class="text-center">Discount</th>
                                        <th width="10%" class="text-center">Amount (<?= htmlspecialchars($currencySymbol) ?>)</th>
                                        <th width="5%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($isEdit && !empty($quoteItems)): ?>
                                        <?php foreach ($quoteItems as $index => $item): ?>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control text-center" name="product_name[]" value="<?= htmlspecialchars($item['product_name'] ?? '') ?>" id="productname-<?= $index ?>">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control req amnt" name="product_qty[]" id="amount-<?= $index ?>" onkeypress="return isNumber(event)" onkeyup="rowTotal('<?= $index ?>'); billUpyog();" value="<?= htmlspecialchars($item['quantity'] ?? 1) ?>">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control req prc" name="product_price[]" id="price-<?= $index ?>" onkeypress="return isNumber(event)" onkeyup="rowTotal('<?= $index ?>'); billUpyog();" value="<?= htmlspecialchars($item['rate'] ?? 0) ?>">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control vat" name="product_tax[]" id="vat-<?= $index ?>" onkeypress="return isNumber(event)" onkeyup="rowTotal('<?= $index ?>'); billUpyog();" value="<?= htmlspecialchars($item['tax_percent'] ?? 0) ?>">
                                                </td>
                                                <td class="text-center" id="texttaxa-<?= $index ?>"><?= number_format(floatval($item['tax_amount'] ?? 0), 2) ?></td>
                                                <td>
                                                    <input type="text" class="form-control discount" name="product_discount[]" id="discount-<?= $index ?>" onkeypress="return isNumber(event)" onkeyup="rowTotal('<?= $index ?>'); billUpyog();" value="<?= htmlspecialchars($item['discount'] ?? 0) ?>">
                                                </td>
                                                <td>
                                                    <span class="currenty"><?= htmlspecialchars($currencySymbol) ?></span>
                                                    <strong><span class="ttlText" id="result-<?= $index ?>"><?= number_format(floatval($item['subtotal'] ?? 0), 2) ?></span></strong>
                                                </td>
                                             
                                                <input type="hidden" name="taxa[]" id="taxa-<?= $index ?>" value="<?= htmlspecialchars($item['tax_amount'] ?? 0) ?>">
                                                <input type="hidden" name="disca[]" id="disca-<?= $index ?>" value="<?= htmlspecialchars($item['discount'] ?? 0) ?>">
                                                <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-<?= $index ?>" value="<?= htmlspecialchars($item['subtotal'] ?? 0) ?>">
                                                <input type="hidden" class="pdIn" name="pid[]" id="pid-<?= $index ?>" value="<?= htmlspecialchars($item['id'] ?? 0) ?>">
                                            </tr>
                                            <tr>
                                                <td colspan="8">
                                                    <textarea id="dpid-<?= $index ?>" class="form-control" name="product_description[]"><?= htmlspecialchars($item['product_description'] ?? '') ?></textarea><br>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td>
                                                <input required type="text" class="form-control text-center" name="product_name[]" placeholder="Enter Product name or Code" id="productname-0" aria-label="Product Name">
                                            </td>
                                            <td>
                                                <input required type="text" class="form-control req amnt" name="product_qty[]" id="amount-0" onkeypress="return isNumber(event)" onkeyup="rowTotal('0'); billUpyog();" autocomplete="off" value="1" aria-label="Quantity">
                                            </td>
                                            <td>
                                                <input required type="text" class="form-control req prc" name="product_price[]" id="price-0" onkeypress="return isNumber(event)" onkeyup="rowTotal('0'); billUpyog();" autocomplete="off" aria-label="Price">
                                            </td>
                                            <td>
                                                <input required type="text" class="form-control vat" name="product_tax[]" id="vat-0" onkeypress="return isNumber(event)" onkeyup="rowTotal('0'); billUpyog();" autocomplete="off" aria-label="Tax Percent">
                                            </td>
                                            <td class="text-center" id="texttaxa-0">0.00</td>
                                            <td>
                                                <input required type="text" class="form-control discount" name="product_discount[]" id="discount-0" onkeypress="return isNumber(event)" onkeyup="rowTotal('0'); billUpyog();" autocomplete="off" aria-label="Discount">
                                            </td>
                                            <td>
                                                <span class="currenty"><?= htmlspecialchars($currencySymbol) ?></span>
                                                <strong><span class="ttlText" id="result-0">0.00</span></strong>
                                            </td>
                                    
                                            <input type="hidden" name="taxa[]" id="taxa-0" value="0">
                                            <input type="hidden" name="disca[]" id="disca-0" value="0">
                                            <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-0" value="0">
                                            <input type="hidden" class="pdIn" name="pid[]" id="pid-0" value="0">
                                        </tr>
                                        <tr>
                                            <td colspan="8">
                                                <textarea id="dpid-0" class="form-control" name="product_description[]" placeholder="Enter Product description" autocomplete="off"></textarea><br>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="last-item-row sub_c">
                                        <td class="add-row">
                                            <button type="button" class="btn btn-success" aria-label="Add Row" data-toggle="tooltip" data-placement="top" title="Add product row" id="addproduct">
                                                <i class="icon-plus-square"></i> Add Row
                                            </button>
                                        </td>
                                        <td colspan="7"></td>
                                    </tr>
                                    <tr class="sub_c" style="display: table-row;">
                                        <td colspan="6" align="right">
                                            <input type="hidden" value="<?= $isEdit ? number_format($quotes['subtotal'] ?? 0, 2) : '0.00' ?>" id="subttlform" name="subtotal">
                                            <strong>Total Tax</strong>
                                        </td>
                                        <td align="left" colspan="2">
                                            <span class="currenty lightMode"><?= htmlspecialchars($currencySymbol) ?></span>
                                            <span id="taxr" class="lightMode"><?= $isEdit ? number_format($quotes['total_tax'] ?? 0, 2) : '0.00' ?></span>
                                        </td>
                                    </tr>
                                    <tr class="sub_c" style="display: table-row;">
                                        <td colspan="6" align="right">
                                            <strong>Total Discount</strong>
                                        </td>
                                        <td align="left" colspan="2">
                                            <span class="currenty lightMode"><?= htmlspecialchars($currencySymbol) ?></span>
                                            <span id="discs" class="lightMode"><?= $isEdit ? number_format($quotes['total_discount'] ?? 0, 2) : '0.00' ?></span>
                                        </td>
                                    </tr>
                                    <tr class="sub_c" style="display: table-row;">
                                        <td colspan="6" align="right">
                                            <strong>Shipping</strong>
                                        </td>
                                        <td align="left" colspan="2">
                                            <input type="text" class="form-control shipVal" onkeypress="return isNumber(event)" placeholder="Value" name="shipping" autocomplete="off" onkeyup="updateTotal()" value="<?= $isEdit ? number_format($quotes['shipping'] ?? 0, 2) : '0.00' ?>">
                                        </td>
                                    </tr>
                                    <tr class="sub_c" style="display: table-row;">
                                        <td colspan="2">
                                            Payment Currency for your client <small>based on live market</small>
                                            <select name="mcurrency" class="selectpicker form-control" onchange="updateCurrency(this.value)">
                                                <option value="0" <?= $isEdit && ($quotes['currency'] ?? 0) == 0 ? 'selected' : '' ?>>Default ($)</option>
                                                <option value="1" <?= $isEdit && ($quotes['currency'] ?? 0) == 1 ? 'selected' : '' ?>>£ (GBP)</option>
                                                <option value="2" <?= $isEdit && ($quotes['currency'] ?? 0) == 2 ? 'selected' : '' ?>>€ (EUR)</option>
                                            </select>
                                        </td>
                                        <td colspan="4" align="right">
                                            <strong>Grand Total (<span class="currenty lightMode"><?= htmlspecialchars($currencySymbol) ?></span>)</strong>
                                        </td>
                                        <td align="left" colspan="2">
                                            <input type="text" name="total" class="form-control" id="invoiceyoghtml" readonly value="<?= $isEdit ? number_format($quotes['grand_total'] ?? 0, 2) : '0.00' ?>">
                                        </td>
                                    </tr>
                                    <tr class="sub_c" style="display: table-row;">
                                        <td colspan="2">
                                            Payment Terms
                                            <select name="pterms" class="selectpicker form-control">
                                                <option value="1" <?= $isEdit && ($quotes['payment_terms'] ?? '') == 1 ? 'selected' : 'selected' ?>>Due On Receipt</option>
                                                <option value="2" <?= $isEdit && ($quotes['payment_terms'] ?? '') == 2 ? 'selected' : '' ?>>Net 15</option>
                                                <option value="3" <?= $isEdit && ($quotes['payment_terms'] ?? '') == 3 ? 'selected' : '' ?>>Net 30</option>
                                            </select>
                                        </td>
                                        <td align="right" colspan="6">
                                            <button type="submit" class="btn btn-success sub-btn" data-loading-text="<?= $isEdit ? 'Updating...' : 'Creating...' ?>">
                                                <?= $isEdit ? 'Update Quote' : 'Generate Quote' ?>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" value="quote/action" id="action-url">
                        <input type="hidden" value="search" id="billtype">
                        <input type="hidden" value="<?= $isEdit ? count($quoteItems) : 1 ?>" name="counter" id="ganak">
                        <input type="hidden" value="<?= htmlspecialchars($currencySymbol) ?>" name="currency" id="currency">
                        <input type="hidden" value="<?= $isEdit ? ($quotes['tax_format'] ?? 'off') : 'off' ?>" name="taxformat" id="tax_format">
                        <input type="hidden" value="<?= $isEdit ? ($quotes['discount_format'] ?? '%') : '%' ?>" name="discountFormat" id="discount_format">
                        <input type="hidden" value="yes" name="tax_handle" id="tax_status">
                        <input type="hidden" value="yes" name="applyDiscount" id="discount_handle">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require('modal.view.php'); ?>
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
                    resultBox.innerHTML = "<p class='text-muted'>No customers found</p>";
                    return;
                }

                data.forEach(customer => {
                    const div = document.createElement("div");
                    div.classList.add("customer-suggestion", "p-2", "border-bottom", "cursor-pointer");
                    div.innerHTML = `<strong>${customer.name}</strong> - ${customer.phone}<br><small>${customer.email}</small>`;
              div.addEventListener("click", () => selectCustomer(customer));
                    resultBox.appendChild(div);
                });
            });
    });

    function selectCustomer(customer) {
        document.getElementById("customer_id").value = customer.id;
        document.getElementById("customer_name").innerHTML =`<strong> ${customer.name}</strong>`;
        document.getElementById("customer_address1").innerHTML =`<strong> ${customer.address}</strong>`;
        document.getElementById("customer_phone").innerHTML =
        `<strong>PHONE:</strong> ${customer.phone}<br><strong>EMAIL:</strong> ${customer.email}`;

        document.getElementById("customer-box").value = customer.name;
        document.getElementById("customer-box-result").innerHTML = "";
    }
});

</script>
<script type="text/javascript">
    $(function () {
        $('.summernote').summernote({
            height: 100,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['fullscreen', ['fullscreen']],
                ['codeview', ['codeview']]
            ]
        });
    });

</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Delegate event for all current & future product name inputs
    document.querySelector(".tfr tbody").addEventListener("input", function (e) {
        const input = e.target;
          const warehouseId = document.getElementById("warehouses").value;
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

        fetch(`/AIS/api/product_search?query=${encodeURIComponent(query)}&warehouse_id=${warehouseId}`)
            .then(res => res.json())
            .then(data => {
                resultBox.innerHTML = "";
                if (data.length === 0) {
                    resultBox.innerHTML = `<p class="text-muted">No products found</p>`;
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
                document.getElementById(`discount-${rowId}`).value = product.discount || 0;
                document.getElementById(`dpid-${rowId}`).value = product.description || '';
                document.getElementById(`pid-${rowId}`).value = product.id;

                const resultBox = document.getElementById(`product-box-result-${rowId}`);
                if (resultBox) resultBox.remove();

                rowTotal(rowId);
                billUpyog();
            }
        });
    var dtformat = $('#hdata').attr('data-df');
        var currency = $('#hdata').attr('data-curr');
</script>
<!-- BEGIN VENDOR JS-->
<script type="text/javascript">

    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('#sdate').datepicker('setDate', '16-06-2025');
    $('.date30').datepicker('setDate', '16-06-2025');


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
