@extends('layouts.app')
@section('title','chartofaccounts')

@section('content')
    <section class="content-header no-print">
        <h1>@lang( 'chartofaccounts::lang.journals')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])


                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('account_id',' الحساب : ') !!}
                        {!! Form::select('account_id', $accounts, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('created_by',  __('report.user') . ':') !!}
                        {!! Form::select('created_by', $sales_representative, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>


        @endcomponent

       @component('components.widget', ['class' => 'box-primary', 'title' => __( 'chartofaccounts::lang.journals')])
                @can('sell.create')
                    @slot('tool')
                        <div class="box-tools">
                            <a class="btn btn-block btn-primary" href="{{action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@create')}}">
                                <i class="fa fa-plus"></i> @lang('messages.add')</a>
                        </div>
                    @endslot
                @endcan
                @if(auth()->user()->can('direct_sell.access') ||  auth()->user()->can('view_own_sell_only') ||  auth()->user()->can('view_commission_agent_sell'))
                    @php
                        $custom_labels = json_decode(session('business.custom_labels'), true);
                    @endphp
                    <table class="table table-bordered table-striped ajax_view" id="sell_table" style="width: 100%">
                        <thead>
                        <tr>
                            <th style="width: 100px">@lang('messages.action')</th>
                            <th>رقم القيد</th>
                            <th>@lang('messages.date')</th>
                             <th>الوصف</th>
                             <th style="width: 100px">الإجمالي</th>
                            <th style="width: 150px">@lang('lang_v1.added_by')</th>

                         </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                @endif
            @endcomponent
    </section>

    <div class="modal fade payment_modal " tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>

    <!-- This will be printed -->
    <!-- <section class="invoice print_section" id="receipt_section">
    </section> -->

@stop

@section('javascript')
   {{-- @include('chartofaccounts::javascript')--}}

<script type="text/javascript">
    $(document).ready( function(){
        //Date range as a button
        $('#sell_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                sell_table.ajax.reload();
            }
        );
        $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_list_filter_date_range').val('');
            sell_table.ajax.reload();
        });

        sell_table = $('#sell_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            "ajax": {
                "url":  '/chartofaccounts',
                "data": function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    d.account_id = $('#account_id').val();
                    d.created_by = $('#created_by').val();
                    d = __datatable_ajax_callback(d);
                }
            },
            scrollY:        "75vh",
            scrollX:        true,
            scrollCollapse: true,
            columns: [
                { data: 'action', name: 'action', orderable: false, "searchable": false},
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'additional_notes', name: 'additional_notes'},
                { data: 'final_total', name: 'final_total'},
                { data: 'added_by', name: 'u.first_name'},

               ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#sell_table'));
            },
             createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(6)').attr('class', 'clickable_td');
            }
        });

        $(document).on('change', '#account_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status',  function() {
            sell_table.ajax.reload();
        });

        $(document).on('click', '.btn-modal-delete', function(e) {
            e.preventDefault();
            swal({
                title:   'سوف يتم حذف القيد ',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).attr('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        data:{
                            account_id:$('#selected_account').val()
                        },
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                sell_table.ajax.reload();
                                toastr.success(result.msg);

                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });



    });









        function getaccount(account_id) {

            $.ajax({
                url: '/chartofaccounts',
                type:'GET',
                data:{
                    id:account_id
                },
                success: function(result) {
                    $('#journal_table').html(result);

                },
            });

        }
        function addchartaccount() {
            $.ajax({
                url: '/chartofaccounts/addaccount',
                dataType: 'html',
                success: function(result) {
                    $('#modeldiv').html(result).modal('show');
                },
            });
        }


        $(document).on('click','.add_document',function (e){
            e.preventDefault();
            var href = $(this).attr('href');
            $.ajax({
                method: 'GET',
                url: href,
                success: function(result) {
                    $('.payment_modal').html(result).modal('show');
                },
            });

        });

        $(document).on('submit','form#store_journal_document',function (e){
            e.preventDefault();
            var form=$(this);
            var data =new FormData(this);// $(this).serialize();
            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                contentType: false,
                processData: false,
                beforeSend: function(xhr) {
                    __disable_submit_button(form.find('button[type="submit"]'));
                },
                success: function(result) {
                    if (result.success == true) {
                        $('div.payment_modal').modal('hide');
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                        __enable_submit_button(form.find('button[type="submit"]'));
                    }
                },
            });

        });

        function chart_view(){
            $.ajax({
                url: '/chartofaccounts/chart_view',
                success: function(result) {
                    $('#treeview').treeview({
                        data:result
                    })
                },
            });
        }

        $('#modeldiv').on('shown.bs.modal', function() {
            $('#modeldiv')
                .find('.select2')
                .each(function() {
                    var $p = $(this).parent();
                    $(this).select2({dropdownParent: $p });
                });
        });

        $(document).on('submit', 'form#add_form', function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();

            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                beforeSend: function(xhr) {
                    __disable_submit_button(form.find('button[type="submit"]'));
                },
                success: function(result) {
                    if (result.success == true) {
                        $('div.modeldiv').modal('hide');
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                        __enable_submit_button(form.find('button[type="submit"]'));
                    }
                },
            });
        });

    </script>


@endsection
