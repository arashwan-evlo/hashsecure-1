<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('AccountController@store'), 'method' => 'post', 'id' => 'payment_account_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'account.add_account' )</h4>
    </div>

    <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __( 'lang_v1.name' ) .":*") !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'required','placeholder' => __( 'lang_v1.name' ) ]); !!}
            </div>

            <div class="form-group">
                {!! Form::label('account_code', __( 'account.account_code' ) .":*") !!}
                {!! Form::text('account_code', null, ['class' => 'form-control', 'required' ]); !!}
            </div>

            <div class="form-group">
                <div class="form-group">
                    {!! Form::label('parent_id', __('chartofaccounts::lang.themain_account') ) !!}
                    {!! Form::select('parent_id', $accounts,$account->parent_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'parent_id']); !!}

                </div>
            </div>

            <div class="form-group">
                {!! Form::label('opening_balance', __( 'account.opening_balance' ) .":") !!}
                {!! Form::text('opening_balance', 0, ['class' => 'form-control input_number','placeholder' => __( 'account.opening_balance' ) ]); !!}
            </div>

        <div class="form-group">
            {!! Form::label('account_number', __( 'account.account_number' ) .":") !!}
            {!! Form::text('account_number', $account->account_number, ['class' => 'form-control' ]); !!}
        </div>
            <div class="form-group">
                {!! Form::label('note', __( 'brand.note' )) !!}
                {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]); !!}
            </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->