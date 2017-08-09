<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.1.4 -->
<script src="{{ asset('plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset('js/bootstrap.min.js') }}" type="text/javascript"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/select2.full.min.js') }}" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="{{ asset('js/app.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function() {
        //Initialize Select2 Elements
        $(".select2").select2();

        $('.btn-delete').click(function() {
            $('#myModal').modal('show');
            $('#deleteForm').attr('action', $(this).data('href'));
        });

    });
</script>