<tr class="product_row">
    <td>
        <div title="@lang('lang_v1.pos_edit_product_price_help')">
		<span class="text-link text-info cursor-pointer" data-toggle="modal"
              data-target="#row_edit_product_price_modal_{{$row_index}}">
			{!! $product->product_name !!}
       	</span>
        </div>
        <br/>
        {{$product->sub_sku}}
        <div class="modal fade row_edit_product_price_model" id="row_edit_product_price_modal_{{$row_index}}" tabindex="-1" role="dialog">
            @include('stock_inout.partials.row_edit_product_price_modal')
        </div>

    </td>
  {{--<td>
        <input type="text" name="products[{{$row_index}}][sea_number]" class="form-control product_unit_price input_number" value="0">

    </td>
    <td>
        <input type="text" name="products[{{$row_index}}][batch_number]" class="form-control product_unit_price input_number" value="0">

    </td>
    <td>
       <input type="text" name="products[{{$row_index}}][exp_date]" class="form-control expiry_datepicker exp_date" value="">

    </td>



    <td>
        <input type="text" name="products[{{$row_index}}][baleta_number]" class="form-control product_unit_price input_number" value="0">

    </td>--}}
    <td>
        {{--<input type="text" readonly name="products[{{$row_index}}][price]" class="form-control " value="{{@num_format(0.0)}}">
        --}}
        {!! Form::select('products['.$row_index.'][location_group_id]', $sub_location, null, ['class' => 'form-control select2', 'required']); !!}

    </td>



    <td>
        {{-- If edit then transaction sell lines will be present --}}
        @if(!empty($product->transaction_sell_lines_id))
            <input type="hidden" name="products[{{$row_index}}][transaction_sell_lines_id]" class="form-control" value="{{$product->transaction_sell_lines_id}}">
        @endif

        <input type="hidden" name="products[{{$row_index}}][product_id]" class="form-control product_id" value="{{$product->product_id}}">

        <input type="hidden" value="{{$product->variation_id}}"
            name="products[{{$row_index}}][variation_id]">

        <input type="hidden" value="{{$product->enable_stock}}"
            name="products[{{$row_index}}][enable_stock]">

        @if(empty($product->quantity_ordered))
            @php
                $product->quantity_ordered = 1;
            @endphp
        @endif

        <input type="text" class="form-control product_quantity input_number input_quantity" value="{{@format_quantity($product->quantity_ordered)}}" name="products[{{$row_index}}][quantity]"
        @if($product->unit_allow_decimal == 1) data-decimal=1 @else data-rule-abs_digit="true" data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')" data-decimal=0 @endif
        data-rule-required="true" data-msg-required="@lang('validation.custom-messages.this_field_is_required')"
        >
        {{--{{$product->unit}}--}}
        <input type="hidden" name="products[{{$row_index}}][unit_price]" class="form-control product_unit_price input_number" value="{{@num_format($product->last_purchased_price)}}">
    </td>

    <td class="text-center">
           <i class="fa fa-times remove_product_row  cursor-pointer btn btn-danger" aria-hidden="true"></i>
    </td>
</tr>