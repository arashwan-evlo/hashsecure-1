@extends('layouts.app')
@section('title', __('manufacturing::lang.production'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('manufacturing::lang.production')</h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
       <div class="col-md-3">
           <div class="form-group">
               {!! Form::label('date_range','حالة الصنيع:') !!}
               <select class="form-control select2" name="mfg_is_final" id="mfg_is_final">
                   <option value="">الكل</option>
                   <option value="0">مطلوب التصنيع</option>
                   <option value="1">تم التصنيع</option>
               </select>
           </div>
       </div>



        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_range', __('report.date_range') . ':') !!}
                {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>

<div class="clearfix"></div>

            <a class="btn  btn-primary" href="{{action('\Modules\Manufacturing\Http\Controllers\ProductionController@create')}}">
                <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>

    @endcomponent
    @component('components.widget', ['class' => 'box-primary'])
       <div class="table-responsive">
            <table class="table table-bordered table-striped" id="productions_table">
                 <thead>
                    <tr>
                        <th>@lang('messages.date')</th>
                        <th>@lang('purchase.ref_no')</th>
                        <th>@lang('purchase.location')</th>
                        <th>@lang('sale.product')</th>
                        <th>@lang('lang_v1.quantity')</th>
                        <th>@lang('manufacturing::lang.total_cost')</th>
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
<!-- /.content -->
<div class="modal fade" id="recipe_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
@stop
@section('javascript')
    @include('manufacturing::layouts.partials.common_script')
    <script type="text/javascript">
        $(document).ready( function() {
              });

    </script>

@endsection
