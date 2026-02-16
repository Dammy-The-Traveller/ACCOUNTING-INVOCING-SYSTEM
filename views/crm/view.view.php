<?php include __DIR__ . '/../partials/head.php'; ?>
<?php
$isCustomer = isset($customer) && !empty($customer['id']);
?>
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
  <?php include __DIR__ . '/../partials/navbar.php'; ?>
  <!-- main menu-->
  <?php include __DIR__ . '/../partials/Sidenav.php'; ?>
  <!-- / main menu-->



<div class="app-content content container-fluid">
  <div class="content-wrapper">
    <div id="notify" class="alert alert-success" style="display:none;">
      <a href="#" class="close" data-dismiss="alert">&times;</a>
      <div class="message"></div>
    </div>
    <div class="content-body">
      <section>
        <div class="row wrapper white-bg page-heading">
          <div class="col-md-4">
            <div class="card card-block">
              <h4 class="text-xs-center">Test Customer</h4>
              <div class="ibox-content mt-2">
                <img loading="lazy" alt="image" id="dpic" class="img-responsive" src="Public/assets/img/example.png">
              </div>
              <hr>
              <div class="user-button">
                <div class="row mt-3">
                  <div class="col-md-6">
                    <a href="#sendMail" data-toggle="modal" data-remote="false" class="btn btn-primary btn-md sendbill" data-type="reminder"><i class="icon-envelope"></i> Send Message </a>
                  </div>
                  <div class="col-md-6">
                    <a href="/AIS/customer-edit?id=<?= $id ?>" class="btn btn-info btn-md"><i class="icon-pencil"></i> Edit Profile </a>
                  </div>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col-md-12">
                  <h3>Balance</h3>
                  $ 0.00
                  <hr>
                  <h5>Balance Summary</h5>
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                      <span class="tag tag-default tag-pill bg-primary float-xs-right">$ 0.00</span>
                      Income
                    </li>
                    <li class="list-group-item">
                      <span class="tag tag-default tag-pill bg-danger float-xs-right">$ 0.00</span>
                      Expenses
                    </li>
                    <li class="list-group-item">
                      <span class="tag tag-default tag-pill bg-danger float-xs-right">$ 0.00</span>
                      Total Due
                    </li>
                  </ul>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-md-12">
                  <h5>Client Group <small><?= $isCustomer ? htmlspecialchars($group['name'] ?? '') : ''?></small>
                  </h5>
                </div>
              </div>
              <div class="row mt-1">
                <div class="offset-md-2 col-md-4">
                  <a href="#" class="btn btn-danger btn-md"><i class="icon-pencil"></i> Change Password </a>
                </div>
                <div class="col-md-12"><br>
                  <h5>Change Customer Picture</h5><input id="fileupload" type="file" name="files[]">
                </div>
                <div id="progress" class="progress1">
                  <div class="progress-bar progress-bar-success"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <div class="card card-block">
              <h4>Customer Details</h4>
              <hr>
              <div class="row m-t-lg">
                <div class="col-md-2">
                  <strong>Name</strong>
                </div>
                <div class="col-md-10">
                 <?= $isCustomer ? htmlspecialchars($customer['name'] ?? '') : ''?> </div>
              </div>
              <hr>
              <div class="row m-t-lg">
                <div class="col-md-2">
                  <strong>Company</strong>
                </div>
                <div class="col-md-10">
                    <?= $isCustomer ? htmlspecialchars($customer['company'] ?? '') : ''?>
                </div>
              </div>
              <hr>
              <div class="row m-t-lg">
                <div class="col-md-2">
                  <strong> Address</strong>
                </div>
                <div class="col-md-10">
                 <?= $isCustomer ? htmlspecialchars($customer['address'] ?? '') : ''?>
                </div>
              </div>
              <hr>
              <div class="row m-t-lg">
                <div class="col-md-2">
                  <strong>City</strong>
                </div>
                <div class="col-md-10">
                  <?= $isCustomer ? htmlspecialchars($customer['city'] ?? '') : ''?></div>
              </div>
              <hr>
              <div class="row m-t-lg">
                <div class="col-md-2">
                  <strong>Region</strong>
                </div>
                <div class="col-md-10">
                 <?= $isCustomer ? htmlspecialchars($customer['region'] ?? '') : ''?></div>
              </div>
              <hr>
              <div class="row m-t-lg">
                <div class="col-md-2">
                  <strong>Country</strong>
                </div>
                <div class="col-md-10">
                  <?= $isCustomer ? htmlspecialchars($customer['country'] ?? '') : ''?></div>
              </div>
              <hr>
              <div class="row m-t-lg">
                <div class="col-md-2">
                  <strong>PostBox</strong>
                </div>
                <div class="col-md-10">
                  <?= $isCustomer ? htmlspecialchars($customer['postbox'] ?? '') : ''?></div>
              </div>
              <hr>
              <div class="row m-t-lg">
                <div class="col-md-2">
                  <strong>Email</strong>
                </div>
                <div class="col-md-10">
                 <?= $isCustomer ? htmlspecialchars($customer['email'] ?? '') : ''?></div>
              </div>
              <hr>
              <div class="row m-t-lg">
                <div class="col-md-2">
                  <strong> Phone</strong>
                </div>
                <div class="col-md-10">
                 <?= $isCustomer ? htmlspecialchars($customer['phone'] ?? '') : ''?> </div>
              </div>
              <hr>
              <div class="row mt-3">
                <div class="col-md-4">
                  <a href="/AIS/customer-invoice?id=<?= $id ?>" class="btn btn-primary btn-lg"><i class="icon-file-text2"></i> View Invoices</a>
                </div>
                <div class="col-md-4">
                  <a href="#" class="btn btn-success btn-lg"><i class="icon-money3"></i> View Transactions </a>
                </div>
                <div class="col-md-4">
                  <a href="#" class="btn btn-primary btn-lg"><i class="icon-wallet"></i> Wallet </a>
                </div>
              </div>
              <hr>
              <h5 class="text-xs-center">Wallet Recharge/Payment History</h5>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Amount</th>
                    <th>Note</th>
                  </tr>
                </thead>
                <tbody id="activity"></tbody>

   <div id="sendMail" class="modal fade">
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
                  <input type="text" class="form-control" placeholder="Email" name="mailtoc" value="info@dtt.com">
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
  </div>


  <!-- Summernote JS -->
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
  <!-- Font Awesome for icons -->
  <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/js/all.min.js"></script>
    <script src="Public/assets/myjs/jquery.ui.widget.js"></script>
  <script src="Public/assets/myjs/jquery.fileupload.js"></script>

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
        $('#sendMail').modal('hide');
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
  <script type="text/javascript">
    $('[data-toggle="datepicker"]').datepicker({
      autoHide: true,
      format: 'dd-mm-yyyy'
    });
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({
      autoHide: true,
      format: 'dd-mm-yyyy'
    });
    $('#sdate').datepicker('setDate', '11-06-2025');
    $('.date30').datepicker('setDate', '11-06-2025');
  </script>

  <script src="Public/assets/myjs/jquery-ui.js"></script>
  <script src="Public/assets/vendor/js/ui/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
  <script src="Public/assets/vendor/js/ui/unison.min.js" type="text/javascript"></script>
  <script src="Public/assets/vendor/js/ui/blockUI.min.js" type="text/javascript"></script>
  <script src="Public/assets/vendor/js/ui/jquery.matchHeight-min.js" type="text/javascript"></script>
  <script src="Public/assets/vendor/js/ui/screenfull.min.js" type="text/javascript"></script>
  <script src="Public/assets/vendor/js/extensions/pace.min.js" type="text/javascript"></script>
  <script src="Public/assets/myjs/jquery.dataTables.min.js"></script>
  <script type="text/javascript">
    var dtformat = $('#hdata').attr('data-df');
    var currency = $('#hdata').attr('data-curr');;
  </script>
  <script src="Public/assets/myjs/custom.js?v=3.3"></script>
  <script src="Public/assets/myjs/basic.js?v=3.3"></script>
  <script src="Public/assets/js/core/app.js?v=3.3" type="text/javascript"></script>
  <script src="Public/assets/js/core/app-menu.js" type="text/javascript"></script>
  <script type="text/javascript">
    $.ajax({
      url: baseurl,
      dataType: 'json',
      type: 'GET',
      success: function(data) {
        $('#tasklist').html(data.tasks);
        $('#taskcount').html(data.tcount);
      },
      error: function(data) {
        $('#response').html('Error')
      }
    });
    var winh = document.body.scrollHeight;
    var sideh = document.getElementById('side').scrollHeight;
    var opx = winh - sideh;
    document.getElementById('rough').style.height = opx + "px";
    $('body').on('click', '.menu-toggle', function() {
      var opx2 = winh - sideh + 180;
      document.getElementById('rough').style.height = opx2 + "px";
    });
  </script>
</body>
</html>