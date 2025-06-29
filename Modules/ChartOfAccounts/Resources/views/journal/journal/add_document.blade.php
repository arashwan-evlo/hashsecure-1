@extends('layouts.app')
@section('title',__('chartofaccounts::lang.journal_add'))

@section('content')
    <section class="content-header no-print">
        <h3>@lang('chartofaccounts::lang.journal_add')
        </h3>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary'])
            {!! Form::open(['url' => action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@store_journal'),
                     'method' => 'post','id' => 'journal_save', 'files' => true ]) !!}

        <input type="hidden" name="transaction_id" value="{{$journal->id}}">
            <div class="row">
              <div class="col-md-4">
                  <div class="form-group">
                      {!! Form::label('transaction_date', __( 'messages.date' ) .":*") !!}
                      <div class="input-group date" id='od_datetimepicker'>
                          {!! Form::text('transaction_date',$currentdate, ['class' => 'form-control', 'required','placeholder' => __( 'messages.date'),'readonly' ]); !!}
                          <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                      </div>
                  </div>
              </div>
              <div class="col-md-2">
                       <div class="form-group">
                          {!! Form::label('account_code','كود المستند:') !!}
                          {!! Form::text('account_code',$journal->ref_no, ['class' => 'form-control','id'=>'account_code' ]); !!}
                      </div>
                  </div>
              <div class="clearfix"></div>
              <div class="col-lg-6 ">
                {!! Form::label('additional_notes','الوصف: ') !!}
                <textarea id="additional_notes" class="form-control" name="additional_notes" rows="4" cols="50">{!! $journal->additional_notes !!}</textarea>
            </div>

          </div>
<div style="margin-top: 22px;">

    <?php
        $row_count=1;
    ?>
    <i class="fa fa-plus-circle  add_btn cursor-pointer btn btn-primary" aria-hidden="true"></i>
    <input type="hidden" id="row_count" value="3">

</div>


           <div class="responsive" >
                <table class="table table-bordered table-hover" id="journal_table">
                    <thead>
                    <tr>
                        <th style="width: 220px">جهة التعامل </th>
                        <th>الحساب</th>
                        <th>مركز التكلفة</th>
                        <th style="width: 120px">نسبة مركز التكلفة</th>
                        <th style="width: 100px">مدين ( Debit )</th>
                        <th style="width: 100px">دائن ( Credit )</th>
                        <th style="width: 60px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($journal->id))
                        @foreach($account_transactions as $row )
                            @include('chartofaccounts::journal.journal.journal_row_edit',['row_count'=>$row_count,$row])

                            <?php
                                $row_count=$row_count+1;
                                ?>
                        @endforeach




                        @else
                             @include('chartofaccounts::journal.journal.journal_row',['row_count'=>1])
                             @include('chartofaccounts::journal.journal.journal_row',['row_count'=>2])
                        @endif
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: left">
                            الإجمالي
                        </td>
                         <td style="text-align: center">
                             <span id="debit_total">0.0</span>
                         </td>
                         <td  style="text-align: center">
                             <span id="credit_total">0.0</span>
                         </td>
                        <td>
                             <i class="fa fa-plus-circle  add_btn cursor-pointer btn btn-primary" aria-hidden="true"></i>
                        </td>
                    </tr>
                    </tfoot>
                </table>

               <input type="hidden" name="debit_total" id="debit_total_amount" value="0">
               <input type="hidden" name="credit_total" id="credit_total_amount" value="0">

            </div>

            <br>
            <button type="submit" class="btn btn-primary ">@lang( 'messages.save' )</button>


            {!! Form::close() !!}

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
            journal_total_row();
            $('#od_datetimepicker').datepicker({

            });


        });



        $(document).on('change','.account_type',function (){
            var tr = $(this).closest('tr');

            var account_type= tr.find('select.account_type').val()*1;
            tr.find('.sub_account_dev').addClass('hidden')
            tr.find('.customer_dev').addClass('hidden')
            tr.find('.supplier_dev').addClass('hidden')
            tr.find('.save_account_dev').addClass('hidden')


            if(account_type===1){
                tr.find('.sub_account_dev').removeClass('hidden');
            }
            if(account_type===2) {
                tr.find('.customer_dev').removeClass('hidden');
            }
            if(account_type===3) {
                tr.find('.supplier_dev').removeClass('hidden');
            }
            if(account_type===4) {
                tr.find('.save_account_dev').removeClass('hidden');
            }

        });



        function journal_total_row() {
            var total_debit = 0;
            var total_credit = 0;
             $('table#journal_table tbody tr').each(function() {
                 total_debit = total_debit + __read_number($(this).find('input.amount_debit'));
                 total_credit = total_credit + __read_number($(this).find('input.amount_credit'));

            });


            $('span#debit_total').html(__currency_trans_from_en(total_debit, false));
            $('span#credit_total').html(__currency_trans_from_en(total_credit, false));
            $('#debit_total_amount').val(total_debit)
            $('#credit_total_amount').val(total_credit)

        }

        $(document).on('change','.amount_debit',function (){
            var tr = $(this).closest('tr');
             tr.find('input.amount_credit').val(0);
            journal_total_row();
        });


        $(document).on('change','.amount_credit',function (){
            var tr = $(this).closest('tr');

            tr.find('input.amount_debit').val(0);
            journal_total_row();
        });

        $('#modeldiv').on('shown.bs.modal', function() {
            $('#modeldiv')
                .find('.select2')
                .each(function() {
                    var $p = $(this).parent();
                    $(this).select2({dropdownParent: $p });
                });
        });


        //Remove row on click on remove row
        $('table#journal_table tbody').on('click', 'i.journal_remove_row', function() {
            $(this)
                .parents('tr')
                .remove();
            journal_total_row();
        });

        $(document).on('click','.add_btn',function (){
            var row_count=$('#row_count').val()*1;
            $.ajax({
                url: '/chartofaccounts/journal_row',
                type:'GET',
                data:{
                    row_count:row_count
                },
                success: function(result) {
                    $('table#journal_table tbody').append(result);
                    row_count=row_count+1;
                    $('#row_count').val(row_count);
                    journal_total_row();

                },
            });
        });

        $('table#journal_table tbody').on('click', 'i.pos_remove_row', function() {
            $(this)
                .parents('tr')
                .remove();

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



        $(document).on('submit', 'form#journal_save', function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();
            var total_debit= __read_number($('input#debit_total_amount'));
            var total_credit= __read_number($('input#credit_total_amount'));

            var def=Math.abs(total_debit-total_credit);

            if(def>0.0001){
                toastr.error('إجمالي المدين يجب أن يكون مساوياَ لإجمالي الدائن!');

                __enable_submit_button(form.find('button[type="submit"]'));
                return false;
            }


            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                beforeSend: function(xhr) {
                    __disable_submit_button(form.find('button[type="submit"]'));
                },
                success: function(result) {
                    if (result.success === true) {
                        toastr.success(result.msg);
                        setTimeout(function() {
                            window.location = '/chartofaccounts';
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
