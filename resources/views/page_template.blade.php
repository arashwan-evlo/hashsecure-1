@extends('layouts.app')
@section('title','chartofaccounts')

@section('content')
    <section class="content-header no-print">
        <h1>@lang( 'sale.sells')
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('only_woocommerce_sells', 1, false,
                            [ 'class' => 'input-icheck', 'id' => 'synced_from_woocommerce']); !!} {{ __('lang_v1.synced_from_woocommerce') }}
                        </label>
                    </div>
                </div>
            </div>

        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_sales')])
            @can('sell.create')
                @slot('tool')
                    <div class="box-tools">
                        <a class="btn btn-block btn-primary" href="{{action('SellController@create')}}">
                            <i class="fa fa-plus"></i> @lang('messages.add')</a>
                    </div>
                @endslot
            @endcan
            @if(auth()->user()->can('direct_sell.access') ||  auth()->user()->can('view_own_sell_only') ||  auth()->user()->can('view_commission_agent_sell'))
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
                        <th>@lang('lang_v1.sell_return_due')</th>
                        <th>@lang('sale.total_paid')</th>
                        <th>@lang('lang_v1.sell_due')</th>

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

    <div class="modal fade payment_modal" tabindex="-1" role="dialog"
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
        $(document).ready(function () {




        });
        function addchartaccount() {
            $.ajax({
                url: '/chartofaccounts/addaccount',
                dataType: 'html',
                success: function(result) {
                    $('#modeldiv').html(result).modal('show');
                },
            });
        }

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

        function getaccount(id) {
            var account_id=id;
            $.ajax({
                url: '/chartofaccounts/getaccount',
                type:'GET',
                data:{
                    id:account_id
                },
                success: function(result) {
                    $('#acount-chiled').html(result.html);

                },
            });

        }



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
