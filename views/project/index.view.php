<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notify = document.getElementById('notify');
            const message = notify.querySelector('.message');
           const form = document.getElementById('data_form');
            message.innerHTML = <?= json_encode($_SESSION['success']) ?>;
            notify.style.display = 'block';
            form.style.display = 'none';
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php include __DIR__ . '/../partials/head.php'; ?>

<body data-open="click" data-menu="vertical-menu" data-col="2-columns"
      class="vertical-layout vertical-menu 2-columns  fixed-navbar"><span id="hdata"
                                                                          data-df="dd-mm-yyyy"
                                                                          data-curr="$"></span>

<!-- navbar-fixed-top-->
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<!-- ////////////////////////////////////////////////////////////////////////////-->


<!-- main menu-->
<?php include __DIR__ . '/../partials/Sidenav.php'; ?>


<!-- / main menu-->
 <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
 <link href="Public/assets/portcss/bootstrap-colorpicker.min.css" rel="stylesheet"/>
<script src='Public/assets/portjs/moment.min.js'></script>
<script src="Public/assets/portjs/fullcalendar.min.js"></script>
<script src='Public/assets/portjs/bootstrap-colorpicker.min.js'></script>
<script src='Public/assets/portjs/main.js?v=3.3'></script>

<article class="content">
    <div class="card card-block">
        <div id="notify" class="alert alert-success" style="display:none;">
            <a href="#" class="close" data-dismiss="alert">&times;</a>

            <div class="message"></div>
        </div>
        <div class="grid_3 grid_4">


            <form method="post" id="data_form" class="form-horizontal">

                <h5>Add Project</h5>
                <hr>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="name">Title</label>

                    <div class="col-sm-10">
                        <input type="text" placeholder="Project Title"
                               class="form-control margin-bottom  required" name="name">
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="name">Status</label>

                    <div class="col-sm-4">
                        <select name="status" class="form-control">
                            <option value='Waiting'>Waiting</option>
                            <option value='Pending'>Pending</option>
                            <option value='Terminated'>Terminated</option>
                            <option value='Finished'>Finished</option>
                            <option value='Progress'>In Progress</option>                        </select>
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="progress">In Progress                        (in %)</label>

                    <div class="col-sm-10">
                        <input type="range" min="0" max="100" value="0" class="slider" id="progress" name="progress">
                        <p><span id="prog"></span></p>

                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="pay_cat">Priority</label>

                    <div class="col-sm-4">
                        <select name="priority" class="form-control">
                            <option value='Low'>Low</option>
                            <option value='Medium'>Medium</option>
                            <option value='High'>High</option>
                            <option value='Urgent'>Urgent</option>
                        </select>


                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"
                           for="pay_cat">Customer</label>

                    <div class="col-sm-10">
                        <select name="customer" class="form-control" id="customer_statement">
                            <option value="0">Select Customer</option>

                        </select>


                    </div>

                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="name">Customer Can View</label>

                    <div class="col-sm-4">
                        <select name="customerview" class="form-control">
                            <option value='true'>True</option>
                            <option value='false'>False</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="name">Customer Can Comment</label>

                    <div class="col-sm-4">
                        <select name="customercomment" class="form-control">
                            <option value='true'>True</option>
                            <option value='false'>False</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="worth">Budget</label>

                    <div class="col-sm-4">
                        <input type="number" placeholder="Budget"
                               class="form-control margin-bottom  required" name="worth">
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="pay_cat">Assign to</label>

                    <div class="col-sm-8">
                        <select name="employee[]" class="form-control required select-box" multiple="multiple">
                            <option value='6'>John Doe</option>
                            <option value='16'>Oliver Mammatt</option>
                            <option value='17'>Harry McGaughey</option>
                            <option value='18'>William Longlands</option>
                            <option value='20'>Mark Wales</option>
                            <option value='19'>Stephen L. Turner</option>                        
                        </select>
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="phase">Phase</label>

                    <div class="col-sm-10">
                        <input type="text" placeholder="Phase A,B,C"
                               class="form-control margin-bottom  required" name="phase">
                    </div>
                </div>


                <div class="form-group row">

                    <label class="col-sm-2 control-label"
                           for="edate">Start Date</label>

                    <div class="col-sm-2">
                        <input type="text" class="form-control required"
                               placeholder="Start Date" name="sdate"
                               data-toggle="datepicker" autocomplete="false">
                    </div>
                </div>


                <div class="form-group row">

                    <label class="col-sm-2 control-label"
                           for="edate">Due Date</label>

                    <div class="col-sm-2">
                        <input type="text" id="pdate_2" class="form-control required edate"
                               placeholder="End Date" name="edate"
                              autocomplete="false" value="09-09-2025">
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="name">Link to calendar</label>

                    <div class="col-sm-4">
                        <select name="link_to_cal" class="form-control" id="link_to_cal">
                            <option value='0'>No</option>
                            <option value='1'>Mark Deadline(End Date)</option>
                            <option value='2'>Mark Start to End Date</option>
                        </select>
                    </div>
                </div>

                <div id="hidden_div" class="row form-group" style="display: none">
                    <label class="col-md-2 control-label" for="color">Color</label>
                    <div class="col-md-4">
                        <input id="color" name="color" type="text" class="form-control input-md"
                               readonly="readonly"/>
                        <span class="help-block">Click to pick a color</span>
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 control-label"
                           for="content">Note</label>

                    <div class="col-sm-10">
                        <textarea class="summernote"
                                  placeholder=" Note"
                                  autocomplete="false" rows="10" name="content"></textarea>
                    </div>
                </div>

                <div class="form-group row">

                    <label class="col-sm-2 col-form-label" for="tags">Tags</label>

                    <div class="col-sm-10">
                        <input type="text" placeholder="Tags"
                               class="form-control margin-bottom  required" name="tags">
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"
                           for="name">Task Communication</label>

                    <div class="col-sm-4">
                        <select name="ptype" class="form-control">
                            <option value='0'>No</option>
                            <option value='1'>Emails to team</option>
                            <option value='2'>Emails to team, customer</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">

                    <label class="col-sm-2 col-form-label"></label>

                    <div class="col-sm-4">
                        <input type="submit" id="submit-data" class="btn btn-success margin-bottom"
                               value="Add" data-loading-text="Adding...">
                        <input type="hidden" value="projects/addproject" id="action-url">

                    </div>
                </div>


            </form>
        </div>
    </div>
