@extends('layouts.app')
@section('title',__('chartofaccounts::lang.payment_receipt'))

@section('content')
    @include('chartofaccounts::layouts.style')
    <!-- Main content -->
    <section class="content">
        <section class="content ">
            @if(!empty($account->id))
                <h4 class="modal-title mb-10">@lang( 'chartofaccounts::lang.edit_payment_receipt' ) : <span style="color: #6F1212ED;">{{$account->account_code}} - {{$account->name}}</span></h4>
            @else
                <h4 class="modal-title mb-10">@lang( 'messages.add' ) @lang(('chartofaccounts::lang.payment_receipt'))</h4>
            @endif


            @component('components.widget', ['class' => 'box-primary', 'title' => __('chartofaccounts::lang.payment_receipt')])
                 {!! Form::open(['url' => action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@payment_receipt_save'),
                   'method' => 'post','id' => 'cash_receipt_save', 'files' => true ]) !!}


                        <input type="hidden" value="{{$account->id}}" class="form-control" name="account_id" id="account_id">
                            <div class="row">

                                <?php
                                $disabled='';
                                if($account->parent_id==0 && $account->id){
                                    $disabled='disabled';
                                    $account->parent_id=$account->id;
                                }

                                ?>
                                <div class="col-md-2 hidden">
                                    <div class="form-group">
                                        {!! Form::label('account_code','كود المستند:') !!}
                                        {!! Form::text('account_code', $account->account_code, ['class' => 'form-control','id'=>'account_code' ]); !!}
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-3 ">
                                    <div class="form-group ">
                                        {!! Form::label('account_type','جهة التعامل : ' ) !!}
                                        <select class="form-control select2" id="account_type" name="account_type">
                                            <option value="sub_account" @if($account_type=='sub_account') selected @endif>حساب فرعي</option>
                                            <option value="customer" @if($account_type=='customer') selected @endif> حسابات العملاء</option>
                                            <option value="supplier" @if($account_type=='supplier') selected @endif>حسابات الموردين</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 @if($account_type!='sub_account') hidden @endif" id="sub_account_dev">
                                    <div class="form-group">
                                        {!! Form::label('sub_account', __('chartofaccounts::lang.chiled_account') ) !!}
                                        {!! Form::select('sub_account', $accounts,$account_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sub_account' ]); !!}
                                    </div>
                                </div>
                                <div class="col-md-3   @if($account_type!='customer') hidden @endif" id="customer_dev">
                                    <div class="form-group">
                                        {!! Form::label('customer', __('chartofaccounts::lang.customer_account') ) !!}
                                        {!! Form::select('customer', $customers,$account_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'customer' ]); !!}
                                    </div>
                                </div>

                                <div class="col-md-3   @if($account_type!='supplier') hidden @endif" id="supplier_dev">
                                    <div class="form-group">
                                        {!! Form::label('supplier', __('chartofaccounts::lang.supplier_account') ) !!}
                                        {!! Form::select('supplier', $suppliers,$account_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'supplier' ]); !!}
                                    </div>
                                </div>



                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('debit_account', __('chartofaccounts::lang.save_account') ) !!}
                                        {!! Form::select('debit_account', $save_account,$account->parent_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'save_account' ]); !!}
                                    </div>
                                </div>

                                <div class="clearfix"></div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {!! Form::label('amount', __( 'chartofaccounts::lang.receipt_amount' ) . ':*') !!}
                                        {!! Form::text('amount', $account->name, ['class' => 'form-control', 'required' ]); !!}
                                    </div>
                                </div>




                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('transaction_date', __( 'messages.date' ) .":*") !!}
                                        <div class="input-group date" id='od_datetimepicker'>
                                            {!! Form::text('transaction_date', $currentdate, ['class' => 'form-control', 'required','placeholder' => __( 'messages.date'),'readonly' ]); !!}
                                            <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="clearfix"></div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                                        {!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                                        {{-- <p class="help-block">
                                             @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                             @includeIf('components.document_help_text')
                                         </p>--}}
                                    </div>
                                </div>

                               {{-- <div class="col-lg-12 mt-15">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>جهة التعامل</th>
                                            <th>الحساب الفرعي</th>
                                            <th>الوصف</th>
                                            <th>المبلغ</th>
                                            <th>مركز التكلفة</th>
                                        </tr>

                                        </thead>
                                    </table>
                                </div>--}}



                                <div class="col-lg-12 mt-15">
                                    {!! Form::label('additional_notes','ملاحظات: ') !!}
                                    <textarea id="additional_notes" class="form-control" name="additional_notes" rows="4" cols="50"></textarea>

                                </div>

                            </div>
                 <br>
                            <button type="submit" class="btn btn-primary ">@lang( 'messages.save' )</button>


                        {!! Form::close() !!}


            @endcomponent



            <div class="modal fade brands_modal" tabindex="-1" role="dialog"
                 aria-labelledby="gridSystemModalLabel">
            </div>
        </section>
        @endsection

        @section('javascript')
        @include('chartofaccounts::javascript')

 <script>

 $(document).ready( function(){
            $('#od_datetimepicker').datepicker({

            });
        });

 $(document).on('change','#account_type',function (){
     var account_type=$('#account_type').val();
     $('#sub_account_dev').addClass('hidden');
     $('#customer_dev').addClass('hidden');
     $('#supplier_dev').addClass('hidden');

     if(account_type==="sub_account"){
         $('#sub_account_dev').removeClass('hidden');
     }
     if(account_type==="customer") {
         $('#customer_dev').removeClass('hidden');
     }
     if(account_type==="supplier") {
         $('#supplier_dev').removeClass('hidden');
     }

 });

 $(document).on('submit', 'form#cash_receipt_save', function(e) {
     e.preventDefault();
     var form = $(this);
     var data_1 = form.serialize();
     var data = new FormData(this); // Create FormData object
     $.ajax({
         method: 'POST',
         url: $(this).attr('action'),
         dataType: 'json',
         data: data,
         contentType: false,
         processData: false,
         beforeSend: function(xhr) {
             __disable_submit_button(form.find('button[type="submit"]'));
         },
         success: function(result) {
             if (result.success == true) {
               toastr.success(result.msg);
                 setTimeout(function() {
                     window.location = '/journal/payment_receipt';
                 }, 4000);
             } else {
                 toastr.error(result.msg);
                 __enable_submit_button(form.find('button[type="submit"]'));
             }
         },
     });
 });


 </script>
@endsection