<?php
/** @var array $invoice */
/** @var array $customer */
/** @var array $items */

$publicUrl = "/AIS/invoice-view?id={$invoice['id']}&token={$invoice['public_token']}";
$stripeUrl = "/AIS/payment-stripe?invoice_id={$invoice['id']}&token={$invoice['public_token']}";
?>
<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notify = document.getElementById('notify');
            const message = notify.querySelector('.message');
            const contentBody = document.getElementById('content-body');
            message.innerHTML = <?= json_encode($_SESSION['success']) ?>;
            notify.style.display = 'block';
           contentBody.style.display = 'none';

                   setTimeout(function() {
            notifyDiv.style.display = 'none';
           contentBody.style.display = 'block';
        }, 3000);
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php include __DIR__ . '/../../partials/head.php'; ?>
<style>
    @media print {
  body * {
    visibility: hidden;
  }
  #invoice-section, #invoice-section * {
    visibility: visible;
  }
  #invoice-section {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  .no-print {
    display: none !important;
  }
}

</style>
<body data-open="click" data-menu="vertical-menu" data-col="1-column" class="vertical-layout vertical-menu 1-column  container boxed-layout">
  <span id="hdata" data-df="dd-mm-yyyy" data-curr="$"></span>
  <!-- main menu-->
  <!-- main menu header-->
  <!-- / main menu-->
 <div class="app-content content container-fluid">
  <div class="content-wrapper">

    <div id="notify" class="alert alert-success" style="display:none;">
      <a href="#" class="close" data-dismiss="alert">&times;</a>
      <div class="message"></div>
    </div>

    <div class="content-body" id="content-body">
      <section class="card">
        <div id="invoice-template" class="card-block">

        <div id="invoice-section"></div>
          <div class="row wrapper white-bg page-heading">
            <div class="col-lg-12">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group mt-2">
                    Payment:
                    <a class="btn btn-success btn-min-width mr-1" href="#" data-toggle="modal" data-target="#paymentCard"><i class="fa fa-cc"></i> Credit Card</a>
                    <a class="btn btn-cyan btn-min-width mr-1" href="/AIS/bank" target="_blank"><i class="icon-bank"></i> Bank / Cash</a>
                  </div>
                </div>
                <div class="col-md-4 text-xs-right">
                  <div class="btn-group mt-2">
                    <button  type="button" class="btn btn-primary btn-min-width dropdown-toggle" data-toggle="dropdown"><i class="icon-print"></i> Print Invoice </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="/AIS/invoice-generate?id=<?= $invoice['id'] ?>&token=<?= $invoice['public_token'] ?>" target="_blank">Print</a>
                        <a class="dropdown-item" href="/AIS/invoice-download?id=<?= $invoice['id']  ?>&token=<?= $invoice['public_token'] ?>">PDF Download</a>
                    </div>
                  </div>
                </div>
              </div>
              <hr>
            </div>
          </div>

          <!-- Invoice Company Details -->
          <div id="invoice-company-details" class="row mt-2">
                        <div class="col-md-6 col-sm-12 text-xs-center text-md-left"><p></p>
                            <!-- <img loading="lazy" src="Public/assets/img/logo.png"
                                 class="img-responsive p-1 m-b-2" style="max-height: 120px;"> -->
                                 <h1  id="logoText" class="brand-logo height-50 text-responsive p-1 m-b-2"><span style="color:yellow;font-weight: 900;font-family: system-ui;">DTT </span><span style="color:#3BAFDA;">EDGE</span></h1>
                            <p class="text-muted">From</p>
                            <ul class="px-0 list-unstyled">
                                <li class="text-bold-800">DAMMY TECH</li><li>K3 Dove Street, </li><li>Achimota Golf Hills,</li><li>Accra, Ghana </li><li> Phone : +233-598-238-797</li><li>  Email : info@dtt.com                               </li>
                            </ul>
                        </div>
                    

                   
            <div class="col-md-6 text-md-right">
              <h2>
              <?php if($invoice['type'] === 'recurring'): ?>  
                RECURRING
                  <?php endif; ?>
              INVOICE</h2>
              <p>SRN #<?= htmlspecialchars($invoice['invoice_number']) ?></p>
              <ul class="list-unstyled">
                <li>Gross Amount</li>
                <?php if($invoice['currency'] == 1){
                    $currencySymbol = '£';
                } elseif($invoice['currency'] == 2){
                    $currencySymbol = '€';
                    } else {
                    $currencySymbol = "₵";
                }
                ?>
                <li class="lead text-bold-800"><?= $currencySymbol ?> <?= number_format($calculatedGrandTotal, 2) ?></li>
                <?php if($invoice['type'] === 'recurring'): ?>
                           <li>Repeat on</li>
                <li class="lead text-bold-800"><?= $invoice['recurring_period'] ?></li>
                <?php endif; ?>
              </ul>
            </div>
          </div>

          <!-- Invoice Customer Details -->
          <div id="invoice-customer-details" class="row pt-2">
            <div class="col-md-4">
              <p class="text-muted">Bill To</p>
              <ul class="list-unstyled">
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
            <div class="col-md-4"></div>
            <div class="col-md-4">
              <p>Invoice Date: <?= date('d-m-Y', strtotime($invoice['invoice_date'])) ?></p>
              <p>Due Date: <?= date('d-m-Y', strtotime($invoice['due_date'])) ?></p>
               <?php if($invoice['payment_terms'] == 1){
                    $payment_terms = 'Payment Due On Receipt';
                } else {
                    $payment_terms= "Your payment terms";
                }
                ?>
              <p>Terms: <?= $payment_terms ?></p>
            </div>
          </div>

          <!-- Invoice Items -->
          <div id="invoice-items-details" class="pt-2">
            <div class="row">
              <div class="col-sm-12 table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                        <th>Product</th>
                      <th>Description</th>
                      <th>Rate</th>
                      <th>Qty</th>
                      <th>Tax</th>
                      <th>Discount</th>
                 
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($items as $index => $item): ?>
                      <tr>
                        <th><?= $index + 1 ?></th>
                        <td><?= $item['product_name'] ?></td>
                        <td><?= $item['product_description'] ?></td>
                        <td><?= $currencySymbol ?> <?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $currencySymbol ?> <?= number_format($item['tax_amount'], 2) ?> (<?= $item['tax_percent'] ?>%)</td>
                        <td><?= $currencySymbol ?> <?= number_format($item['discount_amount'], 2) ?>(<?= $item['discount'] ?>%)</td>
                        
                      </tr>
                    
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-7">
                <p class="lead">Payment Status: <strong><?= $invoice['payment_status'] ?? 'Unpaid' ?></strong></p>
                <p class="lead">Payment Method: <strong><?= $invoice['payment_method'] ?? '---' ?></strong></p>
                <?php if($invoice['type'] == 'recurring'): ?>
               <p class="lead">Invoice Status: <strong><?= $invoice['type'] ?? '---' ?></strong></p>
                  <?php endif; ?>
                <p class="lead mt-1">Note:</p>
                <code><?= $invoice['notes'] ?? 'No notes provided.' ?></code>
              </div>
              <div class="col-md-5">
                <p class="lead">Summary</p>
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
                <div class="text-xs-center">
                  <p>Authorized person</p>
                  <img loading="lazy" src="Public/assets/img/sign.png" alt="signature" class="height-100" />
                  <h6>(John Doe)</h6>
                  <p class="text-muted">Business Owner</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Credit Transactions (Optional Future Enhancement) -->
        <div id="invoice-footer">
            <p class="lead">Credit Transactions:</p>
            <table class="table table-striped">
              <thead>
                <tr><th>Date</th><th>Method</th><th>Amount</th><th>Note</th></tr>
              </thead>
              <tbody id="activity">
                <!-- You can populate with actual credit transaction logs if available -->
              </tbody>
            </table>
            <div class="row">
              <div class="col-md-7">
                <h6>Terms & Condition</h6>
                <p><?= nl2br($invoice['terms'] ?? 'Payment Due On Receipt.') ?></p>
              </div>
            </div>
        </div>

        </div>
      </section>
    </div>

  </div>
</div>


  <div id="paymentCard" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Make Payment</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
<form action="/AIS/stripe-checkout" method="POST">
  <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">
  <input type="hidden" name="token" value="<?= $invoice['public_token'] ?>">
  <button type="submit" class="btn btn-block btn-primary">
    <span class="display-block">Pay with <strong>Stripe</strong> (<?= $currencySymbol ?> <?= number_format($calculatedGrandTotal, 2) ?>+3%)</span>
 <img loading="lazy" src="Public/assets/img/stripe-logo.png" class="mt-1 bg-white round" style="max-width:10rem;max-height:5rem">
  </button>
</form>

<form action="/AIS/paystack-checkout" method="POST">
  <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?>">
  <input type="hidden" name="token" value="<?= $invoice['public_token'] ?>">
  <button type="submit" class="btn mb-1 btn-block blue rounded border border-info">
    <span class="display-block"><span class="grey">Pay With </span> <strong>PayStack</strong> (<?= $currencySymbol ?> <?= number_format($calculatedGrandTotal, 2) ?>+3%)</span>
<img loading="lazy" src="Public/assets/img/paystack-logo.png" class="mt-1 bg-white round" style="max-width:20rem;max-height:10rem">
  </button>
</form>
          <br>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
</body>
</html>
