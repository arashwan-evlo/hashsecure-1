@extends('layouts.app')
@section('title', __( 'lang_v1.all_sales'))

@section('content')
@include('installment::layouts.partials.style')
    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>@lang( 'sale.sells')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
            @if(empty($only) || in_array('sell_list_filter_location_id', $only))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_location_id',  __('purchase.business_location') . ':') !!}

                        {!! Form::select('sell_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
                    </div>
                </div>
            @endif
            @if(empty($only) || in_array('sell_list_filter_customer_id', $only))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_customer_id',  __('contact.customer') . ':') !!}
                        {!! Form::select('sell_list_filter_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
            @endif
            @if(empty($only) || in_array('sell_list_filter_payment_status', $only))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_payment_status',  __('purchase.payment_status') . ':') !!}
                        {!! Form::select('sell_list_filter_payment_status', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue'),'installmented' => __('lang_v1.installmented')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
            @endif
            @if(empty($only) || in_array('sell_list_filter_date_range', $only))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('sell_list_filter_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('sell_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
            @endif
            @if((empty($only) || in_array('created_by', $only)) && !empty($sales_representative))
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('created_by',  __('report.user') . ':') !!}
                        {!! Form::select('created_by', $sales_representative, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
            @endif





        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_sales')])

            @if(auth()->user()->can('installment.view') || auth()->user()->can('direct_sell.access') ||  auth()->user()->can('view_own_sell_only') ||  auth()->user()->can('view_commission_agent_sell'))
                @php
                    $custom_labels = json_decode(session('business.custom_labels'), true);
                @endphp
                <table class="table table-bordered table-striped ajax_view" id="sell_table">
                    <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('sale.invoice_no')</th>
                        <th>@lang('sale.customer_name')</th>
                        <th>@lang('lang_v1.contact_no')</th>
                        <th>@lang('sale.location')</th>
                        <th>@lang('sale.payment_status')</th>
                        <th>@lang('lang_v1.payment_method')</th>
                        <th>@lang('sale.total_amount')</th>
                        <th>@lang('sale.total_paid')</th>
                        <th>@lang('lang_v1.sell_due')</th>
                        <th>@lang('lang_v1.sell_return_due')</th>
                        <th>@lang('lang_v1.shipping_status')</th>
                        <th>@lang('lang_v1.total_items')</th>
                        <th>@lang('lang_v1.types_of_service')</th>
                        <th>{{ $custom_labels['types_of_service']['custom_field_1'] ?? __('lang_v1.service_custom_field_1' )}}</th>
                        <th>@lang('lang_v1.added_by')</th>
                        <th>@lang('sale.sell_note')</th>
                        <th>@lang('sale.staff_note')</th>
                        <th>@lang('sale.shipping_details')</th>
                        <th>@lang('restaurant.table')</th>
                        <th>@lang('restaurant.service_staff')</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                        <td class="footer_payment_status_count"></td>
                        <td class="payment_method_count"></td>
                        <td class="footer_sale_total"></td>
                        <td class="footer_total_paid"></td>
                        <td class="footer_total_remaining"></td>
                        <td class="footer_total_sell_return_due"></td>
                        <td colspan="2"></td>
                        <td class="service_type_count"></td>
                        <td colspan="7"></td>
                    </tr>
                    </tfoot>
                </table>
            @endif
        @endcomponent
    </section>
    <!-- /.content -->
    <div class="modal fade payment_modal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>

    <!-- This will be printed -->
    <!-- <section class="invoice print_section" id="receipt_section">
    </section> -->
    <div class="modal fade div_modal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>
@stop


@section('javascript')
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
                    "url": "/installment/sells",
                    "data": function ( d ) {
                        if($('#sell_list_filter_date_range').val()) {
                            var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                        d.is_direct_sale = 1;

                        d.location_id = $('#sell_list_filter_location_id').val();
                        d.customer_id = $('#sell_list_filter_customer_id').val();
                        d.payment_status = $('#sell_list_filter_payment_status').val();
                        d.created_by = $('#created_by').val();
                        d.sales_cmsn_agnt = $('#sales_cmsn_agnt').val();
                        d.service_staffs = $('#service_staffs').val();

                        if($('#shipping_status').length) {
                            d.shipping_status = $('#shipping_status').val();
                        }

                        @if($is_woocommerce)
                        if($('#synced_from_woocommerce').is(':checked')) {
                            d.only_woocommerce_sells = 1;
                        }
                        @endif

                        if($('#only_subscriptions').is(':checked')) {
                            d.only_subscriptions = 1;
                        }

                        d = __datatable_ajax_callback(d);
                    }
                },
                scrollY:        "75vh",
                scrollX:        true,
                scrollCollapse: true,
                columns: [
                    { data: 'action', name: 'action', orderable: false, "searchable": false},
                    { data: 'transaction_date', name: 'transaction_date'  },
                    { data: 'invoice_no', name: 'invoice_no'},
                    { data: 'conatct_name', name: 'conatct_name'},
                    { data: 'mobile', name: 'contacts.mobile'},
                    { data: 'business_location', name: 'bl.name'},
                    { data: 'payment_status', name: 'payment_status'},
                    { data: 'payment_methods', orderable: false, "searchable": false},
                    { data: 'final_total', name: 'final_total'},
                    { data: 'total_paid', name: 'total_paid', "searchable": false},
                    { data: 'total_remaining', name: 'total_remaining'},
                    { data: 'return_due', orderable: false, "searchable": false},
                    { data: 'shipping_status', name: 'shipping_status'},
                    { data: 'total_items', name: 'total_items', "searchable": false},
                    { data: 'types_of_service_name', name: 'tos.name', @if(empty($is_types_service_enabled)) visible: false @endif},
                    { data: 'service_custom_field_1', name: 'service_custom_field_1', @if(empty($is_types_service_enabled)) visible: false @endif},
                    { data: 'added_by', name: 'u.first_name'},
                    { data: 'additional_notes', name: 'additional_notes'},
                    { data: 'staff_note', name: 'staff_note'},
                    { data: 'shipping_details', name: 'shipping_details'},
                    { data: 'table_name', name: 'tables.name', @if(empty($is_tables_enabled)) visible: false @endif },
                    { data: 'waiter', name: 'ss.first_name', @if(empty($is_service_staff_enabled)) visible: false @endif },
                ],
                "fnDrawCallback": function (oSettings) {
                    __currency_convert_recursively($('#sell_table'));
                },
                "footerCallback": function ( row, data, start, end, display ) {
                    var footer_sale_total = 0;
                    var footer_total_paid = 0;
                    var footer_total_remaining = 0;
                    var footer_total_sell_return_due = 0;
                    for (var r in data){
                        footer_sale_total += $(data[r].final_total).data('orig-value') ? parseFloat($(data[r].final_total).data('orig-value')) : 0;
                        footer_total_paid += $(data[r].total_paid).data('orig-value') ? parseFloat($(data[r].total_paid).data('orig-value')) : 0;
                        footer_total_remaining += $(data[r].total_remaining).data('orig-value') ? parseFloat($(data[r].total_remaining).data('orig-value')) : 0;
                        footer_total_sell_return_due += $(data[r].return_due).data('orig-value') ? parseFloat($(data[r].return_due).data('orig-value')) : 0;
                    }

                    $('.footer_total_sell_return_due').html(__currency_trans_from_en(footer_total_sell_return_due));
                    $('.footer_total_remaining').html(__currency_trans_from_en(footer_total_remaining));
                    $('.footer_total_paid').html(__currency_trans_from_en(footer_total_paid));
                    $('.footer_sale_total').html(__currency_trans_from_en(footer_sale_total));

                    $('.footer_payment_status_count').html(__count_status(data, 'payment_status'));
                    $('.service_type_count').html(__count_status(data, 'types_of_service_name'));
                    $('.payment_method_count').html(__count_status(data, 'payment_methods'));
                },
                createdRow: function( row, data, dataIndex ) {
                    $( row ).find('td:eq(6)').attr('class', 'clickable_td');
                }
            });

            $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status',  function() {
                sell_table.ajax.reload();
            });

            @if($is_woocommerce)
            $('#synced_from_woocommerce').on('ifChanged', function(event){
                sell_table.ajax.reload();
            });
            @endif

            $('#only_subscriptions').on('ifChanged', function(event){
                sell_table.ajax.reload();
            });



            $(document).on('click','.add_installment_modal',function (e) {
                e.preventDefault();
                var contact_id=$('#customer_id').val();
                if(contact_id==''){
                    toastr.error('عفوا برجاء إختيار العميل');
                    return true;
                }

                var url=$(this).attr('href');
                $.ajax({
                    method: 'GET',
                    url: url,
                    dataType: 'html',
                    success: function (result) {
                        $(".div_modal").html(result).modal('show');


                    },
                });

            });





        });





    </script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection