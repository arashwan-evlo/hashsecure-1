<div class="modal-dialog no-print journal" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle"> تفاصيل القيد :  #{{$transaction->ref_no??$transaction->invoice_no}}
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-xs-12">
                    <p><b>تاريخ القيد  : </b>{{$transaction->transaction_date}} </p>
                </div>

                <div class="col-xs-12">
                    <b>الوصف  : </b>
                    {!! $transaction->additional_notes !!}
                </div>
            </div>



            <div class="row">
                <div class="col-sm-12 col-xs-12 mt-15">
                    <b>إجمالي القيد :
                     <span class="display_currency" data-currency_symbol="true"> {{ $transaction->final_total }}</span>
                    </b>
                </div>
                <br>
                <div class="col-md-12 col-sm-12 col-xs-12 mt-15">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr class="bg-green">
                                <th>#</th>
                                <th>رقم الحساب</th>
                                <th>الحساب</th>
                                <th>مدين</th>
                                <th>دائن</th>
                            </tr>
                            @foreach($account_transactions as $data)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td style="text-align: right">
                                        {{$data->account_code}}
                                    </td>
                                    <td >{{$data->account_name}}
                                        @if(!empty($data->contact_name))
                                            ({{$data->contact_name}})
                                        @endif
                                    </td>
                                    <td>
                                        @if($data->type==='debit')
                                            <span class="display_currency" data-currency_symbol="true"> {{$data->amount}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($data->type==='credit')
                                            <span class="display_currency" data-currency_symbol="true"> {{$data->amount}}
                                        @endif
                                    </td>


                                </tr>

                            @endforeach




                        </table>
                    </div>
                </div>

                <br>
                <div class="col-md-12 col-sm-12 col-xs-12 mt-15">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr class="bg-green">
                                <th>المستند</th>
                                 <th style="width: 130px"></th>
                            </tr>
                            @foreach($documents as $data)
                                <tr>

                                    <td style="text-align: right">
                                        {{$data->name}}
                                    </td>

                                   <td>
                                       <a href="/uploads/media/{{$data->file_name}}" target="_blank" class="btn btn-sm btn-primary">تحميل</a>
                                       <a href="" class="btn btn-sm btn-danger">حذف</a>
                                   </td>


                                </tr>

                            @endforeach




                        </table>
                    </div>
                </div>

            </div>

        </div>
        <div class="modal-footer">
           {{-- <a href="#" class="print-invoice btn btn-success" data-href="{{route('sell.printInvoice',1)}}?package_slip=true"><i class="fas fa-file-alt" aria-hidden="true"></i> @lang("lang_v1.packing_slip")</a>

            @can('print_invoice')
                <a href="#" class="print-invoice btn btn-primary" data-href="{{route('sell.printInvoice', 1)}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("lang_v1.print_invoice")</a>
            @endcan--}}
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var element = $('div.journal');
        __currency_convert_recursively(element);
    });
</script>
