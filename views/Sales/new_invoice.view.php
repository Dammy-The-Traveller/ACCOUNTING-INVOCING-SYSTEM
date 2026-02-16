<?php
$isEdit = isset($invoice);
$isEditingItems = isset($items);
$isEditingCustomer = isset($customer);
$isEditingProducts = isset($products);

// Set currency symbol
$currencySymbol = '$'; // Default
if (isset($invoice)) {
    if ($invoice['currency'] == 1) {
        $currencySymbol = '£';
    } elseif ($invoice['currency'] == 2) {
        $currencySymbol = '€';
    }
}
?>

<style>
.customer-suggestion {
    cursor: pointer;
}
.customer-suggestion:hover {
    background-color: #f1f1f1;
}
.product-suggestion {
    cursor: pointer;
}
.product-suggestion:hover {
    background-color: #f1f1f1;
}
</style>

<?php include __DIR__ . '/../partials/head.php'; ?>

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

<body data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns fixed-navbar">
    <span id="hdata" data-df="dd-mm-yyyy" data-curr="<?= $currencySymbol ?>"></span>

    <!-- Navbar -->
    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <!-- Sidenav -->
    <?php include __DIR__ . '/../partials/Sidenav.php'; ?>

    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="card card-block">
                    <div id="notify" class="alert alert-success" style="display:none;">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <div class="message"></div>
                    </div>
                    <form method="POST" id="data_form" action="<?= $isEdit ? '/AIS/invoices-update' : '/AIS/store-sales' ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="id" value="<?= $isEdit ? $invoice['id'] : '' ?>">
                        <div class="row">
                            <div class="col-sm-6 cmp-pnl">
                                <div id="customerpanel" class="inner-cmp-pnl">
                                    <div class="form-group row">
                                        <div class="fcol-sm-12">
                                            <h3 class="title">
                                                Bill To
                                                <a href="#" class="btn btn-primary btn-sm rounded" data-toggle="modal" data-target="#addCustomer" aria-label="Add Client">
                                                    Add Client
                                                </a>
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="frmSearch col-sm-12">
                                            <label for="cst" class="caption">Search Client</label>
                                            <input type="text" class="form-control" name="cst" id="customer-box" placeholder="Enter Customer Name or Mobile Number to search" autocomplete="off" aria-label="Search Client" />
                                            <div id="customer-box-result"></div>
                                        </div>
                                    </div>
                                    <div id="customer">
                                        <div class="clientinfo">
                                            Client Details
                                            <hr>
                                            <input type="hidden" name="customer_id" id="customer_id" value="<?= $isEdit ? $invoice['customer_id'] : '0' ?>">
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
                                        <select id="warehouses" class="selectpicker form-control" required aria-label="Select Warehouse">
                                            <option value="0">Select Warehouse</option>
                                            <?php foreach ($warehouses as $warehouse): ?>
                                                <option value="<?= $warehouse['id'] ?>"><?= htmlspecialchars($warehouse['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 cmp-pnl">
                                <div class="inner-cmp-pnl">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <h3 class="title">Invoice Properties</h3>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label for="invocieno" class="caption">Invoice Number</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-file-text-o" aria-hidden="true"></span></div>
                                                <input required type="text" class="form-control" placeholder="Invoice #" name="invocieno" value="<?= $isEdit ? htmlspecialchars($invoice['invoice_number']) : generateInvoiceIndex() ?>" aria-label="Invoice Number">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="refer" class="caption">Reference</label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="icon-bookmark-o" aria-hidden="true"></span></div>
                                                <input required type="text" class="form-control" placeholder="Reference #" name="refer" value="<?= $isEdit ? htmlspecialchars($invoice['reference']) : '' ?>" aria-label="Reference Number">
                                            </div>
                                        </div>
                                    </div>
                                  <div class="form-group row">
           <div class="col-sm-6"><label for="invociedate" class="caption">Invoice Date</label>
             <div class="input-group">
               <div class="input-group-addon"><span class="icon-calendar4" aria-hidden="true"></span></div>
               <input required type="text" class="form-control required" placeholder="Billing Date" name="invoicedate" data-toggle="datepicker" autocomplete="false" value="<?= $isEdit ? date('d-m-Y', strtotime($invoice['invoice_date'])) : date('d-m-Y') ?>" id="sdate">
             </div>
           </div>
           <div class="col-sm-6"><label for="invocieduedate" class="caption">Invoice Due Date</label>
             <div class="input-group">
               <div class="input-group-addon"><span class="icon-calendar-o" aria-hidden="true"></span></div>
               <input required type="text" class="form-control required"  name="invocieduedate" placeholder="Due Date" data-toggle="datepicker" autocomplete="false" value="<?= $isEdit ? date('d-m-Y', strtotime($invoice['due_date'])) : date('d-m-Y') ?>" id="sdate">
             </div>
           </div>
         </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label for="taxformat" class="caption">Tax</label>
                                            <select required class="form-control" name="taxformat" onchange="changeTaxFormat(this.value)" id="taxformat" aria-label="Tax Format">
                                                <option value="on" <?= $isEdit && $invoice['tax_format'] == 'on' ? 'selected' : '' ?>>On</option>
                                                <option value="off" <?= $isEdit && $invoice['tax_format'] == 'off' ? 'selected' : '' ?>>Off</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="discountFormat" class="caption">Discount</label>
                                            <select class="form-control" name="discountFormat" onchange="changeDiscountFormat(this.value)" id="discountFormat" aria-label="Discount Format">
                                                <option value="%" <?= $isEdit && $invoice['tax_format'] == '%' ? 'selected' : '' ?>>% Discount After TAX</option>
                                                <option value="flat" <?= $isEdit && $invoice['tax_format'] == 'flat' ? 'selected' : '' ?>>Flat Discount After TAX</option>
                                                <option value="b_p" <?= $isEdit && $invoice['tax_format'] == 'b_p' ? 'selected' : '' ?>>% Discount Before TAX</option>
                                                <option value="bflat" <?= $isEdit && $invoice['tax_format'] == 'bflat' ? 'selected' : '' ?>>Flat Discount Before TAX</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label for="toAddInfo" class="caption">Invoice Note</label>
                                            <textarea class="form-control" name="notes" rows="2" aria-label="Invoice Note"><?= $isEdit ? htmlspecialchars($invoice['notes']) : '' ?></textarea>
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
                                        <th width="8%" class="text-center">Quantity</th>
                                        <th width="10%" class="text-center">Rate</th>
                                        <th width="10%" class="text-center">Tax(%)</th>
                                        <th width="10%" class="text-center">Tax</th>
                                        <th width="7%" class="text-center">Discount(%)</th>
                                        <th width="10%" class="text-center">Amount (<?= $currencySymbol ?>)</th>
                                        <th width="5%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($isEdit && !empty($items)): ?>
                                        <?php foreach ($items as $index => $item): ?>
                                            <tr>
                                                <td>
                                                    <input required type="text" class="form-control text-center" name="product_name[]" value="<?= htmlspecialchars($item['product_name']) ?>" id="productname-<?= $index ?>" aria-label="Product Name">
                                                </td>
                                                <td>
                                                    <input required type="text" class="form-control req amnt" name="product_qty[]" id="amount-<?= $index ?>" onkeyup="rowTotal('<?= $index ?>'); billUpyog();" value="<?= htmlspecialchars($item['quantity']) ?>" aria-label="Quantity">
                                                </td>
                                                <td>
                                                    <input required type="text" class="form-control req prc" name="product_price[]" id="price-<?= $index ?>" onkeyup="rowTotal('<?= $index ?>'); billUpyog();" value="<?= htmlspecialchars($item['price']) ?>" aria-label="Price">
                                                </td>
                                                <td>
                                                    <input required type="text" class="form-control vat" name="product_tax[]" id="vat-<?= $index ?>" onkeyup="rowTotal('<?= $index ?>'); billUpyog();" value="<?= htmlspecialchars($item['tax_percent']) ?>" aria-label="Tax Percent">
                                                </td>
                                                <td class="text-center" id="texttaxa-<?= $index ?>"><?= htmlspecialchars($item['tax_amount']) ?></td>
                                                <td>
                                                    <input required type="text" class="form-control discount" name="product_discount[]" id="discount-<?= $index ?>" onkeyup="rowTotal('<?= $index ?>'); billUpyog();" value="<?= htmlspecialchars($item['discount']) ?>" aria-label="Discount">
                                                </td>
                                                <td><strong><span class="ttlText" id="result-<?= $index ?>"><?= htmlspecialchars($item['subtotal']) ?></span></strong></td>
                                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row" aria-label="Remove Row">X</button></td>
                                                <input required type="hidden" name="taxa[]" id="taxa-<?= $index ?>" value="<?= htmlspecialchars($item['tax_amount']) ?>">
                                                <input required type="hidden" name="disca[]" id="disca-<?= $index ?>" value="<?= htmlspecialchars($item['discount']) ?>">
                                                <input required type="hidden" class="ttInput" name="product_subtotal[]" id="total-<?= $index ?>" value="<?= htmlspecialchars($item['subtotal']) ?>">
                                                <input required type="hidden" class="pdIn" name="pid[]" id="pid-<?= $index ?>" value="<?= htmlspecialchars($item['id']) ?>">
                                            </tr>
                                            <tr>
                                                <td colspan="8">
                                                    <textarea id="dpid-<?= $index ?>" class="form-control" name="product_description[]" aria-label="Product Description"><?= htmlspecialchars($item['product_description']) ?></textarea><br>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="last-item-row sub_c">
                                            <td class="add-row">
                                                <button type="button" class="btn btn-success" id="addproducts" aria-label="Add Product Row" data-toggle="tooltip" data-placement="top" title="Add product row">
                                                    <i class="icon-plus-square"></i> Add Row
                                                </button>
                                            </td>
                                            <td colspan="7"></td>
                                        </tr>
                                        <tr class="sub_c">
                                            <td colspan="6" align="right">
                                                <input required type="hidden" name="subtotal" id="subttlform" value="<?= $isEdit ? htmlspecialchars($invoice['subtotal']) : '0' ?>">
                                                <strong>Total Tax</strong>
                                            </td>
                                            <td colspan="2">
                                                <span class="currenty"><?= $currencySymbol ?></span>
                                                <span id="taxr"><?= $isEdit ? htmlspecialchars($items[0]['tax_amount']) : '0' ?></span>
                                            </td>
                                        </tr>
                                        <tr class="sub_c">
                                            <td colspan="6" align="right">
                                                <strong>Total Discount (<?= $currencySymbol ?>)</strong>
                                            </td>
                                            <td colspan="2">
                                                <span class="currenty"><?= $currencySymbol ?></span>
                                                <span id="discs"><?= $isEdit ? htmlspecialchars($items[0]['discount']) : '0' ?></span>
                                            </td>
                                        </tr>
                                        <tr class="sub_c">
                                            <td colspan="6" align="right">
                                                <strong>Shipping</strong>
                                            </td>
                                            <td colspan="2">
                                                <input required type="text" class="form-control shipVal" name="shipping" value="<?= $isEdit ? htmlspecialchars($invoice['shipping']) : '0' ?>" onkeyup="updateTotal();" aria-label="Shipping Cost">
                                            </td>
                                        </tr>
                                        <tr class="sub_c">
                                            <td colspan="2">
                                                Payment Currency
                                                <select name="mcurrency" class="selectpicker form-control" aria-label="Payment Currency">
                                                    <option value="0" <?= $isEdit && $invoice['currency'] == 0 ? 'selected' : '' ?>>Default</option>
                                                    <option value="1" <?= $isEdit && $invoice['currency'] == 1 ? 'selected' : '' ?>>£ (GBP)</option>
                                                    <option value="2" <?= $isEdit && $invoice['currency'] == 2 ? 'selected' : '' ?>>€ (EUR)</option>
                                                </select>
                                            </td>
                                            <td colspan="4" align="right">
                                                <strong>Grand Total (<?= $currencySymbol ?>)</strong>
                                            </td>
                                            <td colspan="2">
                                                <input required type="text" name="total" class="form-control" id="invoiceyoghtml" readonly value="<?= $isEdit ? htmlspecialchars($invoice['grand_total']) : '0' ?>" aria-label="Grand Total">
                                            </td>
                                        </tr>
                                        <tr class="sub_c">
                                            <td colspan="2">
                                                Payment Terms
                                                <select name="pterms" class="selectpicker form-control" aria-label="Payment Terms">
                                                    <option value="1" <?= $isEdit && $invoice['payment_terms'] == 1 ? 'selected' : '' ?>>Due On Receipt</option>
                                                    <option value="2" <?= $isEdit && $invoice['payment_terms'] == 2 ? 'selected' : '' ?>>Net 15</option>
                                                    <option value="3" <?= $isEdit && $invoice['payment_terms'] == 3 ? 'selected' : '' ?>>Net 30</option>
                                                </select>
                                            </td>
                                            <td colspan="6" align="right">
                                                <button type="submit" class="btn btn-success sub-btn" data-loading-text="<?= $isEdit ? 'Updating...' : 'Creating...' ?>" aria-label="<?= $isEdit ? 'Update Invoice' : 'Create Invoice' ?>">
                                                    <?= $isEdit ? 'Update Invoice' : 'Generate Invoice' ?>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td>
                                                <input required type="text" class="form-control text-center" name="product_name[]" placeholder="Enter Product name or Code" id="productname-0" aria-label="Product Name">
                                            </td>
                                            <td>
                                                <input required type="text" class="form-control req amnt" name="product_qty[]" id="amount-0" onkeyup="rowTotal('0'); billUpyog();" autocomplete="off" value="1" aria-label="Quantity">
                                            </td>
                                            <td>
                                                <input required type="text" class="form-control req prc" name="product_price[]" id="price-0" onkeyup="rowTotal('0'); billUpyog();" autocomplete="off" aria-label="Price">
                                            </td>
                                            <td>
                                                <input required type="text" class="form-control vat" name="product_tax[]" id="vat-0" onkeyup="rowTotal('0'); billUpyog();" autocomplete="off" aria-label="Tax Percent">
                                            </td>
                                            <td class="text-center" id="texttaxa-0">0</td>
                                            <td>
                                                <input required type="text" class="form-control discount" name="product_discount[]" id="discount-0" onkeyup="rowTotal('0'); billUpyog();" autocomplete="off" aria-label="Discount">
                                            </td>
                                            <td>
                                                <span class="currenty"><?= $currencySymbol ?></span>
                                                <strong><span class="ttlText" id="result-0">0</span></strong>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-row" aria-label="Remove Row">X</button>
                                            </td>
                                            <input required type="hidden" name="taxa[]" id="taxa-0" value="0">
                                            <input required type="hidden" name="disca[]" id="disca-0" value="0">
                                            <input required type="hidden" class="ttInput" name="product_subtotal[]" id="total-0" value="0">
                                            <input required type="hidden" class="pdIn" name="pid[]" id="pid-0" value="0">
                                        </tr>
                                        <tr>
                                            <td colspan="8">
                                                <textarea id="dpid-0" class="form-control" name="product_description[]" placeholder="Enter Product description" autocomplete="off" aria-label="Product Description"></textarea><br>
                                            </td>
                                        </tr>
                                        <tr class="last-item-row sub_c">
                                            <td class="add-row">
                                                <button type="button" class="btn btn-success" id="addproducts" aria-label="Add Product Row" data-toggle="tooltip" data-placement="top" title="Add product row">
                                                    <i class="icon-plus-square"></i> Add Row
                                                </button>
                                            </td>
                                            <td colspan="7"></td>
                                        </tr>
                                        <tr class="sub_c">
                                            <td colspan="6" align="right">
                                                <input required type="hidden" value="0" id="subttlform" name="subtotal">
                                                <strong>Total Tax</strong>
                                            </td>
                                            <td align="left" colspan="2">
                                                <span class="currenty"><?= $currencySymbol ?></span>
                                                <span id="taxr">0</span>
                                            </td>
                                        </tr>
                                        <tr class="sub_c">
                                            <td colspan="6" align="right">
                                                <strong>Total Discount</strong>
                                            </td>
                                            <td align="left" colspan="2">
                                                <span class="currenty"><?= $currencySymbol ?></span>
                                                <span id="discs">0</span>
                                            </td>
                                        </tr>
                                        <tr class="sub_c">
                                            <td colspan="6" align="right">
                                                <strong>Shipping</strong>
                                            </td>
                                            <td align="left" colspan="2">
                                                <input required type="text" class="form-control shipVal" name="shipping" placeholder="Value" autocomplete="off" onkeyup="updateTotal();" value="0" aria-label="Shipping Cost">
                                            </td>
                                        </tr>
                                        <tr class="sub_c">
                                            <td colspan="2">
                                                Payment Currency
                                                <select name="mcurrency" class="selectpicker form-control" aria-label="Payment Currency">
                                                    <option value="0">Default</option>
                                                    <option value="1">£ (GBP)</option>
                                                    <option value="2">€ (EUR)</option>
                                                </select>
                                            </td>
                                            <td colspan="4" align="right">
                                                <strong>Grand Total (<span class="currenty"><?= $currencySymbol ?></span>)</strong>
                                            </td>
                                            <td align="left" colspan="2">
                                                <input required type="text" name="total" class="form-control" id="invoiceyoghtml" readonly value="0" aria-label="Grand Total">
                                            </td>
                                        </tr>
                                        <tr class="sub_c">
                                            <td colspan="2">
                                                Payment Terms
                                                <select name="pterms" class="selectpicker form-control" aria-label="Payment Terms">
                                                    <option value="1">Due On Receipt</option>
                                                    <option value="2">Net 15</option>
                                                    <option value="3">Net 30</option>
                                                </select>
                                            </td>
                                            <td align="right" colspan="6">
                                                <div class="mb-3 d-grid gap-2">
                                                    <button type="submit" class="btn btn-success sub-btn" data-loading-text="<?= $isEdit ? 'Updating...' : 'Creating...' ?>" aria-label="<?= $isEdit ? 'Update Invoice' : 'Create Invoice' ?>">
                                                        <?= $isEdit ? 'Update Invoice' : 'Generate Invoice' ?>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" value="<?= $isEdit ? 'invoices/update' : 'invoices/action' ?>" id="action-url">
                        <input type="hidden" value="<?= $isEdit ? 'edit' : 'search' ?>" id="billtype">
                        <input type="hidden" value="<?= $isEdit ? count($items) : 1 ?>" name="counter" id="ganak">
                        <input type="hidden" value="yes" name="tax_handle" id="tax_status">
                        <input type="hidden" value="yes" name="applyDiscount" id="discount_handle">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <?php require('modal.view.php'); ?>

    <!-- JavaScript -->
    <script>
        // Debounce function to limit AJAX calls
        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Number validation function
        function isNumber(event) {
            const charCode = event.which ? event.which : event.keyCode;
            if (charCode === 46 || charCode === 8 || (charCode >= 48 && charCode <= 57)) {
                return true;
            }
            return false;
        }

        // Tax format handler
        function changeTaxFormat(value) {
            document.getElementById('tax_status').value = value;
            billUpyog();
        }

        // Discount format handler
        function changeDiscountFormat(value) {
            document.getElementById('discount_handle').value = value;
            billUpyog();
        }

        // Placeholder for generating invoice number
        function generateInvoiceIndex() {
            return 'INV-' + Math.floor(Math.random() * 1000000); // Replace with actual logic
        }

        document.addEventListener("DOMContentLoaded", () => {
            // Initialize datepicker
            $('[data-toggle="datepicker"]').datepicker({
                autoHide: true,
                format: 'dd-mm-yyyy',
                date: new Date()
            });

            // Initialize tooltips (assumes Bootstrap)
            $('[data-toggle="tooltip"]').tooltip();

            // Client search
            const customerBox = document.getElementById("customer-box");
            const customerResultBox = document.getElementById("customer-box-result");

            customerBox.addEventListener("keyup", debounce(function () {
                const query = this.value.trim();
                if (query.length < 2) {
                    customerResultBox.innerHTML = "";
                    return;
                }

                fetch(`/AIS/api/fetch_client_data?query=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.json();
                    })
                    .then(data => {
                        customerResultBox.innerHTML = "";
                        if (data.length === 0) {
                            customerResultBox.innerHTML = "<p class='text-muted'>No customers found</p>";
                            return;
                        }

                        data.forEach(customer => {
                            const div = document.createElement("div");
                            div.classList.add("customer-suggestion", "p-2", "border-bottom", "cursor-pointer");
                            div.innerHTML = `<strong>${customer.name}</strong> - ${customer.phone}<br><small>${customer.email}</small>`;
                            div.addEventListener("click", () => selectCustomer(customer));
                            customerResultBox.appendChild(div);
                        });
                    })
                    .catch(error => {
                        customerResultBox.innerHTML = `<p class="text-danger">Error fetching clients</p>`;
                    });
            }, 300));

            function selectCustomer(customer) {
                document.getElementById("customer_id").value = customer.id;
                document.getElementById("customer_name").innerHTML = `<strong>${customer.name}</strong>`;
                document.getElementById("customer_address1").innerHTML = `<strong>${customer.address}</strong>`;
                document.getElementById("customer_phone").innerHTML = `<strong>PHONE:</strong> ${customer.phone}<br><strong>EMAIL:</strong> ${customer.email}`;
                document.getElementById("customer-box").value = customer.name;
                customerResultBox.innerHTML = "";
            }

            // Product search
            document.querySelector(".tfr tbody").addEventListener("input", debounce(function (e) {
                const input = e.target;
                const warehouseId = document.getElementById("warehouses").value;
                if (!input.name.startsWith("product_name")) return;

                const rowId = input.id.split("-")[1];
                let resultBox = document.getElementById(`product-box-result-${rowId}`);

                if (!resultBox) {
                    resultBox = document.createElement("div");
                    resultBox.id = `product-box-result-${rowId}`;
                    resultBox.classList.add("bg-white", "border", "p-2", "w-100", "mt-1");
                    input.parentElement.appendChild(resultBox);
                }

                const query = input.value.trim();
                if (query.length < 2) {
                    resultBox.innerHTML = "";
                    return;
                }

                if (!warehouseId || warehouseId === "0") {
                    resultBox.innerHTML = `<p class="text-muted">Please select a warehouse first</p>`;
                    return;
                }

                fetch(`/AIS/api/product_search?query=${encodeURIComponent(query)}&warehouse_id=${warehouseId}`)
                    .then(res => {
                        if (!res.ok) throw new Error('Network error');
                        return res.json();
                    })
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
                    })
                    .catch(error => {
                        resultBox.innerHTML = `<p class="text-danger">Error fetching products</p>`;
                          resultBox.classList.add("d-none");
                    });
            }, 300));

            // Row management
            let rowCount = <?= $isEdit ? count($items) : 1 ?>;
            document.getElementById("addproducts").addEventListener("click", function () {
                const table = document.querySelector(".tfr tbody");
                const rowId = rowCount;
                const currencySymbol = document.getElementById("hdata").getAttribute("data-curr");

                const rowHTML = `
                    <tr>
                        <td><input type="text" class="form-control text-center" name="product_name[]" id="productname-${rowId}" placeholder="Enter Product name or Code" aria-label="Product Name"></td>
                        <td><input type="text" class="form-control req amnt" name="product_qty[]" id="amount-${rowId}" value="1" onkeyup="rowTotal('${rowId}'); billUpyog();" autocomplete="off" aria-label="Quantity"></td>
                        <td><input type="text" class="form-control req prc" name="product_price[]" id="price-${rowId}" onkeyup="rowTotal('${rowId}'); billUpyog();" autocomplete="off" aria-label="Price"></td>
                        <td><input type="text" class="form-control vat" name="product_tax[]" id="vat-${rowId}" onkeyup="rowTotal('${rowId}'); billUpyog();" autocomplete="off" aria-label="Tax Percent"></td>
                        <td class="text-center" id="texttaxa-${rowId}">0</td>
                        <td><input type="text" class="form-control discount" name="product_discount[]" id="discount-${rowId}" onkeyup="rowTotal('${rowId}'); billUpyog();" autocomplete="off" aria-label="Discount"></td>
                        <td><span class="currenty">${currencySymbol}</span> <strong><span class="ttlText" id="result-${rowId}">0</span></strong></td>
                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row" aria-label="Remove Row">X</button></td>
                        <input type="hidden" name="taxa[]" id="taxa-${rowId}" value="0">
                        <input type="hidden" name="disca[]" id="disca-${rowId}" value="0">
                        <input type="hidden" class="ttInput" name="product_subtotal[]" id="total-${rowId}" value="0">
                        <input type="hidden" class="pdIn" name="pid[]" id="pid-${rowId}" value="0">
                    </tr>
                    <tr>
                        <td colspan="8">
                            <textarea id="dpid-${rowId}" class="form-control" name="product_description[]" placeholder="Enter Product description" autocomplete="off" aria-label="Product Description"></textarea><br>
                        </td>
                    </tr>`;

                const lastRow = document.querySelector(".last-item-row");
                lastRow.insertAdjacentHTML('beforebegin', rowHTML);
                rowCount++;
                document.getElementById("ganak").value = rowCount;
            });

            document.querySelector(".tfr tbody").addEventListener("click", function (e) {
                if (e.target.classList.contains("remove-row")) {
                    const row = e.target.closest("tr");
                    const nextRow = row.nextElementSibling;
                    row.remove();
                    if (nextRow && nextRow.querySelector("textarea")) nextRow.remove();
                    rowCount--;
                    document.getElementById("ganak").value = rowCount;
                    billUpyog();
                }
            });

            // Calculations
            function rowTotal(id) {
                const qty = parseFloat(document.getElementById(`amount-${id}`).value) || 0;
                const price = parseFloat(document.getElementById(`price-${id}`).value) || 0;
                const taxRate = parseFloat(document.getElementById(`vat-${id}`).value) || 0;
                const discountRate = parseFloat(document.getElementById(`discount-${id}`).value) || 0;

                const base = qty * price;
                const taxAmount = (taxRate / 100) * base;
                const discountAmount = (discountRate / 100) * base;
                const total = base + taxAmount - discountAmount;

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

                document.getElementById("subttlform").value = subtotal.toFixed(2);
                document.getElementById("taxr").innerText = totalTax.toFixed(2);
                document.getElementById("discs").innerText = totalDiscount.toFixed(2);
                document.getElementById("invoiceyoghtml").value = grandTotal.toFixed(2);
            }

            function updateTotal() {
                billUpyog();
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

        // Load vendor scripts
        var dtformat = $('#hdata').attr('data-df');
        var currency = $('#hdata').attr('data-curr');
    </script>
    
<?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>