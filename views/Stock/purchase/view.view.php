<?php include __DIR__ . '/../../partials/head.php'; ?>
<?php if (isset($_SESSION['success'])):  ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const notify = document.getElementById('notify');
    const message = notify.querySelector('.message');
    const form = document.getElementById('data_form');
    message.innerHTML = <?= json_encode($_SESSION['success']) ?> ;
    notify.style.display = 'block';
    form.style.display = 'none';
  });
</script>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<body data-open="click" data-menu="vertical-menu" data-col="2-columns" class="vertical-layout vertical-menu 2-columns  fixed-navbar">
  <span id="hdata" data-df="dd-mm-yyyy" data-curr="$"></span>
  <!-- navbar-fixed-top-->
  <?php include __DIR__ . '/../../partials/navbar.php'; ?>
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
                <?php if($purchase['status'] !== 'cancelled'): ?>
                  <a href="/AIS/purchase?id=<?= $purchase['id'] ?>" class="btn btn-warning"><i class="icon-pencil"></i> Edit Order </a>
                  <a href="#part_payment" data-toggle="modal" data-remote="false" data-type="reminder" class="btn btn-large btn-success" title="Partial Payment"><span class="icon-money"></span> Make Payment </a>
                  <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="icon-envelope-o"></span> Send </button>
                    <div class="dropdown-menu"><a href="#sendEmail" data-toggle="modal" data-remote="false" class="dropdown-item sendbill" data-type="purchase">Purchase Request</a>
                    </div>
                  </div>
                  <div class="btn-group ">
                    <button type="button" class="btn btn-success btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-print"></i> Print Order </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="/AIS/purchase-print?id=<?= $purchase['id'] ?>">Print</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="/AIS/purchase-download?id=<?= $purchase['id'] ?>">PDF Download</a>
                    </div>
                  </div>
                  <a href="/AIS/public-purchase?id=<?= $purchase['id'] ?>" class="btn btn-primary"><i class="icon-earth"></i> Public Preview </a>
                  <a href="#pop_model" data-toggle="modal" data-remote="false" class="btn btn-large btn-success" title="Change Payment Status"><span class="icon-tab"></span> Change Payment Status</a>
                  <a href="#pop_model2" data-toggle="modal" data-remote="false" class="btn btn-large btn-danger" title="Change Status"><span class="icon-tab"></span> Change Status</a>
                  
                
                <?php else: ?>
                    <button class="btn btn-danger mb-1"><i
                                            class="icon-minus-circle"> </i> Cancelled                                
                                        </button>
                            <?php endif; ?> 
                </div>
              </div>
            </div>
            <!-- Invoice Company Details -->
            <div id="invoice-company-details" class="row mt-2">
              <div class="col-md-6 col-sm-12 text-xs-center text-md-left">
              <h1  id="logoText" class="brand-logo height-50 text-responsive p-1 m-b-2"><span style="color:yellow;font-weight: 900;font-family: system-ui;">DTT </span><span style="color:#3BAFDA;">EDGE</span></h1>
              </div>
              <div class="col-md-6 col-sm-12 text-xs-center text-md-right">
                <h2>Purchase Order</h2>
                <p>PO #<?= htmlspecialchars($purchase['invoice_no']) ?></p>
                <p>Reference <?= htmlspecialchars($purchase['reference']) ?></p>
                <ul class="px-0 list-unstyled">
                <li>Gross Amount</li>
                <?php if($purchase['currency'] == 1){
                    $currencySymbol = '£';
                } elseif($purchase['currency'] == 2){
                    $currencySymbol = '€';
                    } else {
                    $currencySymbol = "₵";
                }
                ?>
                <li class="lead text-bold-800"><?= $currencySymbol ?> <?= number_format($purchase['grand_total'], 2) ?></li>
                </ul>
              </div>
            </div>
            <!--/ Invoice Company Details -->
            <!-- Invoice Customer Details -->
            <div id="invoice-customer-details" class="row pt-2">
              <div class="col-sm-12 text-xs-center text-md-left">
                <p class="text-muted">Bill From</p>
              </div>
              <div class="col-md-6 col-sm-12 text-xs-center text-md-left">
                <ul class="px-0 list-unstyled">
                  <li class="text-bold-800"><a href="/AIS/supplier-view?id=<?= $purchase['supplier_id']  ?>"><strong
                  class="invoice_a"><?= $supplier['name'] ?></strong></a></li>
                  <li><?= $supplier['address'] ?></li>
                  <li><?= $supplier['region'] ?>, <?= $supplier['country'] ?></li>
                  <li> Phone:<?= $supplier['phone'] ?></li>
                  <li> Email: <?= $supplier['email'] ?> </li>
                </ul>
              </div>
              <div class="offset-md-3 col-md-3 col-sm-12 text-xs-center text-md-left">
                <p><span class="text-muted">Order Date :</span> <?= date('d-m-Y', strtotime($purchase['invoice_date'])) ?></p>
                <p><span class="text-muted">Due Date :</span> <?= date('d-m-Y', strtotime($purchase['due_date'])) ?></p>
                <?php if($purchase['payment_terms'] == 1){
                    $payment_terms = 'Payment Due On Receipt';
                } else {
                    $payment_terms= "Your payment terms";
                }
                ?>
                <p><span class="text-muted">Terms :</span><?= $payment_terms ?></p>
              </div>
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
                        <th>Name</th>
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
              <p></p>
              <div class="row">
                <div class="col-md-7 col-sm-12 text-xs-center text-md-left">
                  <div class="row">
                    <div class="col-md-8">
                      <p class="lead">Payment Status:
                        <u><strong id="pstatus"><?= $purchase['payment_status'] ?? 'Unpaid' ?></strong></u>
                      </p>
                      <p class="lead">Payment Method: <u><strong id="pmethod"><?= $purchase['payment_method'] ?? 'Unpaid' ?></strong></u>
                      </p>
                      <p class="lead mt-1"><br>Note: </p>
                      <code>
                      <?= $purchase['notes'] ?? 'No notes provided.' ?>
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
                                                <td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($purchase['shipping'], 2) ?></td>
                                            </tr>
                                            <tr class="bg-light">
                                                <td class="text-bold-800">Total</td>
                                                <td class="text-bold-800 text-xs-right"><?= $currencySymbol ?> <?= number_format($calculatedGrandTotal, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Payment Made</td>
                                                <td class="text-xs-right"><?= $currencySymbol ?> <?= number_format($purchase['amount_paid'] ?? 0, 2) ?></td>
                                            </tr>
                                            <tr class="bg-grey bg-lighten-4">
                                                <td class="text-bold-800">Balance Due</td>
                                                <td class="text-bold-800 text-xs-right"><?= $currencySymbol ?> <?= number_format(($calculatedGrandTotal - ($purchase['amount_paid'] ?? 0)), 2) ?></td>
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
              <p class="lead">Debit Transactions:</p>
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
                  <p> <strong>Payment Due On Receipt</strong><br>
                  <div><b>1. Prices And Payment</b></div>
                  <div><span style="font-size: 1rem;">Payments are to be made in U.S funds. Unless otherwise specified all invoices are due net 30&nbsp;</span><span style="font-size: 1rem;">&nbsp;</span><span style="font-size: 1rem;">days from date of Shipment.</span></div>
                  </p>
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
  <script src="Public/assets/myjs/jquery.ui.widget.js"></script>
  <script src="Public/assets/myjs/jquery.fileupload.js"></script>
  <!-- Modal HTML -->
  <div id="part_payment" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Debit Payment Confirmation</h4>
        </div>
        <div class="modal-body">
          <form class="payment">
            <div class="row">
              <div class="col-xs-6">
                <div class="input-group">
                  <div class="input-group-addon">$</div>
                  <input type="text" class="form-control" placeholder="Total Amount" name="amount" id="rmpay" value="0">
                </div>
              </div>
              <div class="col-xs-6">
                <div class="input-group">
                  <div class="input-group-addon"><span class="icon-calendar4" aria-hidden="true"></span></div>
                  <input type="text" class="form-control required" id="tsn_date" placeholder="Billing Date" name="paydate" value="04-08-2025">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="pmethod">Payment Method</label>
                <select name="pmethod" class="form-control mb-1">
                  <option value="Cash">Cash</option>
                  <option value="Card">Card</option>
                  <option value="Bank">Bank</option>
                </select><label for="account">Account</label>
                <select name="account" class="form-control">
                  <option value="1">Company Sales Account / 12345678</option>
                  <option value="6">Purchase A/C / 1234567890</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="shortnote">Note</label>
                <input type="text" class="form-control" name="shortnote" placeholder="Short note" value="Payment for purchase #1045">
              </div>
            </div>
            <div class="modal-footer">
              <input type="hidden" class="form-control required" name="tid" id="invoiceid" value="1045">
              <button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
              <input type="hidden" name="cid" value="89"><input type="hidden" name="cname" value="Haroun Spiers">
              <button type="button" class="btn btn-primary" id="submitpayment">Do Payment</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- cancel -->
  
  </div>
  <!-- Modal HTML -->
  <div id="sendEmail" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Email</h4>
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
                  <div class="input-group-addon"><span class="icon-envelope-o" aria-hidden="true"></span></div>
                  <input type="text" class="form-control" placeholder="Email" name="mailtoc" value="hspiers2g@redcross.org">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="shortnote">Customer Name</label>
                <input type="text" class="form-control" name="customername" value="Haroun Spiers">
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="shortnote">Subject</label>
                <input type="text" class="form-control" name="subject" id="subject">
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="shortnote">Message</label>
                <textarea name="text" class="summernote" id="contents" title="Contents"></textarea>
              </div>
            </div>
            <input type="hidden" class="form-control" id="invoiceid" name="tid" value="1045">
            <input type="hidden" class="form-control" id="emailtype" value="">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
          <button type="button" class="btn btn-primary" id="sendM">Send</button>
        </div>
      </div>
    </div>
  </div>
  <div id="pop_model" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Change Payment Status</h4>
        </div>
        <div class="modal-body">
          <form id="form_model" action="/AIS/purchase-update-status" method="POST">
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="pmethod">Mark As</label>
                <select name="status" class="form-control mb-1">
                  <option value="paid">Paid</option>
                  <option value="due">Due</option>
                  <option value="partial">Partial</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <input type="hidden" class="form-control required" name="tid" id="invoiceid" value="<?= $purchase['id'] ?>">
              <button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
              <button type="submit" class="btn btn-primary" >Change Status</button>
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
          <h4 class="modal-title">Change Status</h4>
        </div>
        <div class="modal-body">
          <form id="form_model" action="/AIS/purchase-status" method="POST">
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="pmethod">Mark As</label>
                <select name="status" class="form-control mb-1">
                  <option value="pending">Pending</option>
                  <option value="active">Active</option>
                  <option value="cancelled">Cancel</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <input type="hidden" class="form-control required" name="tid" id="invoiceid" value="<?= $purchase['id'] ?>">
              <button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
              <input type="hidden" id="action-url" value="purchase/update_status">
              <button type="submit" class="btn btn-primary" >Change Status</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Summernote JS -->
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
  <!-- Font Awesome for icons -->
  <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/js/all.min.js"></script>
  <script type="text/javascript">
    $(function() {
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
      // $('#sendM').on('click', function (e) {
      //     e.preventDefault();
      //     sendBill($('.summernote').summernote('code'));
      // });
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
    $(document).on('click', '.sendbill', function() {
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
      setTimeout(function() {
        $('#subject').val(subject);
        $('#contents').val(message);
        $('#emailbody').fadeIn();
        $('#request').hide();
      }, 800);
    });
  </script>

  <!-- <script>
$(document).ready(function () {
    $('#submit_model').click(function () {
        const status = $('select[name="status"]').val();
        const invoiceId = $('#invoiceid').val();
        const url = $('#action-url').val();
        $.ajax({
            url: `/AIS/${url}`,
            method: 'POST',
            data: {
                status: status,
                invoice_id: invoiceId
            },
            success: function (response) {
                alert('Status updated successfully');
                $('#pop_model').modal('hide');
                // optionally reload or update the UI
                location.reload();
            },
            error: function (xhr) {
                alert('Error updating status');
                console.error(xhr.responseText);
            }
        });
    });
});
</script> -->
<?php require __DIR__ . '/../../partials/footer.php'; ?>
</body>
</html>