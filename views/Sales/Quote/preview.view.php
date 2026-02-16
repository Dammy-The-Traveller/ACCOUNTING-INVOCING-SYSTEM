<?php
/** @var array $quotes */
/** @var array $quoteId */
/** @var array $customer */
/** @var array $quoteItems */
?>
<?php if (isset($_SESSION['success'])): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const notify = document.getElementById('notify');
    const message = notify.querySelector('.message');
    const contentBody = document.getElementById('content-body');
    message.innerHTML = <?= json_encode($_SESSION['success']) ?> ;
    notify.style.display = 'block';
    contentBody.style.display = 'none';
  });
</script>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php include __DIR__ . '/../../partials/head.php'; ?>
<body data-open="click" data-menu="vertical-menu" data-col="1-column" class="vertical-layout vertical-menu 1-column  container boxed-layout"><span id="hdata" data-df="dd-mm-yyyy" data-curr="$"></span>
  <!-- ////////////////////////////////////////////////////////////////////////////-->
  <!-- main menu-->
  <!-- main menu header-->
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
                <div class="row">
                  <div class="col-md-12 text-xs-right">
                    <div class="btn-group mt-2">
                      <button type="button" class="btn btn-primary btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-print"></i> Print Quote </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="/AIS/quote-generate?id=<?= $quotes['id'] ?>&token=<?= $quotes['public_token'] ?>" target="_blank">Print</a>
                        <a class="dropdown-item" href="/AIS/quote-download?id=<?= $quotes['id']  ?>&token=<?= $quotes['public_token'] ?>">PDF Download</a>
                      </div>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="title-action ">
                </div>
              </div>
            </div>
           <!-- Invoice Company Details -->
            <div id="invoice-company-details" class="row mt-2">
              <div class="col-md-6 col-sm-12 text-xs-center text-md-left">
                <p></p>
                <h1  id="logoText" class="brand-logo height-50 text-responsive p-1 m-b-2"><span style="color:yellow;font-weight: 900;font-family: system-ui;">DTT</span><span style="color:#3BAFDA;">EDGE</span></h1>
                   <p class="text-muted">From</p>
                <ul class="px-0 list-unstyled">
                  <li class="text-bold-800">DAMMY TECH</li>
                  <li>K3 Dove Street,</li>
                  <li>Achimota Golf Hills,</li>
                  <li>Accra, Ghana - </li>
                  <li>Phone : +233-598-238-797</li>
                  <li>Email : info@dtt.com   </li>
                </ul>
              </div>
              <div class="col-md-6 col-sm-12 text-xs-center text-md-right">
                <h2>Quote</h2>
                <p class="pb-1"> QT#<?= htmlspecialchars($quotes['quote_number']) ?></p>
                <p class="pb-1">Reference:<?= htmlspecialchars($quotes['reference']) ?></p>
                <ul class="px-0 list-unstyled">
                  <li>Gross Amount</li>
                  <li class="lead text-bold-600">
                  <?php if($quotes['currency'] == 1){
                    $currencySymbol = '£';
                } elseif($quotes['currency'] == 2){
                    $currencySymbol = '€';
                    } else {
                    $currencySymbol = "₵";
                }
                ?>  
                  <?= $currencySymbol ?> <?= number_format($quotes['grand_total'], 2) ?></li>
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
                  <li class="text-bold-600">
                    <a href="/AIS/customer-view?id=<?= $quotes['customer_id']  ?>"><strong class="invoice_a"><?= $customer['name'] ?></strong>
                  </a>
                  </li>
                  <li></li>
                  <li><?= $customer['address'] ?></li>
                  <li><?= $customer['city'] ?>,<?= $customer['region'] ?></li>
                  <li><?= $customer['country'] ?></li>
                  <li> Phone: <?= $customer['phone'] ?></li>
                  <li> Email: <?= $customer['email'] ?> </li>
                </ul>
              </div>
              <div class="offset-md-3 col-md-3 col-sm-12 text-xs-center text-md-left">
                <p><span class="text-muted">Quote Date :</span> <?= date('d-m-Y', strtotime($quotes['quote_date'])) ?></p>
                <p><span class="text-muted">Due Date :</span> <?= date('d-m-Y', strtotime($quotes['due_date'])) ?></p>
                <?php if($quotes['payment_terms'] == 1){
                    $payment_terms = 'Payment Due On Receipt';
                } else {
                    $payment_terms= "Your payment terms";
                }
                ?>
                <p><span class="text-muted">Terms :</span> <?= $payment_terms ?></p>
              </div>
            </div>
            <!--/ Invoice Customer Details -->
            <div id="invoice-customer-details" class="row pt-2">
              <div class="col-sm-12 text-xs-center text-md-left">
                <h5>Proposal</h5>
                <p>
                <p><?= $quotes['proposal'] ?></p>
                </p>
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
                        <th class="text-xs-left"> Discount</th>
                 
                      </tr>
                    </thead>
                    <?php foreach ($quoteItems as $index => $item): ?>
                      <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= $item['product_name'] ?></td>
                        <td><?= $item['product_description'] ?></td>
                        <td><?= $currencySymbol ?> <?= number_format($item['rate'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $currencySymbol ?> <?= number_format($item['tax_amount'], 2) ?> (<?= $item['tax_percent'] ?>%)</td>
                         <td><?= $currencySymbol ?> <?= number_format($item['discount_amount'], 2) ?>(<?= $item['discount'] ?>%)</td>
                      </tr>
                      <tr><td colspan="5"></td>
                    </tr>
                    <?php endforeach; ?>
                
                  </table>
                </div>
              </div>
              <p></p>
              <div class="row">
                <div class="col-md-7 col-sm-12 text-xs-center text-md-left">
                  <div class="row">
                    <div class="col-md-8">
                      <p class="lead">Status: <u><strong id="pstatus"><?= $quotes['status'] ?? 'Pending' ?></strong></u>
                      </p>
                      <p class="lead mt-1"><br>Note:</p>
                      <code>
                        <?= $quotes['note'] ?? 'No note' ?>
                      </code>
                    </div>
                  </div>
                </div>
                <div class="col-md-5 col-sm-12">
                  <p class="lead">Total Due</p>
                  <div class="table-responsive">
                    <table class="table">
                      <tbody>
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
                                                <td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($quotes['shipping'], 2) ?></td>
                                            </tr>
                                            <tr class="bg-light">
                                                <td class="text-bold-800">Total</td>
                                                <td class="text-bold-800 text-xs-right"><?= $currencySymbol ?> <?= number_format($calculatedGrandTotal, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Payment Made</td>
                                                <td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($quotes['amount_paid'] ?? 0, 2) ?></td>
                                            </tr>
                                            <tr class="bg-grey bg-lighten-4">
                                                <td class="text-bold-800">Balance Due</td>
                                                <td class="text-bold-800 text-xs-right"><?= $currencySymbol ?> <?= number_format(($calculatedGrandTotal - ($quotes['amount_paid'] ?? 0)), 2) ?></td>
                                            </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="text-xs-center">
                    <p>Authorized person</p>
                    <img loading="lazy" src="Public/assets/img/sign.png" alt="signature" class="height-100" />
                    <h6>(John Doe)</h6>
                    <p class="text-muted">Business Owner</p>
                  </div>
                </div>
              </div>
            </div>
            <!-- Invoice Footer -->
            <div id="invoice-footer">
              <div class="row">
                <div class="col-md-7 col-sm-12">
                  <h6>Terms & Condition</h6>
                  <p> <strong><?= $payment_terms ?? 'Payment Due On Receipt.' ?></strong><br>
                  <div><b>1. Prices And Payment</b></div>
                  <div><span style="font-size: 1rem;">Payments are to be made in U.S funds. Unless otherwise specified all quotes are due net 30&nbsp;</span><span style="font-size: 1rem;">&nbsp;</span><span style="font-size: 1rem;">days from date of Shipment.</span></div>
                  </p>
                </div>
              </div>
            </div>
            <!-- Invoice Footer -->
          </div>
        </section>
      </div>
    </div>
  </div>
    <script src="Public/assets/myjs/jquery-ui.js"></script>
<script src="Public/assets/vendor/js/ui/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/unison.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/blockUI.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/jquery.matchHeight-min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/screenfull.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/extensions/pace.min.js" type="text/javascript"></script>
<?php require __DIR__ . '/../../partials/footer.php'; ?>
</body>
</html>