<!-- business information here -->

@include('sale_pos.receipts.invoice_style')
<style>
    .table-1>tbody>tr>td{
      border: 1px solid #000000!important;
      padding: 5px;
    }

    .product-div{
        margin-top: 10px;
    }
    .product-table>thead>tr>th{
        border-right: 1px solid #0c0c0c;
        border-left: 1px solid #0c0c0c;
        border-bottom:  1px solid #0c0c0c;
        border-top:  1px solid #0c0c0c;
    }

    .product-table>tbody>tr>td{
        padding: 3px;
     border-right: 1px solid #0c0c0c;
     border-left: 1px solid #0c0c0c;
    border-bottom:  1px dashed #0c0c0c;
    border-top:  1px dashed #0c0c0c;
    }

    .table-total>tbody>tr>th,
    .table-total>tbody>tr>td{
        padding: 3px;
        border-bottom:  1px dashed #0c0c0c;
        border-top:  1px dashed #0c0c0c;
    }

</style>
<div class="invoice-container" style="width: 90%;margin: auto">



        <!-- Logo -->

        @if(!empty($receipt_details->logo))
        <div >
            <img style="max-height: 120px; width: auto;" src="{{$receipt_details->logo}}"
                 class="img img-responsive center-block">
        </div>
        @endif

        <!-- Header text -->
        <div class="text-center">
          <span style="font-size: 20px;font-weight: bold;margin-top: 5px"> {{ $receipt_details->sub_heading_line1 }} -  {{ $receipt_details->sub_heading_line2 }}</span>
        </div>
        <!-- business information here -->
        <div class=" text-center">
            <h2 class="text-center">
                <!-- Shop & Location Name  -->
                @if(!empty($receipt_details->display_name))
                    {{$receipt_details->display_name}}
                @endif
            </h2>


            <table class=" table table-1" style="width: 100%;border: 1px solid #000000;margin: auto">
                <tr class="text-right"  >
                    <td style="width: 65px">إسم العميل</td>
                    <td>
                        {!! $receipt_details->customer_name !!}
                    </td>
                </tr>

                <tr class="text-right"  >
                    <td style="width: 65px">العنوان</td>
                    <td>
                        {{$receipt_details->address_line_1}} {{$receipt_details->address_line_2}}
                    </td>
                </tr>

                <tr class="text-right"  >
                    <td style="width: 65px">رقم الموبايل</td>
                    <td>
                      {{$receipt_details->customer_mobile}}
                    </td>
                </tr>

                <tr class="text-right"  >
                    <td style="width: 65px"> رقم الفاتورة</td>
                    <td>
                        {{$receipt_details->invoice_no}} | {{$receipt_details->invoice_date}}
                    </td>
                </tr>


            </table>

    </div>



       <div class="product-div">
             {{--Table of products--}}
            <table class=" product-table" style="border-bottom: 1px solid #000000!important;">
                <thead>
                <tr>
                    <th  class="text-right" style="padding-right: 2px"> {{$receipt_details->table_product_label}}</th>
                    <th  class="text-center" style="width: 40px">{{$receipt_details->table_qty_label}}</th>
                    <th  class="text-center" style="width: 60px">{{$receipt_details->table_unit_price_label}}</th>
                    <th  class="text-center" style="width: 65px">{{$receipt_details->table_subtotal_label}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($receipt_details->lines as $line)
                    <tr>
                        <td style="padding-right: 2px">
                            {{$line['name']}}
                        </td>
                        <td class="text-center">{{$line['quantity']}} </td>
                        <td class="text-center">{{$line['unit_price_inc_tax']}}</td>
                        <td class="text-center">{{$line['line_total']}}</td>
                    </tr>
               @endforeach
         </tbody>
            </table>

    </div>


        <div  class="product-div">
                <table class="table-total" style="border: 1px solid #000000">
                    <tbody>
                     <tr>
                        <th class="text-right" >
                            {!! $receipt_details->subtotal_label !!}
                        </th>
                        <td class="text-left">
                            {{$receipt_details->subtotal}}
                        </td>
                    </tr>
                      <!-- Discount -->
                    @if( !empty($receipt_details->discount)  )
                        <tr>
                            <th class="text-right" >
                                {!! $receipt_details->discount_label !!}
                            </th>

                            <td class="text-left">
                                (-) {{$receipt_details->total_discount}}
                            </td>
                        </tr>
                    @endif

                    <!-- Tax -->
                    {{--@if( !empty($receipt_details->tax_label) )
                        <tr>
                            <th class="text-right" >
                                {!! $receipt_details->tax_label !!}
                            </th>
                            <td class="text-left">
                                (+) {{$receipt_details->tax}}
                            </td>
                        </tr>
                    @endif--}}

                    <!-- Total -->
                    <tr>
                        <th class="text-right" >
                            {!! $receipt_details->total_label !!}
                        </th>
                        <td class="text-left">
                            {{$receipt_details->transaction_add_total}}
                          </td>
                    </tr>

                @if(!empty($receipt_details->payments))
                    @foreach($receipt_details->payments as $payment)
                        <tr>
                            @if($payment['is_return']==0)
                            <td>طريقة الدفع:  {{$payment['method']}} </td>
                            <td class="text-left">{{$payment['amount']}}</td>
                            @else
                                <td>باقي :  </td>
                                <td class="text-left">{{$payment['amount']}}</td>
                            @endif
                         </tr>
                    @endforeach
                @endif






            </table>
        </div>

        <div class="col-xs-12">
            <p>{!! nl2br($receipt_details->additional_notes) !!}</p>
        </div>




    @if($receipt_details->show_barcode )
        <div class="@if(!empty($receipt_details->footer_text)) col-xs-4 @else col-xs-12 @endif text-center">
            @if($receipt_details->show_barcode)
                {{-- Barcode --}}
                <img class="center-block"
                     src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}">
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


</div>