</article>
<script type="text/javascript">

    $(function () {
        $('.select-box').select2();

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

    $("#customer_statement").select2({
        minimumInputLength: 4,
        tags: [],
        ajax: {
            url: baseurl + 'search/customer_select',
            dataType: 'json',
            type: 'POST',
            quietMillis: 50,
            data: function (customer) {
                return {
                    customer: customer
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.name,
                            id: item.id
                        }
                    })
                };
            },
        }
    });

    $('.edate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    var slider = $('#progress');
    var textn = $('#prog');
    textn.text(slider.val() + '%');
    $(document).on('change', slider, function (e) {
        e.preventDefault();
        textn.text($('#progress').val() + '%');

    });
</script><!-- BEGIN VENDOR JS-->
<script type="text/javascript">

    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'dd-mm-yyyy'});
    $('#sdate').datepicker('setDate', '11-07-2025');
    $('.date30').datepicker('setDate', '11-07-2025');


</script>
<script src="Public/assets/myjs/jquery-ui.js"></script>
<script src="Public/assets/vendor/js/ui/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/unison.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/blockUI.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/jquery.matchHeight-min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/ui/screenfull.min.js" type="text/javascript"></script>
<script src="Public/assets/vendor/js/extensions/pace.min.js" type="text/javascript"></script>
<script src="Public/assets/myjs/jquery.dataTables.min.js"></script>
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
