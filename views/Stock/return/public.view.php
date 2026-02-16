<?php
/** @var array $return */
/** @var array $supplier */
/** @var array $items */
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
                  <div class="col-md-12 text-xs-right">
                    <div class="btn-group mt-2">
                      <button type="button" class="btn btn-primary btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-print"></i> Print return</button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" href="/AIS/return-print?id=<?= $return['id'] ?>" target="_blank">Print</a>
                        <a class="dropdown-item" href="/AIS/return-download?id=<?= $return['id'] ?>">PDF Download</a>
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
            <h2>return Order</h2>
              <p>PO #<?= htmlspecialchars($return['invoice_no']) ?></p>
              <p>Reference <?= htmlspecialchars($return['reference']) ?></p>
              <ul class="list-unstyled">
                <li>Gross Amount</li>
                <?php if($return['currency'] == 1){
                    $currencySymbol = '£';
                } elseif($return['currency'] == 2){
                    $currencySymbol = '€';
                    } else {
                    $currencySymbol = "₵";
                }
                ?>
                <li class="lead text-bold-800"><?= $currencySymbol ?> <?= number_format($return['grand_total'], 2) ?></li>
                
              </ul>
            </div>
          </div>

          <!-- Invoice Customer Details -->
          <div id="invoice-customer-details" class="row pt-2">
            <div class="col-md-4">
              <p class="text-muted">Bill To</p>
              <ul class="list-unstyled">
                <li class="text-bold-800"><a
                                            href="/AIS/supplier-view?id=<?= $return['supplier_id']  ?>"><strong
                                                class="invoice_a"><?= $supplier['name'] ?></strong>
                                            </a>
                                        </li>
                
                <li><?= $supplier['address'] ?></li>
                <li>Phone: <?= $supplier['phone'] ?></li>
                <li>Email: <?= $supplier['email'] ?></li>
              </ul>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-4">
              <p>Invoice Date: <?= date('d-m-Y', strtotime($return['invoice_date'])) ?></p>
              <p>Due Date: <?= date('d-m-Y', strtotime($return['due_date'])) ?></p>
               <?php if($return['payment_terms'] == 1){
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
                      <th>Amount</th>
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
                        <td><?= $currencySymbol ?> <?= number_format($item['discount'], 2) ?></td>
                        <td><?= $currencySymbol ?> <?= number_format($item['subtotal'], 2) ?></td>
                      </tr>
                    
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-md-7">
                <p class="lead">Payment Status: <strong><?= $return['payment_status'] ?? 'Unpaid' ?></strong></p>
                <p class="lead">Payment Method: <strong><?= $return['payment_method'] ?? '---' ?></strong></p>
                <p class="lead mt-1">Note:</p>
                <code><?= $return['notes'] ?? 'No notes provided.' ?></code>
              </div>
              <div class="col-md-5">
                <p class="lead">Summary</p>
                <table class="table">
                 <tr><td>Sub Total</td><td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($item['subtotal'], 2) ?></td></tr>
                  <tr><td>TAX</td><td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($item['tax_amount'], 2) ?></td></tr>
                  <tr><td>Discount</td><td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($item['discount'], 2) ?></td></tr>
                  <tr><td>Shipping</td><td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($return['shipping'], 2) ?></td></tr>
                  <tr class="bg-light"><td class="text-bold-800">Total</td><td class="text-bold-800 text-xs-right"><?= $currencySymbol ?> <?= number_format($return['grand_total'], 2) ?></td></tr>
                  <tr><td>Payment Made</td><td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($payment['amount'] ?? 0, 2) ?></td></tr>
                  <tr class="bg-grey bg-lighten-4"><td class="text-bold-800">Balance Due</td><td class="text-bold-800 text-xs-right"><?= $currencySymbol ?> <?= number_format(($return['grand_total'] - ($payment['amount'] ?? 0)), 2) ?></td></tr>
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
                <p><?= nl2br($return['terms'] ?? 'Payment Due On Receipt.') ?></p>
              </div>
            </div>
        </div>

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
</body>
</html>
