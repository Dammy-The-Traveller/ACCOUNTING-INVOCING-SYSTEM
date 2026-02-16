<?php
/** @var array $invoice */
/** @var array $customer */
/** @var array $items */
?>
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
<?php include __DIR__ . '/../../partials/head.php'; ?>

<body data-open="click" data-menu="vertical-menu" data-col="2-columns"
      class="vertical-layout vertical-menu 2-columns  fixed-navbar"><span id="hdata"
                                                                          data-df="yyyy-mm-dd"
                                                                          data-curr="$"></span>

<!-- navbar-fixed-top-->
<?php include __DIR__ . '/../../partials/navbar.php'; ?>

<!-- ////////////////////////////////////////////////////////////////////////////-->


<!-- main menu-->
<?php include __DIR__ . '/../../partials/Sidenav.php'; ?>
<!-- / main menu-->
 <div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>

        <div class="content-body">
            <section class="card">
                <div id="invoice-template" class="card-block">
                    <div class="row wrapper white-bg page-heading">

                        <div class="col-lg-12">
                          <div class="title-action">

                                <?php if($invoice['status'] !== 'cancelled'): ?>
                                <a href="/AIS/recur-create?id=<?= $invoice['id'] ?>" class="btn btn-warning mb-1">
                          <i class="icon-pencil"></i> Edit Invoice
                       </a>


                                <a href="#part_payment" data-toggle="modal" data-remote="false" data-type="reminder"
                                   class="btn btn-large btn-success mb-1" title="Partial Payment"
                                ><span class="icon-money"></span> Make Payment </a>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle mb-1"
                                            data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                            <span class="icon-envelope-o"></span> 
                            Email
                                    </button>
                                    <div class="dropdown-menu"><a href="#sendEmail" data-toggle="modal"
                                                                  data-remote="false" class="dropdown-item sendbill"
                                                                  data-type="notification">Invoice Notification</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#sendEmail" data-toggle="modal" data-remote="false"
                                           class="dropdown-item sendbill"
                                           data-type="reminder">Payment Reminder</a>
                                        <a
                                                href="#sendEmail" data-toggle="modal" data-remote="false"
                                                class="dropdown-item sendbill"
                                                data-type="received">Payment Received</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#sendEmail" data-toggle="modal" data-remote="false"
                                           class="dropdown-item sendbill" href="#"
                                           data-type="overdue">Payment Overdue</a><a
                                                href="#sendEmail" data-toggle="modal" data-remote="false"
                                                class="dropdown-item sendbill"
                                                data-type="refund">Refund Generated</a>
                                    </div>
                                </div>

                                <!-- SMS -->
                                <div class="btn-group">
                                    <button type="button" class="btn btn-blue dropdown-toggle mb-1"
                                            data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <span
                                    class="icon-envelope-o"></span> SMS
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="#sendSMS" data-toggle="modal"
                                                                  data-remote="false" class="dropdown-item sendsms"
                                                                  data-type="notification">Invoice Notification</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#sendSMS" data-toggle="modal" data-remote="false"
                                           class="dropdown-item sendsms"
                                           data-type="reminder">Payment Reminder</a>
                                        <a
                                                href="#sendSMS" data-toggle="modal" data-remote="false"
                                                class="dropdown-item sendsms"
                                                data-type="received">Payment Received</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#sendSMS" data-toggle="modal" data-remote="false"
                                           class="dropdown-item sendsms" href="#"
                                           data-type="overdue">Payment Overdue</a><a
                                                href="#sendSMS" data-toggle="modal" data-remote="false"
                                                class="dropdown-item sendbill"
                                                data-type="refund">Refund Generated</a>

                                    </div>

                                </div>

                                <div class="btn-group ">
                                    <button type="button" class="btn btn-success mb-1 btn-min-width dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                class="icon-print"></i> Print                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                           href="/AIS/invoice-generate?id=<?= $invoice['id'] ?>&token=<?= $invoice['public_token'] ?>">Print</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item"
                                           href="/AIS/invoice-download?id=<?= $invoice['id']  ?>&token=<?= $invoice['public_token'] ?>">PDF Download</a>

                                    </div>
                                </div>

                                <a href="/AIS/invoice-view?id=<?= $invoice['id']?>&token=<?= $invoice['public_token']?>" class="btn btn-brown mb-1"><i
                                            class="icon-earth"></i> Preview                                
                                </a>

                                <a href="#pop_model" data-toggle="modal" data-remote="false"
                                   class="btn btn-large btn-cyan mb-1" title="Change Status"
                                ><span class="icon-tab"></span> Change Payment Status
                                </a>
                                 <a href="#pop_model2" data-toggle="modal" data-remote="false"
                                   class="btn btn-large btn-cyan mb-1" title="Change Status"
                                ><span class="icon-tab"></span> Change Recurring Status
                                </a>
                          
                               <a href="#cancel_bill" class="btn btn-danger mb-1" id="cancel-bill" data-toggle="modal"><i
                                            class="icon-minus-circle"> </i> Cancel                                
                                </a>
                            </div>   
                            <?php else: ?>
                              <button class="btn btn-danger mb-1"><i
                                            class="icon-minus-circle"> </i> Cancelled                                
                                        </button>
                            <?php endif; ?>                     
                        </div>
                    </div>

                    <!-- Invoice Company Details -->
                    <div id="invoice-company-details" class="row mt-2">
                        <div class="col-md-6 col-sm-12 text-xs-center text-md-left"><p></p>
                           <h1  id="logoText" class="brand-logo height-50 text-responsive p-1 m-b-2"><span style="color:yellow;font-weight: 900;font-family: system-ui;">DTT </span><span style="color:#3BAFDA;">EDGE</span></h1>
                        </div>
                        <div class="col-md-6 col-sm-12 text-xs-center text-md-right">
                            <h2>RECURRING INVOICE</h2>
                            <p class="pb-1"> SRN #<?= htmlspecialchars($invoice['invoice_number']) ?></p>
                                                  <ul class="px-0 list-unstyled">
                                <li>Gross Amount</li>
                <?php if($invoice['currency'] == 1){
                    $currencySymbol = '£';
                } elseif($invoice['currency'] == 2){
                    $currencySymbol = '€';
                    } else {
                    $currencySymbol = "₵";
                }
                ?>
                <li class="lead text-bold-800"><?= $currencySymbol ?> <?= number_format($invoice['grand_total'], 2) ?></li>
                    <li>Repeat on</li>
                <li class="lead text-bold-800"><?= $invoice['recurring_period'] ?></li>
                            </ul>
                        </div>
                    </div>
                    <!--/ Invoice Company Details -->

                    <!-- Invoice Customer Details -->
                    <div id="invoice-customer-details" class="row">
                        <div class="col-sm-12 text-xs-center text-md-left">
                            <p class="text-muted"> Bill To</p>
                        </div>
                        <div class="col-md-6 col-sm-12 text-xs-center text-md-left">
                            <ul class="px-0 list-unstyled">
                                <li class="text-bold-800"><a
                                            href="/AIS/customer-view?id=<?= $invoice['customer_id']  ?>"><strong
                                                class="invoice_a"><?= $customer['name'] ?></strong>
                                            </a>
                                        </li>
                <li><?= $customer['address'] ?></li>
                <li>Phone: <?= $customer['phone'] ?></li>
                <li>Email: <?= $customer['email'] ?></li>
                            </ul>

                        </div>
                        <div class="offset-md-3 col-md-3 col-sm-12 text-xs-center text-md-left">
                            <p><span class="text-muted">Invoice Date  :</span>  <?= date('d-m-Y', strtotime($invoice['invoice_date'])) ?></p> <p><span class="text-muted">Due Date :</span> <?= date('d-m-Y', strtotime($invoice['due_date'])) ?></p>  
                             <?php if($invoice['payment_terms'] == 1){
                    $payment_terms = 'Payment Due On Receipt';
                } else {
                    $payment_terms= "Your payment terms";
                }
                ?>
                            <p><span class="text-muted">Terms :</span> <?= $payment_terms ?></p>                        </div>
                    </div>
                    <!--/ Invoice Customer Details -->

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
                                        <th class="text-xs-left"> Discount</th>
                                       
                                    </tr>
                                    </thead>
                                       <tbody>
                    <?php foreach ($items as $index => $item): ?>
                      <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= $item['product_name'] ?></td>
                        <td><?= $item['product_description'] ?></td>
                        <td><?= $currencySymbol ?> <?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $currencySymbol ?> <?= number_format($item['tax_amount'], 2) ?> (<?= $item['tax_percent'] ?>%)</td>
                        <td><?= $currencySymbol ?> <?= number_format($item['discount'], 2) ?></td>
                       
                      </tr>
                      <tr><td colspan=5></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                                </table>
                            </div>
                        </div>
                        <p></p>
                        <div class="row">
                            <div class="col-md-7 col-sm-12 text-xs-center text-md-left">


                                <div class="row">
                                    <div class="col-md-8"><p
                                                class="lead">Payment Status:
                                            <u><strong
                                                        id="pstatus"><?= $invoice['payment_status'] ?? 'Unpaid' ?></strong></u>
                                        </p>
                                         <p class="lead">Invoice Status: <u><strong id="pmethod"><?= $invoice['status'] ?? '---' ?></strong></u>
                                        </p>
                                        <p class="lead">Payment Method: <u><strong id="pmethod"><?= $invoice['payment_method'] ?? '---' ?></strong></u>
                                        </p>

                                        <p class="lead mt-1"><br>Note:</p>
                                        <code>
                             <?= $invoice['notes'] ?? 'No notes provided.' ?>                                                       </code>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-12">
                                <p class="lead">Summary</p>
                                <div class="table-responsive">
                              <table class="table">
                 <tr>
                                                <td>Sub Total</td>
                                                <td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($calculatedSubtotal, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>TAX</td>
                                                <td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($calculatedTax, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Discount</td>
                                                <td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($calculatedDiscount, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Shipping</td>
                                                <td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($invoice['shipping'], 2) ?></td>
                                            </tr>
                                            <tr class="bg-light">
                                                <td class="text-bold-800">Total</td>
                                                <td class="text-bold-800 text-xs-right"><?= $currencySymbol ?> <?= number_format($calculatedGrandTotal, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Payment Made</td>
                                                <td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($invoice['amount_paid'] ?? 0, 2) ?></td>
                                            </tr>
                                            <tr class="bg-grey bg-lighten-4">
                                                <td class="text-bold-800">Balance Due</td>
                                                <td class="text-bold-800 text-xs-right"><?= $currencySymbol ?> <?= number_format(($calculatedGrandTotal - ($invoice['amount_paid'] ?? 0)), 2) ?></td>
                                            </tr>
                </table>
                                </div>
                                <div class="text-xs-center">
                                    <p>Authorized person</p>
                                    <img loading="lazy" src="Public/assets/img/sign.png" alt="signature" class="height-100"/>
                                    <h6>(Harry McGaughey)</h6>
                                    <p class="text-muted">Sales Manager</p>                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Footer -->

                    <div id="invoice-footer"><p class="lead">Credit Transactions:</p>
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
                            
                            </tbody>
                        </table>

                        <div class="row">

                            <div class="col-md-7 col-sm-12">

                                <h6>Terms & Condition</h6>
                                <p> <strong><?= nl2br($invoice['terms'] ?? 'Payment Due On Receipt.') ?></strong><br><div><b>1. Prices And Payment</b></div><div><span style="font-size: 1rem;">Payments are to be made in U.S funds. Unless otherwise specified all invoices are due net 30&nbsp;</span><span style="font-size: 1rem;">&nbsp;</span><span style="font-size: 1rem;">days from date of Shipment.</span></div></p>
                            </div>

                        </div>

                    </div>
                    <!--/ Invoice Footer -->
                    <hr>
                  
                </div>
            </section>
        </div>
    </div>
</div>



<!-- Modal HTML -->
<div id="part_payment" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Payment Confirmation</h4>
            </div>

            <div class="modal-body">
                <form class="payment">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="input-group">
                                <div class="input-group-addon"><?=  $currencySymbol ?></div>
                                <input type="text" class="form-control" placeholder="Total Amount" name="amount"
                                       id="rmpay" value="<?= $invoice['grand_total']  ?>">
                            </div>

                        </div>
                        <div class="col-xs-6">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="icon-calendar4"
                                                                     aria-hidden="true"></span></div>
                                <input type="text" class="form-control required"
                                       placeholder="Billing Date" name="paydate"
                                       data-toggle="datepicker">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 mb-1"><label
                                    for="pmethod">Payment Method</label>
                            <select name="pmethod" class="form-control mb-1">
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="Balance">Client Balance</option>
                                <option value="Bank">Bank</option>
                            </select><label for="account">Account</label>

                            <select name="account" class="form-control">
                                <option value="1">Company Sales Account / 12345678</option><option value="6">Purchase A/C / 1234567890</option>                            </select></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 mb-1"><label
                                    for="shortnote">Note</label>
                            <input type="text" class="form-control"
                                   name="shortnote" placeholder="Short note"
                                   value="Payment for invoice <?= $invoice['id']  ?>"></div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" class="form-control required"
                               name="tid" id="invoiceid" value="1083">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"> Close</button>
                        <input type="hidden" name="cid" value="54"><input type="hidden"
                                                                                                     name="cname"
                                                                                                     value="Jere Swayne">
                        <button type="button" class="btn btn-primary"
                                id="submitpayment">Make Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- cancel -->
<div id="cancel_bill" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Cancel Invoice</h4>
            </div>
            <div class="modal-body">
                <form class="cancelbill" action="/AIS/RecurringUpdate-status" method="POST">
                       <input type="hidden" class="form-control required" name="id" id="invoiceid" value="<?= $invoice['id']  ?>">
                    <div class="row">
                        <div class="col-xs-12">
                            You can not revert this action! Are you sure?
                            <input type="hidden" class="form-control required" name="status" id="invoiceid" value="cancelled">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-primary"
                                id="send">Cancel Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal HTML -->
<div id="sendEmail" class="modal fsms
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Email</h4>
            </div>
            <div id="request">
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
            <div class="modal-body" id="emailbody" style="display: none;">
                <form id="sendbill">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="icon-envelope-o"
                                                                     aria-hidden="true"></span></div>
                                <input type="text" class="form-control" placeholder="Email" name="mailtoc"
                                       value="jswayne1h@blogs.com">
                            </div>

                        </div>

                    </div>


                    <div class="row">
                        <div class="col-xs-12 mb-1"><label
                                    for="shortnote">Customer Name</label>
                            <input type="text" class="form-control"
                                   name="customername" value="Jere Swayne"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 mb-1"><label
                                    for="shortnote">Subject</label>
                            <input type="text" class="form-control"
                                   name="subject" id="subject">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 mb-1"><label
                                    for="shortnote">Message</label>
                            <textarea name="text" class="summernote" id="contents" title="Contents"></textarea>
                        </div>
                    </div>

             
                    <input type="hidden" class="form-control"
                           id="invoiceid" name="tid" value="1083">
                    <input type="hidden" class="form-control"
                           id="emailtype" value="">


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"> Close</button>
                <button type="button" class="btn btn-primary" id="sendM">Send</button>
            </div>
        </div>
    </div>
</div>
<!--sms-->

<!-- Modal HTML -->
<div id="sendSMS" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Send SMS</h4>
            </div>
            <div id="request_sms">
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
            <div class="modal-body" id="smsbody" style="display: none;">
                <form id="sendsms">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="icon-envelope-o"
                                                                     aria-hidden="true"></span></div>
                                <input type="text" class="form-control" placeholder="SMS" name="mobile"
                                       value="687-699-1265">
                            </div>

                        </div>

                    </div>


                    <div class="row">
                        <div class="col-xs-12 mb-1"><label
                                    for="shortnote">Customer Name</label>
                            <input type="text" class="form-control"
                                   value="Jere Swayne"></div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 mb-1"><label
                                    for="shortnote">Message</label>
                            <textarea class="form-control" name="text_message" id="sms_tem" title="Contents"
                                      rows="3"></textarea></div>
                    </div>


                    <input type="hidden" class="form-control"
                           id="smstype" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"> Close</button>
                <button type="button" class="btn btn-primary"
                        id="submitSMS">Send</button>
            </div>
        </div>
    </div>
</div>

<div id="pop_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Change Status</h4>
            </div>
            <div class="modal-body">
                <form id="form_model" action="/AIS/recur-update-status" method="POST">
                      <input type="hidden" class="form-control required" name="tid" id="invoiceid" value="<?= $invoice['id']  ?>">
                    <div class="row">
                        <div class="col-xs-12 mb-1"><label
                                    for="pmethod">Mark As</label>
                            <select name="status" class="form-control mb-1">
                                <option value="paid">Paid</option>
                                <option value="due">Due</option>
                                <option value="partial">Partial</option>
                            </select>

                        </div>
                    </div>

                    <div class="modal-footer">
                      
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"> Close</button>
                        
                        <button type="submit" class="btn btn-primary">Change Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="pop_model2" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Recurring</h4>
            </div>

            <div class="modal-body">
                 <form id="form_model" action="/AIS/RecurringUpdate-status" method="POST">
    <input type="hidden" class="form-control required" name="id" id="invoiceid" value="<?= $invoice['id']  ?>">

                    <div class="row">
                        <div class="col-xs-12 mb-1"><label
                                    for="pmethod">Mark As</label>
                            <select name="status" class="form-control mb-1">
                                <option value="active">(On) Recurring</option>
                                <option value="paused">(Off) Stop</option>
                            </select>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="hidden" class="form-control required"
                               name="tid" id="invoiceid" value="">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"> Close</button>
                        <input type="hidden" id="action-url" value="rec_invoices/rec_status">
                        <button type="submit" class="btn btn-primary"
                                id="submit_model2">Change Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    $(function () {
        $('.summernote').summernote({
            height: 150,
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

  

         $('#sendM').click(function() {
                var formData = $('#sendbill').serialize();
                console.log('Form data:', formData);
                // Display success message
                $('#notify .message').html('<strong>Success</strong>: Email Sent Successfully!. This feature is disabled in the Demo Mode.');
                $('#notify').fadeIn();
                // Automatically hide the alert after 3 seconds
                setTimeout(function() {
                    $('#notify').fadeOut();
                }, 3000);
                // Close the modal
                $('#sendEmail').modal('hide');
            });

                 $('#submitSMS').click(function() {
                var formData = $('#sendSMS').serialize();
                console.log('Form data:', formData);
                // Display success message
                $('#notify .message').html('<strong>Success</strong>: Message Sent Successfully!. This feature is disabled in the Demo Mode.');
                $('#notify').fadeIn();
                // Automatically hide the alert after 3 seconds
                setTimeout(function() {
                    $('#notify').fadeOut();
                }, 3000);
                // Close the modal
                $('#sendSMS').modal('hide');
            });

         

    $('#submitpayment').click(function() {
        var formData = $('.payment').serialize(); // target the correct form
        console.log('Form data:', formData);

        // Display success message
        $('#notify .message').html('<strong>Success</strong>: Payment made Successfully! This feature is disabled in Demo Mode.');
        $('#notify').fadeIn();

        // Automatically hide the alert after 3 seconds
        setTimeout(function() {
            $('#notify').fadeOut();
        }, 3000);

        // Close the modal
        $('#part_payment').modal('hide');
    });


    });
</script>
<script>
    $(document).on('click', '.sendbill', function () {
    let type = $(this).data('type');
    $('#emailtype').val(type);
    $('#request').show();
    $('#emailbody').hide();

    let subject = '';
    let message = '';

    switch (type) {
        case 'notification':
            subject = 'Your Invoice is Ready';
            message = 'Dear customer, your invoice is ready. Please find the details attached.';
            break;
        case 'reminder':
            subject = 'Payment Reminder';
            message = 'This is a friendly reminder to make your payment.';
            break;
        case 'received':
            subject = 'Payment Received';
            message = 'We have received your payment. Thank you!';
            break;
        case 'overdue':
            subject = 'Payment Overdue';
            message = 'Your payment is overdue. Please take action.';
            break;
        case 'refund':
            subject = 'Refund Generated';
            message = 'Your refund has been processed successfully.';
            break;
    }

    setTimeout(function () {
        $('#subject').val(subject);
        $('#contents').val(message);
        $('#emailbody').fadeIn();
        $('#request').hide();
    }, 800);
});
</script>
<script>
            $(document).ready(function() {
            // Handle dropdown item clicks
            $(document).on('click', '.sendsms', function() {
                var type = $(this).data('type');
                $('#smstype').val(type);

                $('#request_sms').show();
                $('#smsbody').hide();

                var message = '';

                switch (type) {
                    case 'notification':
                        message = 'Dear customer, your invoice is ready. Please find the details attached.';
                        break;
                    case 'reminder':
                        message = 'This is a friendly reminder to make your payment.';
                        break;
                    case 'received':
                        message = 'We have received your payment. Thank you!';
                        break;
                    case 'overdue':
                        message = 'Your payment is overdue. Please take action.';
                        break;
                    case 'refund':
                        message = 'Your refund has been processed successfully.';
                        break;
                }

                setTimeout(function() {
                    $('#sms_tem').val(message);
                    $('#smsbody').fadeIn();
                    $('#request_sms').hide();
                }, 800);
            });
        });

</script>

<!-- BEGIN VENDOR JS-->
   <!-- Summernote JS -->

<?php require __DIR__ . '/../../partials/footer.php'; ?>

</body>
</html>
