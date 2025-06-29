<!-- business information here -->

<div class="row">

    <!-- Logo -->
    @if(!empty($receipt_details->logo))
        <img style="max-height: 90%; width: auto;" src="{{$receipt_details->logo}}" class="img img-responsive center-block">
    @endif

    <!-- Header text -->
    @if(!empty($receipt_details->header_text))
        <div class="col-xs-12">
            {!! $receipt_details->header_text !!}
        </div>
    @endif

    <!-- business information here -->
    <div class="col-xs-12 text-center">

        <!-- Shop & Location Name  -->
        @if(!empty($receipt_details->display_name))
            <h2 class="text-center">
                {{$receipt_details->display_name}}
            </h2>
        @endif
        <!-- Address -->
      <p>
            @if(!empty($receipt_details->address))
                <small class="text-center">
                    {!! $receipt_details->address !!}
                </small>
            @endif
            @if(!empty($receipt_details->contact))
                <br/>{!! $receipt_details->contact !!}
            @endif

            @if(!empty($receipt_details->location_custom_fields))
                <br>{{ $receipt_details->location_custom_fields }}
            @endif
         </p>
         <p>
            @if(!empty($receipt_details->sub_heading_line1))
                {{ $receipt_details->sub_heading_line1 }}
            @endif
            @if(!empty($receipt_details->sub_heading_line2))
                <br>{{ $receipt_details->sub_heading_line2 }}
            @endif
            @if(!empty($receipt_details->sub_heading_line3))
                <br>{{ $receipt_details->sub_heading_line3 }}
            @endif
            @if(!empty($receipt_details->sub_heading_line4))
                <br>{{ $receipt_details->sub_heading_line4 }}
            @endif
            @if(!empty($receipt_details->sub_heading_line5))
                <br>{{ $receipt_details->sub_heading_line5 }}
            @endif
        </p>

        <p>
            @if(!empty($receipt_details->tax_info1))
                <b>{{ $receipt_details->tax_label1 }}</b> {{ $receipt_details->tax_info1 }}
            @endif

            @if(!empty($receipt_details->tax_info2))
                <b>{{ $receipt_details->tax_label2 }}</b> {{ $receipt_details->tax_info2 }}
            @endif
        </p>
      </div>

</div>

<div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6" style="border: #0c0c0c solid 1px;height: 200px"></div>
    <div class="col-md-6 col-sm-6 col-xs-6" style="border: #0c0c0c solid 1px;height: 200px"></div>
</div>


    <table class="table border " style="width: 100%;margin-top:10px">
        <tbody>
        <tr style="border: 1px solid;">
            <td>Test</td>
            <td>Test</td>
        </tr>
        <tr style="border: 1px solid #0c0c0c;">
            <td>Test</td>
            <td></td>
        </tr>
        </tbody>
    </table>



<div class="row">
    @includeIf('sale_pos.receipts.partial.common_repair_invoice')
</div>

