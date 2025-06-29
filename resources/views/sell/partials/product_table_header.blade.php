<thead>
<tr>
    <th class="text-center">
        @lang('sale.product')
    </th>
    <th class="text-center" style="width: 150px">
        @lang('sale.qty')
    </th>
    @if(!empty($pos_settings['enable_sub_location']))
        <th class="text-center">
            المخزن الفرعي
        </th>
    @endif


    @if(!empty($pos_settings['inline_service_staff']))
        <th class="text-center">
            @lang('restaurant.service_staff')
        </th>
    @endif

    @can('edit_product_price_from_sale_screen')
        <th style="width: 100px">@lang('sale.unit_price')</th>
    @endcan

    @can('edit_product_discount_from_sale_screen')
        <th style="width: 100px">
            @lang('receipt.discount')
        </th>
    @endcan

    <th class="text-center {{$hide_tax}}" style="width: 100px">
        @lang('sale.tax')
    </th>

    <th class="text-center hide" style="width: 100px">
        @lang('sale.price_inc_tax')
    </th>

    @if(!empty($warranties))
        <th style="width: 100px" >@lang('lang_v1.warranty')</th>
    @endif

    <th class="text-center" style="width: 110px">
        @lang('sale.subtotal')
    </th>

    <th class="text-center">
        <i class="fas fa-times" aria-hidden="true"></i>
    </th>
</tr>
</thead>