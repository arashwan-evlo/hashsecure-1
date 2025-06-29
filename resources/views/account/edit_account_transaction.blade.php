<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('AccountController@updateAccountTransaction', ['id' => $account_transaction->id ]), 'method' => 'post', 'id' => 'edit_account_transaction_form' ]) !!}


    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@if($account_transaction->sub_type == 'opening_balance')@lang( 'lang_v1.edit_opening_balance' )  @elseif($account_transaction->sub_type == 'deposit') @lang( 'lang_v1.edit_deposit' ) @endif</h4>
    </div>

    <div class="modal-body">

        {!! Form::hidden('account_id', $account_transaction->account_id) !!}
            <div class="form-group">
                <strong>@lang('account.selected_account')</strong>: 
                {{$account_transaction->account->name}}
            </div>

            @if($account_transaction->sub_type == 'deposit')
            @php
              $label = !empty($account_transaction->type == 'debit') ? __( 'account.deposit_from' ) :  __('lang_v1.deposit_to');
            @endphp 

            @endif
            <div class="form-group">
                {!! Form::label('amount', __( 'sale.amount' ) .":*") !!}
                {!! Form::text('amount', @num_format($account_transaction->amount), ['class' => 'form-control input_number', 'required','placeholder' => __( 'sale.amount' ) ]); !!}
            </div>
            @if($account_transaction->sub_type == 'deposit')
            @php
              $label = !empty($account_transaction->type == 'debit') ? __('lang_v1.deposit_to') :  __( 'account.deposit_from' );
            @endphp 
            <div class="form-group hidden">
                {!! Form::label('from_account', $label .":") !!}
                {!! Form::select('from_account', $accounts, $account_transaction->transfer_transaction->account_id ?? null, ['class' => 'form-control', 'placeholder' => __('messages.please_select') ]); !!}
            </div>
            @endif

            <div class="form-group">
                {!! Form::label('operation_date', __( 'messages.date' ) .":*") !!}
                <div class="input-group date">
                  {!! Form::text('operation_date', @format_datetime($account_transaction->operation_date), ['class' => 'form-control', 'required','placeholder' => __( 'messages.date' ), 'id' => 'od_datetimepicker' ]); !!}
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
            </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
  $(document).ready( function(){
    $('#od_datetimepicker').datetimepicker({
      format: moment_date_format + ' ' + moment_time_format
    });
  });
</script>