<div class="row">
    <div class="col-xs-12">
        <br/>
        @php
            $p_width = 40;
        @endphp
        @if(!empty($receipt_details->item_discount_label))
            @php
                $p_width -= 15;
            @endphp
        @endif

        {{--Table of products--}}
        <table class="table table-responsive table-slim">
            <thead>
            <tr>
                <th width="{{$p_width}}%">{{$receipt_details->table_product_label}}</th>
                <th class="text-right" width="15%">{{$receipt_details->table_qty_label}}</th>
                <th class="text-right" width="15%">{{$receipt_details->table_unit_price_label}}</th>
                @if(!empty($receipt_details->item_discount_label))
                    <th class="text-right" width="15%">{{$receipt_details->item_discount_label}}</th>
                @endif
                <th class="text-right" width="15%">{{$receipt_details->table_subtotal_label}}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($receipt_details->lines as $line)
                <tr>
                    <td>
                        @if(!empty($line['image']))
                            <img src="{{$line['image']}}" alt="Image" width="50" style="float: left; margin-right: 8px;">
                        @endif
                        {{$line['name']}} {{$line['product_variation']}} {{$line['variation']}}
                        @if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif @if(!empty($line['brand'])), {{$line['brand']}} @endif @if(!empty($line['cat_code'])), {{$line['cat_code']}}@endif
                        @if(!empty($line['product_custom_fields'])), {{$line['product_custom_fields']}} @endif
                        @if(!empty($line['sell_line_note']))
                            <br>
                            <small>
                                {{$line['sell_line_note']}}
                            </small>
                        @endif
                        @if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif
                        @if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif

                        @if(!empty($line['warranty_name'])) <br><small>{{$line['warranty_name']}} </small>@endif @if(!empty($line['warranty_exp_date'])) <small>- {{@format_date($line['warranty_exp_date'])}} </small>@endif
                        @if(!empty($line['warranty_description'])) <small> {{$line['warranty_description'] ?? ''}}</small>@endif
                    </td>
                    <td class="text-right">{{$line['quantity']}}{{$line['units']}} </td>
                    <td class="text-right">{{$line['unit_price_inc_tax']}}</td>
                    @if(!empty($receipt_details->item_discount_label))
                        <td class="text-right">
                            {{$line['line_discount'] ?? '0.00'}}
                        </td>
                    @endif
                    <td class="text-right">{{$line['line_total']}}</td>
                </tr>
                @if(!empty($line['modifiers']))
                    @foreach($line['modifiers'] as $modifier)
                        <tr>
                            <td colspan="5">
                                {{$modifier['name']}} {{$modifier['variation']}}
                                @if(!empty($modifier['sub_sku'])), {{$modifier['sub_sku']}} @endif @if(!empty($modifier['cat_code'])), {{$modifier['cat_code']}}@endif
                                @if(!empty($modifier['sell_line_note']))({{$modifier['sell_line_note']}}) @endif
                            </td>

                        </tr>
                    @endforeach
                @endif

                @if(empty($receipt_details->item_discount_label) && $line['line_discount']>0)
                    <tr>
                        <td>
                            @lang('sale.discount'):{{$line['line_discount'] }}
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>


            @endforelse


            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-12"><hr/></div>
    <div class="col-xs-6">

        <table class="table table-slim">
            @if(!empty($receipt_details->payments))
                @foreach($receipt_details->payments as $payment)
                    <tr>
                        <td>{{$payment['method']}}</td>
                        <td class="text-right" >{{$payment['amount']}}</td>
                        <td class="text-right">{{$payment['date']}}</td>
                    </tr>
                @endforeach
            @endif

            <!-- Total Paid-->
            @if(!empty($receipt_details->total_paid_label))
                <tr>
                    <th>
                        {!! $receipt_details->total_paid_label !!}
                    </th>
                    <td class="text-right">
                        {{$receipt_details->total_paid}}
                    </td>
                </tr>
            @endif

            <!-- Total Due-->
            @if(!empty($receipt_details->total_due_label))
                <tr>
                    <th>
                        {!! $receipt_details->total_due_label !!}
                    </th>
                    <td class="text-right">
                        {{$receipt_details->total_due}}
                    </td>
                </tr>
            @endif

            @if(!empty($receipt_details->all_due))
                <tr>
                    <th>
                        {!! $receipt_details->all_bal_label !!}
                    </th>
                    <td class="text-right">
                        {{$receipt_details->all_due}}
                    </td>
                </tr>
            @endif
        </table>
    </div>

    <div class="col-xs-6">
        <div class="table-responsive">
            <table class="table table-slim">
                <tbody>
                @if(!empty($receipt_details->total_quantity_label))
                    <tr class="color-555">
                        <th >
                            {!! $receipt_details->total_quantity_label !!}
                        </th>
                        <td class="text-right">
                            {{$receipt_details->total_quantity}}
                        </td>
                    </tr>
                @endif
                <tr>
                    <th >
                        {!! $receipt_details->subtotal_label !!}
                    </th>
                    <td class="text-right">
                        {{$receipt_details->subtotal}}
                    </td>
                </tr>
                @if(!empty($receipt_details->total_exempt_uf))
                    <tr>
                        <th>
                            @lang('lang_v1.exempt')
                        </th>
                        <td class="text-right">
                            {{$receipt_details->total_exempt}}
                        </td>
                    </tr>
                @endif
                <!-- Shipping Charges -->
                @if(!empty($receipt_details->shipping_charges))
                    <tr>
                        <th >
                            {!! $receipt_details->shipping_charges_label !!}
                        </th>
                        <td class="text-right">
                            {{$receipt_details->shipping_charges}}
                        </td>
                    </tr>
                @endif

                @if(!empty($receipt_details->packing_charge))
                    <tr>
                        <th >
                            {!! $receipt_details->packing_charge_label !!}
                        </th>
                        <td class="text-right">
                            {{$receipt_details->packing_charge}}
                        </td>
                    </tr>
                @endif

                <!-- Discount -->
                @if( !empty($receipt_details->discount)  )
                    <tr>
                        <th>
                            {!! $receipt_details->discount_label !!}
                        </th>

                        <td class="text-right">
                            (-) {{$receipt_details->total_discount}}
                        </td>
                    </tr>
                @endif

                <!-- Tax -->
                @if( !empty($receipt_details->tax_label) )
                    <tr>
                        <th >
                            {!! $receipt_details->tax_label !!}
                        </th>
                        <td class="text-right">
                            (+) {{$receipt_details->tax}}
                        </td>
                    </tr>
                @endif

                @if( !empty($receipt_details->reward_point_label)  )
                    <tr>
                        <th>
                            {!! $receipt_details->reward_point_label !!}
                        </th>

                        <td class="text-right">
                            (-) {{$receipt_details->reward_point_amount}}
                        </td>
                    </tr>
                @endif

                @if( !empty($receipt_details->transaction_add) )
                    <tr>
                        <th>
                            {!! $receipt_details->transaction_add !!}
                        </th>

                        <td class="text-right">
                            {{$receipt_details->transaction_add_value}}
                        </td>
                    </tr>

                    {{--<tr>
                        <th>
                            صافي قيمة المستخلص الحالي
                        </th>

                        <td class="text-right">
                            {{ $receipt_details->transaction_add_total}}
                        </td>
                    </tr>--}}


                @endif




                @if( $receipt_details->round_off_amount > 0)
                    <tr>
                        <th>
                            {!! $receipt_details->round_off_label !!}
                        </th>
                        <td class="text-right">
                            {{$receipt_details->round_off}}
                        </td>
                    </tr>
                @endif

                <!-- Total -->
                <tr>
                    <th>
                        {!! $receipt_details->total_label !!}
                    </th>
                    <td class="text-right">
                        {{$receipt_details->transaction_add_total}}
                        @if(!empty($receipt_details->total_in_words))
                            <br>
                            <small>({{$receipt_details->total_in_words}})</small>
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-xs-12">
        <p>{!! nl2br($receipt_details->additional_notes) !!}</p>
    </div>


</div>

@if($receipt_details->show_barcode )
    <div class="@if(!empty($receipt_details->footer_text)) col-xs-4 @else col-xs-12 @endif text-center">
        @if($receipt_details->show_barcode)
            {{-- Barcode --}}
            <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}">
        @endif

    </div>
@endif

@include('sale_pos.partials.qr_code')
<br>
<div class="row">
    @if(!empty($receipt_details->footer_text))
        <div class="col-xs-12 ">
            {!! $receipt_details->footer_text !!}
        </div>
    @endif

</div>
