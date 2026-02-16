<?php
/** @var array $invoice */
/** @var array $customer */
/** @var array $items */

// Set default values
$invoice = isset($invoice) && is_array($invoice) ? $invoice : [];
$customer = isset($customer) && is_array($customer) ? $customer : ['name' => 'Unknown Customer', 'address' => 'N/A', 'email' => '', 'phone' => ''];
$items = isset($items) && is_array($items) ? $items : [];
$currencySymbol = '$'; // Default currency symbol

// Set currency symbol based on invoice currency
if (!empty($invoice['currency'])) {
    switch ($invoice['currency']) {
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

// Calculate summary totals with fallbacks
$calculatedSubtotal = isset($calculatedSubtotal) ? floatval($calculatedSubtotal) : array_sum(array_map(function ($item) {
    return isset($item['price']) && isset($item['quantity']) ? floatval($item['price']) * floatval($item['quantity']) : 0;
}, $items));
$calculatedTax = isset($calculatedTax) ? floatval($calculatedTax) : array_sum(array_column($items, 'tax_amount') ?: [0]);
$calculatedDiscount = isset($calculatedDiscount) ? floatval($calculatedDiscount) : array_sum(array_column($items, 'discount_amount') ?: [0]);
$calculatedGrandTotal = isset($calculatedGrandTotal) ? floatval($calculatedGrandTotal) : ($calculatedSubtotal + $calculatedTax - $calculatedDiscount + (isset($invoice['shipping']) ? floatval($invoice['shipping']) : 0));
?>

<?php if (!empty($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notify = document.getElementById('notify');
            const message = notify.querySelector('.message');
            message.innerHTML = <?= json_encode($_SESSION['success']) ?>;
            notify.style.display = 'block';
            setTimeout(function() {
                notify.style.display = 'none';
            }, 3000);
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php include __DIR__ . '/../../partials/head.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

<body data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns fixed-navbar">
    <span id="hdata" data-df="dd-mm-yyyy" data-curr="<?= htmlspecialchars($currencySymbol) ?>"></span>

    <!-- Navbar -->
    <?php include __DIR__ . '/../../partials/navbar.php'; ?>

    <!-- Sidenav -->
    <?php include __DIR__ . '/../../partials/Sidenav.php'; ?>

    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div id="notify" class="alert alert-success" style="display:none;">
                <a href="#" class="close" data-dismiss="alert" aria-label="Close">&times;</a>
                <div class="message"></div>
            </div>

            <div class="content-body">
                <section class="card">
                    <div id="invoice-template" class="card-block">
                        <div class="row wrapper white-bg page-heading">
                            <div class="col-lg-12">
                                <div class="title-action">
                                    <?php if (!empty($invoice) && ($invoice['status'] ?? 'active') !== 'cancelled'): ?>
                                        <a href="/AIS/create?id=<?= htmlspecialchars($invoice['id'] ?? 0) ?>" class="btn btn-warning mb-1" aria-label="Edit Invoice">
                                            <i class="icon-pencil"></i> Edit Invoice
                                        </a>
                                        <a href="#part_payment" data-toggle="modal" class="btn btn-success mb-1" title="Partial Payment" aria-label="Make Payment">
                                            <i class="icon-money"></i> Make Payment
                                        </a>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle mb-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Email Options">
                                                <i class="icon-envelope-o"></i> Email
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="#sendEmail" data-toggle="modal" class="dropdown-item sendbill" data-type="notification">Invoice Notification</a>
                                                <div class="dropdown-divider"></div>
                                                <a href="#sendEmail" data-toggle="modal" class="dropdown-item sendbill" data-type="reminder">Payment Reminder</a>
                                                <a href="#sendEmail" data-toggle="modal" class="dropdown-item sendbill" data-type="received">Payment Received</a>
                                                <div class="dropdown-divider"></div>
                                                <a href="#sendEmail" data-toggle="modal" class="dropdown-item sendbill" data-type="overdue">Payment Overdue</a>
                                                <a href="#sendEmail" data-toggle="modal" class="dropdown-item sendbill" data-type="refund">Refund Generated</a>
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-blue dropdown-toggle mb-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="SMS Options">
                                                <i class="icon-envelope-o"></i> SMS
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="#sendSMS" data-toggle="modal" class="dropdown-item sendsms" data-type="notification">Invoice Notification</a>
                                                <div class="dropdown-divider"></div>
                                                <a href="#sendSMS" data-toggle="modal" class="dropdown-item sendsms" data-type="reminder">Payment Reminder</a>
                                                <a href="#sendSMS" data-toggle="modal" class="dropdown-item sendsms" data-type="received">Payment Received</a>
                                                <div class="dropdown-divider"></div>
                                                <a href="#sendSMS" data-toggle="modal" class="dropdown-item sendsms" data-type="overdue">Payment Overdue</a>
                                                <a href="#sendSMS" data-toggle="modal" class="dropdown-item sendsms" data-type="refund">Refund Generated</a>
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Print Options">
                                                <i class="icon-print"></i> Print
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="/AIS/invoice-generate?id=<?= htmlspecialchars($invoice['id'] ?? 0) ?>&token=<?= htmlspecialchars($invoice['public_token'] ?? '') ?>" aria-label="Print Invoice">Print</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="/AIS/invoice-download?id=<?= htmlspecialchars($invoice['id'] ?? 0) ?>&token=<?= htmlspecialchars($invoice['public_token'] ?? '') ?>" aria-label="Download PDF">PDF Download</a>
                                            </div>
                                        </div>
                                        <a href="/AIS/invoice-view?id=<?= htmlspecialchars($invoice['id'] ?? 0) ?>&token=<?= htmlspecialchars($invoice['public_token'] ?? '') ?>" class="btn btn-brown mb-1" aria-label="Preview Invoice">
                                            <i class="icon-earth"></i> Preview
                                        </a>
                                        <a href="#pop_model" data-toggle="modal" class="btn btn-cyan mb-1" title="Change Status" aria-label="Change Status">
                                            <i class="icon-tab"></i> Change Status
                                        </a>
                                        <a href="#cancel_bill" class="btn btn-danger mb-1" id="cancel-bill" data-toggle="modal" aria-label="Cancel Invoice">
                                            <i class="icon-minus-circle"></i> Cancel
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-danger mb-1" disabled aria-label="Cancelled Invoice">
                                            <i class="icon-minus-circle"></i> Cancelled
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Company Details -->
                        <div id="invoice-company-details" class="row mt-2">
                            <div class="col-md-6 col-sm-12 text-xs-center text-md-left">
                                <h1 id="logoText" class="brand-logo height-50 text-responsive p-1 m-b-2">
                                    <span style="color:yellow;font-weight: 900;font-family: system-ui;">DTT </span>
                                    <span style="color:#3BAFDA;">EDGE</span>
                                </h1>
                            </div>
                            <div class="col-md-6 col-sm-12 text-xs-center text-md-right">
                                <h2>INVOICE</h2>
                                <p class="pb-1">SRN #<?= htmlspecialchars($invoice['invoice_number'] ?? 'N/A') ?></p>
                                <ul class="px-0 list-unstyled">
                                    <li>Gross Amount</li>
                                    <li class="lead text-bold-800"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($calculatedGrandTotal, 2) ?></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Invoice Customer Details -->
                        <div id="invoice-customer-details" class="row">
                            <div class="col-sm-12 text-xs-center text-md-left">
                                <p class="text-muted">Bill To</p>
                            </div>
                            <div class="col-md-6 col-sm-12 text-xs-center text-md-left">
                                <ul class="px-0 list-unstyled">
                                    <li class="text-bold-800">
                                        <a href="/AIS/customer-view?id=<?= htmlspecialchars($invoice['customer_id'] ?? 0) ?>" aria-label="View Customer">
                                            <strong class="invoice_a"><?= htmlspecialchars($customer['name'] ?? 'Unknown Customer') ?></strong>
                                        </a>
                                    </li>
                                    <li><?= htmlspecialchars($customer['address'] ?? 'N/A') ?></li>
                                </ul>
                            </div>
                            <div class="offset-md-3 col-md-3 col-sm-12 text-xs-center text-md-left">
                                <p><span class="text-muted">Invoice Date:</span> <?= !empty($invoice['invoice_date']) ? date('d-m-Y', strtotime($invoice['invoice_date'])) : 'N/A' ?></p>
                                <p><span class="text-muted">Due Date:</span> <?= !empty($invoice['due_date']) ? date('d-m-Y', strtotime($invoice['due_date'])) : 'N/A' ?></p>
                                <p><span class="text-muted">Terms:</span> <?= htmlspecialchars(!empty($invoice['payment_terms']) ? ($invoice['payment_terms'] == 1 ? 'Due On Receipt' : ($invoice['payment_terms'] == 2 ? 'Net 15' : 'Net 30')) : 'N/A') ?></p>
                            </div>
                        </div>

                        <!-- Invoice Items Details -->
                        <div id="invoice-items-details" class="pt-2">
                            <div class="row">
                                <div class="table-responsive col-sm-12">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Description</th>
                                                <th class="text-xs-left">Rate</th>
                                                <th class="text-xs-left">Qty</th>
                                                <th class="text-xs-left">Tax</th>
                                                <th class="text-xs-left">Discount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($items)): ?>
                                                <tr><td colspan="7" class="text-center">No items found.</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($items as $index => $item): ?>
                                                    <tr>
                                                        <th scope="row"><?= $index + 1 ?></th>
                                                        <td><?= htmlspecialchars($item['product_name'] ?? 'N/A') ?></td>
                                                        <td><?= htmlspecialchars($item['product_description'] ?? 'N/A') ?></td>
                                                        <td><?= htmlspecialchars($currencySymbol) ?> <?= number_format(floatval($item['price'] ?? 0), 2) ?></td>
                                                        <td><?= htmlspecialchars($item['quantity'] ?? 0) ?></td>
                                                        <td><?= htmlspecialchars($currencySymbol) ?> <?= number_format(floatval($item['tax_amount'] ?? 0), 2) ?> (<?= htmlspecialchars($item['tax_percent'] ?? 0) ?>%)</td>
                                                        <td><?= htmlspecialchars($currencySymbol) ?> <?= number_format(floatval($item['discount_amount'] ?? 0), 2) ?> (<?= htmlspecialchars($item['discount'] ?? 0) ?>%)</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7 col-sm-12 text-xs-center text-md-left">
                                    <p class="lead">Payment Status: <u><strong id="pstatus"><?= htmlspecialchars($invoice['payment_status'] ?? 'Unpaid') ?></strong></u></p>
                                    <p class="lead">Payment Method: <u><strong id="pmethod"><?= htmlspecialchars($invoice['payment_method'] ?? '---') ?></strong></u></p>
                                    <p class="lead mt-1">Note:</p>
                                    <code><?= htmlspecialchars($invoice['notes'] ?? 'No notes provided.') ?></code>
                                </div>
                                <div class="col-md-5 col-sm-12">
                                    <p class="lead">Summary</p>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tr>
                                                <td>Sub Total</td>
                                                <td class="text-xs-right"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($calculatedSubtotal, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>TAX</td>
                                                <td class="text-xs-right"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($calculatedTax, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Discount</td>
                                                <td class="text-xs-right"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($calculatedDiscount, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Shipping</td>
                                                <td class="text-xs-right"><?= htmlspecialchars($currencySymbol) ?> <?= number_format(floatval($invoice['shipping'] ?? 0), 2) ?></td>
                                            </tr>
                                            <tr class="bg-light">
                                                <td class="text-bold-800">Total</td>
                                                <td class="text-bold-800 text-xs-right"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($calculatedGrandTotal, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Payment Made</td>
                                                <td class="text-xs-right"><?= htmlspecialchars($currencySymbol) ?> <?= number_format(floatval($invoice['amount_paid'] ?? 0), 2) ?></td>
                                            </tr>
                                            <tr class="bg-grey bg-lighten-4">
                                                <td class="text-bold-800">Balance Due</td>
                                                <td class="text-bold-800 text-xs-right"><?= htmlspecialchars($currencySymbol) ?> <?= number_format($calculatedGrandTotal - floatval($invoice['amount_paid'] ?? 0), 2) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="text-xs-center">
                                        <p>Authorized person</p>
                                        <img loading="lazy" src="Public/assets/img/sign.png" alt="Signature" class="height-100"/>
                                        <h6>(Harry McGaughey)</h6>
                                        <p class="text-muted">Sales Manager</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Footer -->
                        <div id="invoice-footer">
                            <p class="lead">Credit Transactions:</p>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody id="activity">
                                    <?php if (!empty($invoice['transactions']) && is_array($invoice['transactions'])): ?>
                                        <?php foreach ($invoice['transactions'] as $transaction): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($transaction['date'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($transaction['method'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($currencySymbol) ?> <?= number_format(floatval($transaction['amount'] ?? 0), 2) ?></td>
                                                <td><?= htmlspecialchars($transaction['note'] ?? 'N/A') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center">No transactions recorded.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-md-7 col-sm-12">
                                    <h6>Terms & Conditions</h6>
                                    <p>
                                        <strong><?= htmlspecialchars($invoice['terms'] ?? 'Payment Due On Receipt.') ?></strong><br>
                                        <div><b>1. Prices And Payment</b></div>
                                        <div>Payments are to be made in U.S funds. Unless otherwise specified, all invoices are due net 30 days from date of shipment.</div>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Payment Modal -->
    <div id="part_payment" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Payment Confirmation</h4>
                </div>
                <div class="modal-body">
                    <form class="payment" action="/AIS/invoice-payment" method="POST">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="input-group">
                                    <div class="input-group-addon"><?= htmlspecialchars($currencySymbol) ?></div>
                                    <input type="number" step="0.01" class="form-control" placeholder="Total Amount" name="amount" id="rmpay" value="<?= htmlspecialchars($calculatedGrandTotal - floatval($invoice['amount_paid'] ?? 0)) ?>" aria-label="Payment Amount">
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="icon-calendar4" aria-hidden="true"></i></div>
                                    <input type="text" class="form-control required" placeholder="Billing Date" name="paydate" data-toggle="datepicker" aria-label="Payment Date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 mb-1">
                                <label for="pmethod">Payment Method</label>
                                <select name="pmethod" class="form-control mb-1" aria-label="Payment Method">
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Balance">Client Balance</option>
                                    <option value="Bank">Bank</option>
                                </select>
                                <label for="account">Account</label>
                                <select name="account" class="form-control" aria-label="Account">
                                    <option value="1">Company Sales Account / 12345678</option>
                                    <option value="6">Purchase A/C / 1234567890</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 mb-1">
                                <label for="shortnote">Note</label>
                                <input type="text" class="form-control" name="shortnote" placeholder="Short note" value="Payment for invoice <?= htmlspecialchars($invoice['id'] ?? 'N/A') ?>" aria-label="Payment Note">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="tid" value="<?= htmlspecialchars($invoice['id'] ?? 0) ?>">
                            <input type="hidden" name="cid" value="<?= htmlspecialchars($invoice['customer_id'] ?? 0) ?>">
                            <input type="hidden" name="cname" value="<?= htmlspecialchars($customer['name'] ?? 'Unknown Customer') ?>">
                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
                            <button type="submit" class="btn btn-primary" id="submitpayment" aria-label="Make Payment">Make Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancel_bill" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Cancel Invoice</h4>
                </div>
                <div class="modal-body">
                    <form class="cancelbill" action="/AIS/invoices-status" method="POST">
                        <input type="hidden" name="tid" value="<?= htmlspecialchars($invoice['id'] ?? 0) ?>">
                        <input type="hidden" name="status" value="cancelled">
                        <div class="row">
                            <div class="col-xs-12">
                                You cannot revert this action! Are you sure?
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
                            <button type="submit" class="btn btn-primary" id="cancelInvoice" aria-label="Cancel Invoice">Cancel Invoice</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div id="sendEmail" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Email</h4>
                </div>
                <div id="request" style="display: none;">
                    <div id="ballsWaveG">
                        <div id="ballsWaveG_1" class="ballsWaveG"></div>
                        <div id="ballsWaveG_2" class="ballsWaveG"></div>
                        <div id="ballsWaveG_3" class="ballsWaveG"></div>
                        <div id="ballsWaveG_4" class="ballsWaveG"></div>
                        <div id="ballsWaveG_5" class="ballsWaveG"></div>
                        <div id="ballsWaveG_6" class="ballsWaveG"></div>
                        <div id="ballsWaveG_7" class="ballsWaveG"></div>
                        <div id="ballsWaveG_8" class="ballsWaveG"></div>
                    </div>
                </div>
                <div class="modal-body" id="emailbody">
                    <form id="sendbill" action="/AIS/send-email" method="POST">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="icon-envelope-o" aria-hidden="true"></i></div>
                                    <input type="email" class="form-control" placeholder="Email" name="mailtoc" value="<?= htmlspecialchars($customer['email'] ?? '') ?>" aria-label="Recipient Email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 mb-1">
                                <label for="customername">Customer Name</label>
                                <input type="text" class="form-control" name="customername" value="<?= htmlspecialchars($customer['name'] ?? 'Unknown Customer') ?>" aria-label="Customer Name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 mb-1">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control" name="subject" id="subject" aria-label="Email Subject">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 mb-1">
                                <label for="contents">Message</label>
                                <textarea name="text" class="summernote" id="contents" title="Contents" aria-label="Email Message"></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="tid" value="<?= htmlspecialchars($invoice['id'] ?? 0) ?>">
                        <input type="hidden" name="emailtype" id="emailtype">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="submit" class="btn btn-primary" form="sendbill" aria-label="Send Email">Send</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SMS Modal -->
    <div id="sendSMS" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Send SMS</h4>
                </div>
                <div id="request_sms" style="display: none;">
                    <div id="ballsWaveG1">
                        <div id="ballsWaveG_1" class="ballsWaveG"></div>
                        <div id="ballsWaveG_2" class="ballsWaveG"></div>
                        <div id="ballsWaveG_3" class="ballsWaveG"></div>
                        <div id="ballsWaveG_4" class="ballsWaveG"></div>
                        <div id="ballsWaveG_5" class="ballsWaveG"></div>
                        <div id="ballsWaveG_6" class="ballsWaveG"></div>
                        <div id="ballsWaveG_7" class="ballsWaveG"></div>
                        <div id="ballsWaveG_8" class="ballsWaveG"></div>
                    </div>
                </div>
                <div class="modal-body" id="smsbody">
                    <form id="sendsms" action="/AIS/send-sms" method="POST">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="icon-envelope-o" aria-hidden="true"></i></div>
                                    <input type="text" class="form-control" placeholder="Mobile Number" name="mobile" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>" aria-label="Recipient Mobile Number">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 mb-1">
                                <label for="customername">Customer Name</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($customer['name'] ?? 'Unknown Customer') ?>" readonly aria-label="Customer Name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 mb-1">
                                <label for="text_message">Message</label>
                                <textarea class="form-control" name="text_message" id="sms_tem" rows="3" aria-label="SMS Message"></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="tid" value="<?= htmlspecialchars($invoice['id'] ?? 0) ?>">
                        <input type="hidden" name="smstype" id="smstype">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="submit" class="btn btn-primary" form="sendsms" aria-label="Send SMS">Send</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Modal -->
    <div id="pop_model" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Change Status</h4>
                </div>
                <div class="modal-body">
                    <form id="form_model" action="/AIS/invoices-update-status" method="POST">
                        <input type="hidden" name="tid" value="<?= htmlspecialchars($invoice['id'] ?? 0) ?>">
                        <div class="row">
                            <div class="col-xs-12 mb-1">
                                <label for="status">Mark As</label>
                                <select name="status" class="form-control mb-1" aria-label="Invoice Status">
                                    <option value="paid" <?= ($invoice['status'] ?? '') == 'paid' ? 'selected' : '' ?>>Paid</option>
                                    <option value="due" <?= ($invoice['status'] ?? '') == 'due' ? 'selected' : '' ?>>Due</option>
                                    <option value="partial" <?= ($invoice['status'] ?? '') == 'partial' ? 'selected' : '' ?>>Partial</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
                            <button type="submit" class="btn btn-primary" id="submit_model" aria-label="Change Status">Change Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
 
   

    <script>
        $(document).ready(function () {
            // Initialize datepicker
            $('[data-toggle="datepicker"]').datepicker({
                autoHide: true,
                format: 'dd-mm-yyyy',
                date: new Date()
            });

            // Initialize Summernote
            $('.summernote').summernote({
                height: 150,
                toolbar: [
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

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Email modal
            $(document).on('click', '.sendbill', function () {
                const type = $(this).data('type') || 'notification';
                $('#emailtype').val(type);
                $('#request').show();
                $('#emailbody').hide();

                let subject = '';
                let message = '';

                const customerName = '<?= addslashes(htmlspecialchars($customer['name'] ?? 'Unknown Customer')) ?>';
                const invoiceNumber = '<?= addslashes(htmlspecialchars($invoice['invoice_number'] ?? 'N/A')) ?>';

                switch (type) {
                    case 'notification':
                        subject = 'Your Invoice is Ready';
                        message = `Dear ${customerName}, your invoice is ready. Please find the details attached.`;
                        break;
                    case 'reminder':
                        subject = 'Payment Reminder';
                        message = `This is a friendly reminder to make your payment for invoice #${invoiceNumber}.`;
                        break;
                    case 'received':
                        subject = 'Payment Received';
                        message = `We have received your payment for invoice #${invoiceNumber}. Thank you!`;
                        break;
                    case 'overdue':
                        subject = 'Payment Overdue';
                        message = `Your payment for invoice #${invoiceNumber} is overdue. Please take action.`;
                        break;
                    case 'refund':
                        subject = 'Refund Generated';
                        message = `Your refund for invoice #${invoiceNumber} has been processed successfully.`;
                        break;
                    default:
                        subject = 'Invoice Notification';
                        message = `Dear ${customerName}, please review your invoice #${invoiceNumber}.`;
                }

                setTimeout(function () {
                    $('#subject').val(subject);
                    $('.summernote').summernote('code', message);
                    $('#emailbody').fadeIn();
                    $('#request').hide();
                }, 800);
            });

            // SMS modal
            $(document).on('click', '.sendsms', function () {
                const type = $(this).data('type') || 'notification';
                $('#smstype').val(type);
                $('#request_sms').show();
                $('#smsbody').hide();

                let message = '';

                const customerName = '<?= addslashes(htmlspecialchars($customer['name'] ?? 'Unknown Customer')) ?>';
                const invoiceNumber = '<?= addslashes(htmlspecialchars($invoice['invoice_number'] ?? 'N/A')) ?>';

                switch (type) {
                    case 'notification':
                        message = `Dear ${customerName}, your invoice is ready. Please find the details attached.`;
                        break;
                    case 'reminder':
                        message = `Reminder: Please make your payment for invoice #${invoiceNumber}.`;
                        break;
                    case 'received':
                        message = `Payment received for invoice #${invoiceNumber}. Thank you!`;
                        break;
                    case 'overdue':
                        message = `Payment overdue for invoice #${invoiceNumber}. Please take action.`;
                        break;
                    case 'refund':
                        message = `Refund processed for invoice #${invoiceNumber}.`;
                        break;
                    default:
                        message = `Dear ${customerName}, please review your invoice #${invoiceNumber}.`;
                }

                setTimeout(function () {
                    $('#sms_tem').val(message);
                    $('#smsbody').fadeIn();
                    $('#request_sms').hide();
                }, 800);
            });

            // Form submissions
            $('#sendbill').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: '/AIS/send-email',
                    method: 'POST',
                    data: formData,
                    success: function () {
                        $('#notify .message').html('<strong>Success</strong>: Email sent successfully!');
                        $('#notify').fadeIn();
                        setTimeout(() => $('#notify').fadeOut(), 3000);
                        $('#sendEmail').modal('hide');
                    },
                    error: function () {
                        $('#notify .message').html('<strong>Error</strong>: Failed to send email.');
                        $('#notify').fadeIn();
                        setTimeout(() => $('#notify').fadeOut(), 3000);
                    }
                });
            });

            $('#sendsms').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: '/AIS/send-sms',
                    method: 'POST',
                    data: formData,
                    success: function () {
                        $('#notify .message').html('<strong>Success</strong>: SMS sent successfully!');
                        $('#notify').fadeIn();
                        setTimeout(() => $('#notify').fadeOut(), 3000);
                        $('#sendSMS').modal('hide');
                    },
                    error: function () {
                        $('#notify .message').html('<strong>Error</strong>: Failed to send SMS.');
                        $('#notify').fadeIn();
                        setTimeout(() => $('#notify').fadeOut(), 3000);
                    }
                });
            });

            $('#form_model').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: '/AIS/invoices-update-status',
                    method: 'POST',
                    data: formData,
                    success: function () {
                        $('#notify .message').html('<strong>Success</strong>: Status updated successfully!');
                        $('#notify').fadeIn();
                        setTimeout(() => {
                            $('#notify').fadeOut();
                            location.reload();
                        }, 3000);
                        $('#pop_model').modal('hide');
                    },
                    error: function () {
                        $('#notify .message').html('<strong>Error</strong>: Failed to update status.');
                        $('#notify').fadeIn();
                        setTimeout(() => $('#notify').fadeOut(), 3000);
                    }
                });
            });

            $('#cancel_bill').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: '/AIS/invoices-status',
                    method: 'POST',
                    data: formData,
                    success: function () {
                        $('#notify .message').html('<strong>Success</strong>: Invoice cancelled successfully!');
                        $('#notify').fadeIn();
                        setTimeout(() => {
                            $('#notify').fadeOut();
                            location.reload();
                        }, 3000);
                        $('#cancel_bill').modal('hide');
                    },
                    error: function () {
                        $('#notify .message').html('<strong>Error</strong>: Failed to cancel invoice.');
                        $('#notify').fadeIn();
                        setTimeout(() => $('#notify').fadeOut(), 3000);
                    }
                });
            });


    <?php require __DIR__ . '/../../partials/footer.php'; ?>
</body>
</html>