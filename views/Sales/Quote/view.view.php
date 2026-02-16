<?php
/** @var array $quotes */
/** @var array $quoteId */
/** @var array $customer */
/** @var array $quoteItems */
?>
<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notify = document.getElementById('notify');
            const message = notify.querySelector('.message');
            message.innerHTML = <?= json_encode($_SESSION['success']) ?>;
            notify.style.display = 'block';
    
        });
    </script>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
<?php include __DIR__ . '/../../partials/head.php'; ?>
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
                  <a href="/AIS/quote?id=<?= $quotes['id'] ?>" class="btn btn-warning mb-1"><i class="icon-pencil"></i> Edit Quote </a>
                  <a href="#pop_model" data-toggle="modal" data-remote="false" class="btn btn-large btn-success mb-1" title="Change Status"><span class="icon-tab"></span> Change Status </a>
                  <a href="#pop_model2" data-toggle="modal" data-remote="false" class="btn btn-large btn-info mb-1" title="convert to Invoice"><span class="icon-share2"></span> Convert to Invoice </a>
                  <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle mb-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="icon-envelope-o"></span> EMail
                    </button>
                    <div class="dropdown-menu"><a href="#sendEmail" data-toggle="modal" data-remote="false" class="dropdown-item sendbill" data-type="quote">Send Proposal</a>
                    </div>
                  </div>
                  <div class="btn-group">
                    <button type="button" class="btn btn-blue dropdown-toggle mb-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="icon-envelope-o"></span> SMS
                    </button>
                    <div class="dropdown-menu"><a href="#sendSMS" data-toggle="modal" data-remote="false" class="dropdown-item sendsms" data-type="quotes">Send Proposal</a>
                    </div>
                  </div>
                  <div class="btn-group ">
                    <button type="button" class="btn btn-success btn-min-width dropdown-toggle mb-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icon-print"></i> Print Quote </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="/AIS/quote-generate?id=<?= $quotes['id'] ?>&token=<?= $quotes['public_token'] ?>">Print</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="/AIS/quote-download?id=<?= $quotes['id']  ?>&token=<?= $quotes['public_token'] ?>">PDF Download</a>
                    </div>
                  </div>
                  <a href="/AIS/quote-views?id=<?=$quoteId?>&token=<?= $quotes['public_token'] ?>" class="btn btn-primary mb-1"><i class="icon-earth"></i> Preview </a>
                </div>
              </div>
            </div>
            <div class="tag tag-info text-xs-center mt-2">Payment currency is different for this invoice. Please check the invoice preview.</div>

            <!-- Invoice Company Details -->
            <div id="invoice-company-details" class="row mt-2">
              <div class="col-md-6 col-sm-12 text-xs-center text-md-left">
                <p></p>
                <h1  id="logoText" class="brand-logo height-50 text-responsive p-1 m-b-2"><span style="color:yellow;font-weight: 900;font-family: system-ui;">DTT</span><span style="color:#3BAFDA;">EDGE</span></h1>
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
            </div> <!-- Invoice Items Details -->
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
                      <tr><td colspan=5></td>
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
            <!--/ Invoice Footer -->
          </div>
        </section>
      </div>
    </div>
  </div>

  <!-- Modal HTML -->
  <div id="pop_model" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Change Status</h4>
        </div>
        <div class="modal-body">
          <form id="form_model" action="/AIS/quote-update-status" method="POST">
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="pmethod">Mark As</label>
                <select name="status" class="form-control mb-1">
                  <option value="pending">Pending</option>
                  <option value="accepted">Accepted</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <input required type="hidden" class="form-control required" name="tid" id="invoiceid" value="<?= $quotes['id']  ?>">
              <button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
              <button type="submit" class="btn btn-primary" id="submit_model">Change Status</button>
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
          <form id="form_model2" >
            <div class="row">
                
              <div class="col-xs-12 mb-1">Convert the quote as invoice. Are you sure?
              </div>
            </div>
            <div class="modal-footer">
              <input required type="hidden" class="form-control required" name="tid" id="invoiceid" value="">
              <button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
              
              <button type="button" class="btn btn-primary" id="submit_model2">Yes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
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
                  <input required type="text" class="form-control" placeholder="Email" name="mailtoc" value="customer@example.com">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="shortnote">Customer Name</label>
                <input required type="text" class="form-control" name="customername" value="Test Customer">
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="shortnote">Subject</label>
                <input required type="text" class="form-control" name="subject" id="subject">
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="shortnote">Message</label>
                <textarea name="text" class="summernote" id="contents" title="Contents"></textarea>
              </div>
            </div>
            <input required type="hidden" class="form-control" id="invoiceid" name="tid" value="1046">
            <input required type="hidden" class="form-control" id="emailtype" value="">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
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
                  <div class="input-group-addon"><span class="icon-envelope-o" aria-hidden="true"></span></div>
                  <input required type="text" class="form-control" placeholder="SMS" name="mobile" value="12345678">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="shortnote">Customer Name</label>
                <input required type="text" class="form-control" value="Test Customer">
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 mb-1"><label for="shortnote">Message</label>
                <textarea class="form-control" name="text_message" id="sms_tem" title="Contents" rows="3"></textarea>
              </div>
            </div>
            <input required type="hidden" class="form-control" id="smstype" value="">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
          <button type="button" class="btn btn-primary" id="submitSMS">Send</button>
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


                   $('#submit_model2').click(function() {
                var formData = $('#pop_model2').serialize();
                console.log('Form data:', formData);
                // Display success message
                $('#notify .message').html('<strong>Success</strong>: Conversion Successful!. This feature is disabled in the Demo Mode.');
                $('#notify').fadeIn();
                // Automatically hide the alert after 3 seconds
                setTimeout(function() {
                    $('#notify').fadeOut();
                }, 3000);
                // Close the modal
                $('#pop_model2').modal('hide');
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
        case 'Send Proposal':
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
                    case 'Send Proposal':
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
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
  <script type="text/javascript">
    $(function() {
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
      $('#sendM').on('click', function(e) {
        e.preventDefault();
        sendBill($('.summernote').summernote('code'));
      });
    });
  </script>
  <!-- BEGIN VENDOR JS-->
  
<?php require __DIR__ . '/../../partials/footer.php'; ?>
</body>
</html>