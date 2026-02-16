<?php include __DIR__ . '/../../partials/head.php'; ?>
<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notify = document.getElementById('notify');
            const message = notify.querySelector('.message');
           const form = document.getElementById('data_form');
            message.innerHTML = <?= json_encode($_SESSION['success']) ?>;
            notify.style.display = 'block';
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<body data-open="click" data-menu="vertical-menu" data-col="2-columns"
      class="vertical-layout vertical-menu 2-columns  fixed-navbar"><span id="hdata"
                                                                          data-df="dd-mm-yyyy"
                                                                          data-curr="$"></span>

<!-- navbar-fixed-top-->
<?php include __DIR__ . '/../../partials/navbar.php'; ?>

<!-- ////////////////////////////////////////////////////////////////////////////-->


<!-- main menu-->
<?php include __DIR__ . '/../../partials/Sidenav.php'; ?>
<!-- / main menu-->
 
 <article class="content">
    <div class="card card-block">
         <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>      
          <div class="grid_3 grid_4"><h4>Sample ticket :  an issue invoice 
            <a href="#pop_model" data-toggle="modal" data-remote="false"  class="btn btn-sm btn-cyan mb-1" title="Change Status"><span class="icon-tab"></span> Change Status
             </a>
           </h4>
            <p class="card card-block"><strong>Created on: </strong> <?=  date('Y-m-d H:i:s', strtotime($support['created_at'])) ?><br><strong>Customer</strong> <?= $user['name'] ?? 'CUSTOMER' ?><br>
            <strong>Status</strong>
             <span id="pstatus">
                <?php if(isset($support)){
  if($support['status'] == 'processing'){
     echo 'Processing';
    } elseif($support['status'] == 'solved'){
       echo  'Solved';
    } elseif($support['status'] == 'waiting'){
      echo   'Waiting';
    } elseif($support['status'] == 'closed'){
     echo    'Closed';
    }
     else {
     echo    'pending';
    }  
 }
 ?>
             </span></p>
                <div class="form-group row">
                    <div class="col-sm-10">
                 <?php foreach ($replies as $reply): ?>    
    <div class="card card-block">
        <strong><?= ucfirst($reply['sender_type']) ?> replied:</strong><br>
        <?= nl2br(htmlspecialchars(trim($reply['message']))) ?><br><br>

        <?php
        $replyAttachments = $db->query(
            "SELECT * FROM ticket_attachments WHERE reply_id = :reply_id",
            ['reply_id' => $reply['id']]
        )->get();
        ?>

        <?php if ($replyAttachments): ?>
            <strong>Attachments:</strong><br>
            <?php foreach ($replyAttachments as $att): ?>
                <a href="Public/<?= htmlspecialchars($att['file_path']) ?>" target="_blank">
                    <?= htmlspecialchars($att['file_name']) ?>
                </a><br>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

                    </div>
                </div>
            <form action="/AIS/ticket-store" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                <input type="hidden" name="sent_via" value="web">
                <input type="hidden" name="ticket_id" value="<?= $id ?>">
                <input type="hidden" name="sender_type" value="admin">
            <h5>Your Response</h5>
            <hr>
            <div class="form-group row">

                <label class="col-sm-2 control-label"
                       for="edate">Reply</label>

                <div class="col-sm-10">
                        <textarea class="summernote"
                                  placeholder=" Message"
                                  autocomplete="false" rows="10" name="content"></textarea>
                </div>
            </div>

            <div class="form-group row">

                <label class="col-sm-2 col-form-label" for="name">Attach </label>

                <div class="col-sm-6">
                    <input type="file" name="attachment[]" multiple/><br>
                    <small>(docx, docs, txt, pdf, xls, png, jpg, gif)</small>
                </div>
            </div>


            <div class="form-group row">

                <label class="col-sm-2 col-form-label"></label>

                <div class="col-sm-4">
                    <button type="submit" class="btn btn-success margin-bottom">
                        Update
                        </button>
                </div>
            </div>


            </form>
        </div>
    </div>
</article>


<script type="text/javascript">
    $(function () {
        $('.summernote').summernote({
            height: 250,
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

<div id="pop_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Change Status</h4>
            </div>

            <div class="modal-body">
                <form id="form_model" action="/AIS/ticket-update" method="POST">

                    <div class="row">
                        <div class="col-xs-12 mb-1"><label
                                    for="pmethod">Mark As</label>
                            <select name="status" class="form-control mb-1">
                                <option value="solved">Solved</option>
                                <option value="processing">Processing</option>
                                <option value="waiting">Waiting</option>
                            </select>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <input type="hidden" class="form-control required"
                               name="id" value="<?= ($support['id'] ?? '') ?>">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-primary" >Change Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- BEGIN VENDOR JS-->
<script type="text/javascript">

    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('#sdate').datepicker('setDate', '15-06-2025');
    $('.date30').datepicker('setDate', '15-06-2025');


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
