<tr class="journal_row" data-row_index="{{$row_count}}">
    <td>
        <div class="form-group ">
           <input type="hidden" name="journal[{{$row_count}}][account_transactions_id]" value="{{$row->id}}">
            <?php
                  if($row->account_type_id==3){
                      $account_type='sub_account';
                        }

                     if($row->account_type_id==4 && ($row->contact_type=='customer' || $row->contact_type=='both')){

                         $account_type='customer';
                     }

                     if($row->account_type_id==4 && ($row->contact_type=='supplier' || $row->contact_type=='both')){
                         $account_type='supplier';
                     }

                     if($row->account_type_id==6){
                         $account_type='save_account';
                     }
       ?>
      <select class="form-control select2 account_type"  name="journal[{{$row_count}}][account_type]">
                <option value="1" @if($account_type=='sub_account') selected @endif>حساب فرعي</option>
                <option value="2" @if($account_type=='customer') selected @endif> حسابات العملاء</option>
                <option value="3" @if($account_type=='supplier') selected @endif>حسابات الموردين</option>
                <option value="4" @if($account_type=='save_account') selected @endif>حسابات الخزن و البنوك</option>
            </select>
        </div>
    </td>
    <td>
        <div class=" @if($account_type!='sub_account') hidden @endif sub_account_dev ">
            <div class="form-group">

                {!! Form::select('journal['.$row_count.'][sub_account]', $accounts,$row->account_id, ['class' => 'form-control select2 sub_account', 'style' => 'width:100%' ]); !!}
            </div>
        </div>
        <div class="   @if($account_type!='customer') hidden @endif customer_dev" >
            <div class="form-group">

                {!! Form::select('journal['.$row_count.'][customer]', $customers,$row->contact_id, ['class' => 'form-control select2 customer', 'style' => 'width:100%' ]); !!}
            </div>
        </div>
        <div class="   @if($account_type!='supplier') hidden @endif supplier_dev" >
            <div class="form-group">

                {!! Form::select('journal['.$row_count.'][supplier]', $suppliers,$row->contact_id, ['class' => 'form-control select2 supplier', 'style' => 'width:100%' ]); !!}
            </div>
        </div>

        <div class="   @if($account_type!='save_account') hidden @endif save_account_dev">
            <div class="form-group">

                {!! Form::select('journal['.$row_count.'][save_account]', $save_account,$row->account_id, ['class' => 'form-control select2 save_account', 'style' => 'width:100%' ]); !!}
            </div>
        </div>
    </td>
    <td>
        <div class="form-group">

            {!! Form::select('journal['.$row_count.'][cost_center]', $cost_center,$row->cost_center, ['class' => 'form-control select2', 'style' => 'width:100%' ]); !!}
        </div>
    </td>
    <td>
        <div class="form-group">

            <div class="input-group">
                {!! Form::text('journal['.$row_count.'][percent]',  0, ['class' => 'form-control input_number mousetrap']); !!}
                <span class="input-group-addon">
                              <i class="fa fa-percentage"></i>
                          </span>
            </div>

        </div>
    </td>
    <td>
        <?php
               $amount_debit=0;
               $amount_credit=0;
               if($row->type=='debit')
                   $amount_debit=$row->amount;
               else
                   $amount_credit=$row->amount;

            ?>
        <input type="text" name="journal[{{$row_count}}][amount_debit]" class="form-control amount_debit input_number mousetrap" value="{{@number_format($amount_debit,2,'.','')}}">
    </td>
    <td>
        <input type="text" name="journal[{{$row_count}}][amount_credit]" class="form-control amount_credit input_number mousetrap" value="{{@number_format($amount_credit,2,'.','')}}">
    </td>

    <td class="text-center " style="padding-top: 10px;" >
        <i class="fa fa-times  journal_remove_row cursor-pointer btn btn-danger" aria-hidden="true"></i>
    </td>
</tr>