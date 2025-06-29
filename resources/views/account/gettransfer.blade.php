@extends('layouts.app')
@section('title', 'الشحن وتحويل الرصيد')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.payment_accounts')
            <small>@lang('account.manage_your_account')</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.filters', ['title' => __('report.filters')])

        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('from_account',"التحويل من حساب :") !!}
                {!! Form::select('from_account', $accounts, null, ['class' => 'form-control select2', 'required' ]); !!}
            </div>
        </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('to_account',"إلي حساب :") !!}
                    {!! Form::select('to_account', $accounts, null, ['class' => 'form-control select2', 'required' ]); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('user_id',"بواسطة :") !!}
                    {!! Form::select('user_id', $users, null, ['class' => 'form-control select2', 'required' ]); !!}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('transaction_date_range', __('report.date_range') . ':') !!}
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        {!! Form::text('transaction_date_range', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]) !!}
                    </div>
                </div>
            </div>
       <div class="clearfix"></div>
        <div class="col-md-2">
            <button data-href="{{action('AccountController@getFundTransfer',[1])}}" class="btn btn-danger  m-6  m-5 btn-modal" data-container=".view_modal"><i class="fa fa-exchange"></i> @lang("account.fund_transfer")</button>
        </div>

        @endcomponent


       <div class="table-responsive">
        <table class="table table-bordered table-striped" id="other_account_table">
            <thead>
            <tr>
                <th>التاريخ</th>
                <th>من حساب</th>
                <th>المبلغ</th>
                <th>إلي حساب</th>
                <th>المبلغ</th>
                <th>العملية</th>
                <th>بواسطة</th>
                <th>@lang( 'messages.action' )</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
             <td colspan="5">
                 إجمالي :
             </td>
                <td>
                    <span id="total_dev">300.50</span>
                </td>
                <td></td>
                <td></td>
            </tr>
            </tfoot>
        </table>
    </div>




        <div class="modal fade account_model" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel" id="account_type_modal">
        </div>
    </section>
    <!-- /.content -->

@endsection

@section('javascript')
    <script>
        $(document).ready(function(){
            $('#transaction_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    other_account_table.ajax.reload();
                }
            );
            other_account_table = $('#other_account_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/account/account-transfer?account_type=other',
                    data: function(d) {
                        var start = '';
                        var end = '';
                        if($('#transaction_date_range').val()){
                            start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            end = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        }
                        var transaction_type = $('select#transaction_type').val();
                        d.start_date = start;
                        d.end_date = end;
                        d.from_account=$('#from_account').val();
                        d.to_account=$('#to_account').val();
                        d.user_id=$('#user_id').val();

                    }
                },
                columnDefs:[{
                    "targets": 7,
                    "orderable": false,
                    "searchable": false
                }],
                columns: [
                    {data: 'transaction_date', name: 'fund_transfers.transaction_date'},
                    {data: 'account_from', name: 'account_from'},
                    {data: 'account_from_amount', name: 'account_from_amount'},
                    {data: 'account_to', name: 'account_to'},
                    {data: 'account_to_amount', name: 'account_to_amount'},
                    {data: 'dev_val', name: 'dev_val'},
                    {data: 'added_by', name: 'added_by',searchable: false},
                    {data: 'action', name: 'action'}
                ],
                "fnDrawCallback": function (oSettings) {
                    __currency_convert_recursively($('#other_account_table'));
                    calctotal();
                }
            });
            $(document).on('click', '.delete_account_transaction', function(e){
                e.preventDefault();
                swal({
                    title: LANG.sure,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).data('href');
                        $.ajax({
                            url: href,
                            dataType: "json",
                            success: function(result){
                                if(result.success === true){
                                    toastr.success(result.msg);
                                    other_account_table.ajax.reload();

                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            $(document).on('change','#user_id,#from_account,#to_account',function () {
                other_account_table.ajax.reload();
             });

            $(document).on('submit', 'form#fund_transfer_form', function(e){
                e.preventDefault();
                var data = $(this).serialize();
                var form = $(this);
                $.ajax({
                    method: "POST",
                    url: $(this).attr("action"),
                    dataType: "json",
                    data: data,
                    success: function(result){
                        if(result.success == true){
                            $('div.view_modal').modal('hide');
                            toastr.success(result.msg);
                            other_account_table.ajax.reload();
                            calctotal();
                        } else {
                            __enable_submit_button(form.find('button[type="submit"]'));
                            toastr.error(result.msg);
                        }
                    }
                });

            });

            calctotal();
            function calctotal() {
                $.ajax({
                    url: '/account/get-total-trafer',
                    method:'get',
                    data:{
                        start_date : $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                        end_date : $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                        from_account:$('#from_account').val(),
                        to_account:$('#to_account').val(),
                        user_id:$('#user_id').val()

                        },
                    success: function (result) {
                        $('#total_dev').text(result);
                    }
                });
            }
        });
    </script>
@endsection