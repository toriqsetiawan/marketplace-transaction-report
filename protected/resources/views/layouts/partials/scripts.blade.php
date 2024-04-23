<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.1.4 -->
<script src="{{ asset('plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset('js/bootstrap.min.js') }}" type="text/javascript"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/select2.full.min.js') }}" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="{{ asset('js/app.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('js/jquery.mask.min.js') }}" type="text/javascript"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        //Initialize Select2 Elements
        $(".select2").select2();

        $('.btn-delete').click(function() {
            $('#myModal').modal('show');
            $('#deleteForm').attr('action', $(this).data('href'));
        });

        $('.btn-print').click(function() {
            $('#myModalPrint').modal('show');
            $('#printForm').attr('action', $(this).data('href'));
        });

        $('.money').mask('000.000.000.000.000,-', {
            reverse: true
        });

        $('#weekly_report').click(function() {
            $('#modalWeekly').modal('show');
        });

        $('#channel').on('change', function() {
            if (this.value == 'online') {
                $('.marketplace-block').show();
            } else {
                $('.marketplace-block').hide();
            }

            let uri = updateQueryStringParameter(window.location.href, 'channel', this.value);
            window.location.href = uri;
        })

        function updateQueryStringParameter(uri, key, value) {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            } else {
                return uri + separator + key + "=" + value;
            }
        }
    });

    $(function() {
        let startDate = '{{ request('start_date') }}';
        let endDate = '{{ request('end_date') }}';

        $('input[name="daterange"]').daterangepicker({
            minYear: 2023,
            startDate: (startDate != "Invalid date" && startDate != "") ? startDate : moment().startOf(
                'month'),
            endDate: (endDate != "Invalid date" && endDate != "") ? endDate : moment().endOf('month'),
            locale: {
                format: 'DD/MM/YYYY'
            },
            ranges: true
        }, function(start, end, label) {
            let startDate = start.format('DD/MM/YYYY'),
                endDate = end.format('DD/MM/YYYY'),
                url = new URL(window.location.href),
                params = url.searchParams;

            params.set('start_date', startDate);
            params.set('end_date', endDate);

            window.location.href = url.toString();
        });
    });
</script>
