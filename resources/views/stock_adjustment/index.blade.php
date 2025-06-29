@extends('layouts.app')
@section('title', __('stock_adjustment.stock_adjustments'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('stock_adjustment.stock_adjustments')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => ''])
        @slot('tool')
            <div class="box-tools">
                <a class="btn btn-block btn-danger" href="{{action('StockAdjustmentController@create')}}">
                <i class="fa fa-plus"></i> @lang('messages.add') @lang('stock_adjustment.stock_faulty')</a>
            </div>
            <div class="box-tools mr-8">
                <a class="btn btn-block btn-primary" href="{{action('StockInoutController@stock_out')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add') @lang('stock_adjustment.stock_out')</a>
            </div>

            <div class="box-tools  mr-8">
                <a class="btn btn-block btn-info" href="{{action('StockInoutController@stock_add')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add') @lang('stock_adjustment.stock_addition')</a>
            </div>

        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="stock_adjustment_table">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('business.location')</th>
                        <th>@lang('stock_adjustment.stock_type')</th>
                        <th>@lang('stock_adjustment.total_amount')</th>
                        <th>@lang('lang_v1.added_by')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent

</section>
<!-- /.content -->
@stop
@section('javascript')
	<script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
@endsection