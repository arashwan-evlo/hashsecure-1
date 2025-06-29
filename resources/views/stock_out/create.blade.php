@extends('layouts.app')
@section('title','إذن صرف')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <br>
        <h1>إذن صرف</h1>

    </section>

    <!-- Main content -->
    <section class="content no-print">
        {!! Form::open(['url' => action('StockInoutController@store'), 'method' => 'post', 'id' => 'stock_inout_form' ]) !!}
        <input type="hidden" name="stock_type" value="stock_out">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
            <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('location_id', __('purchase.business_location').':*') !!}
                            {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('ref_no', __('purchase.ref_no').':') !!}
                            {!! Form::text('ref_no', null, ['class' => 'form-control']); !!}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('transaction_date', __('messages.date') . ':*') !!}
                            <div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
                                {!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required']); !!}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div> <!--box end-->
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">{{ __('stock_adjustment.search_products') }}</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4 ">
                        <div class="form-group">
                            <div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-search"></i>
							</span>
                                {!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_srock_adjustment', 'placeholder' => __('stock_adjustment.search_product'), 'disabled']); !!}
                            </div>
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <input type="hidden" id="product_row_index" value="0">
                        <input type="hidden" id="total_amount" name="final_total" value="0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-condensed"
                                   id="stock_adjustment_product_table">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 40%" >
                                        @lang('sale.product')
                                    </th>
                                    <th class="text-center" style="width: 50px">
                                        @lang('sale.qty') الأصل
                                    </th>
                                    <th class="col-sm-3 text-center">
                                        @lang('sale.qty')
                                    </th>
                                    <th class=" text-center" style="width: 50px"><i class="fa fa-times" aria-hidden="true"></i></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!--box end-->
        <div class="box box-solid ">
            <div class="box-body">
                <div class="row hidden">
                    <div class="col-sm-4  ">
                        <div class="form-group">
                            {!! Form::label('total_amount_recovered', __('stock_adjustment.total_amount_recovered') . ':') !!}
                            @show_tooltip(__('tooltip.total_amount_recovered'))
                            {!! Form::text('total_amount_recovered', 0, ['class' => 'form-control input_number', 'placeholder' => __('stock_adjustment.total_amount_recovered')]); !!}
                        </div>
                    </div>
                    <div class="col-sm-4  ">
                        <div class="form-group">
                            {!! Form::label('additional_notes', __('stock_adjustment.reason_for_stock_adjustment') . ':') !!}
                            {!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'placeholder' => __('stock_adjustment.reason_for_stock_adjustment'), 'rows' => 3]); !!}
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
                    </div>
                </div>

            </div>
        </div> <!--box end-->
        {!! Form::close() !!}

        <div class="row" style="border: 1px solid #979090;padding: 20px 10px;border-radius: 10px">
            <div class="col-md-12">
             <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed"
                       id="stock_out_table">
                    <thead>
                    <tr>
                        <th class="text-center"  >  المنتج</th>
                        <th class="text-center" > تاريخ الإضافة</th>
                        <th class=" text-center">رقم LPN </th>
                        <th class=" text-center">الرقم البحري </th>
                        <th class=" text-center"> رقم الباتش</th>
                        <th class=" text-center" >عدد الوحدات الموجودة في البالتة</th>
                        <th class=" text-center"> تاريخ الإنتاج</th>
                        <th class=" text-center"> تاريخ الإنتهاء</th>
                        <th class=" text-center"> المخزن الفرعي</th>
                        <th class=" text-center"> الكمية</th>
                        <th class=" text-center" ><i class="fa fa-times" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </section>
@stop
@section('javascript')
    <script src="{{ asset('js/stock_inout.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        __page_leave_confirmation('#stock_adjustment_form');

        $(document).ready(function() {
            stock_out_table = $('#stock_out_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [1, 'desc']
                ],

                ajax: {
                    url: "/stock_products",
                    type: 'GET',
                    data: function (d) {

                    },
                },
                columns: [

                    {data: 'name', name: 'products.name'},
                    {data: 'transaction_date', name: 'transactions.transaction_date'},
                    {data: 'lpn_number', name: 'TSL.lpn_number'},
                    {data: 'sea_number', name: 'TSL.sea_number'},
                    {data: 'batch_number', name: 'TSL.batch_number'},
                    {data: 'baleta_number', name: 'TSL.baleta_number'},
                    {data: 'production_date', name: 'TSL.production_date'},
                    {data: 'exp_date', name: 'TSL.exp_date'},
                    {data: 'sublocation', name: 'sublocation', orderable: false, searchable: false},
                    {data: 'quantity', name: 'quantity', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},

                ],

            });



        });

        function stock_product_row(variation_id) {
            var row_index = parseInt($('#product_row_index').val());
            var location_id = $('select#location_id').val();

            $.ajax({
                method: 'POST',
                url: '/stock-inout/get_product_row',
                data: { row_index: row_index, variation_id: variation_id, location_id: location_id },
                dataType: 'html',
                success: function(result) {
                    $('table#stock_adjustment_product_table tbody').append(result);
                    update_table_total();
                    toastr.success("تم إضافة المنتج إلي إذن الصرف");
                    $('#product_row_index').val(row_index + 1);
                },
            });
        }


    </script>
@endsection
