<script type="text/javascript">
    $('[data-toggle="datepicker"]').datepicker({autoHide: true, format: 'yyyy-mm-dd'});
    $('[data-toggle="datepicker"]').datepicker('setDate', new Date());
    $('#sdate').datepicker({autoHide: true, format: 'yyyy-mm-dd'});
    $('#sdate').datepicker('setDate', new Date());
    $('.date30').datepicker('setDate', new Date());
</script>
<!-- Font Awesome JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <!-- Font Awesome for icons -->
<script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/js/all.min.js"></script>
    <script>
        $.ajax({
            url: baseurl + 'manager/pendingtasks',
            dataType: 'json',
            success: function (data) {
                $('#tasklist').html(data.tasks);
                $('#taskcount').html(data.tcount);
            },
            error: function () {
                $('#response').html('Error');
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