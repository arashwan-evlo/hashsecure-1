<script>
    $(document).ready(function () {

        $(document).on('change', '#account_1', function () {
            var account_id = $(this).val();
            $.ajax({
                url: "{{action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@getaccount')}}",
                type: 'GET',
                data: {
                    id: account_id
                },
                success: function (result) {
                    $('#acount-chiled').html(result.html);
                    var s = '<option value="">الكل</option>';
                    result.data.forEach(function (row) {
                        s += '<option value="' + row.id + '">' + row.name + '</option>';
                    });
                    $('#account_2').html(s);
                },
            });

        });

        $(document).on('change', '#account_2', function () {
            var account_id = $(this).val();
            $.ajax({
                url: "{{action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@getaccount')}}",
                type: 'GET',
                data: {
                    id: account_id
                },
                success: function (result) {
                    $('#acount-chiled').html(result.html);
                    var s = '<option value="">الكل</option>';
                    result.data.forEach(function (row) {
                        s += '<option value="' + row.id + '">' + row.name + '</option>';
                    });
                    $('#account_3').html(s);
                },
            });

        });
    });

</script>