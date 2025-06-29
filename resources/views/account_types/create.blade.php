<div class="modal-dialog" role="document">
  	<div class="modal-content">

    {!! Form::open(['url' => action('AccountTypeController@store'), 'method' => 'post', 'id' => 'account_type_form' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.add_account_type' )</h4>
    </div>

    <div class="modal-body">

       <div class="row">

           <div class="col-md-6">
               <div class="form-group">
                   {!! Form::label('parent_account_type_id', __( 'lang_v1.parent_account_type' ) . ':') !!}
                   {!! Form::select('parent_account_type_id', $account_types->pluck('name', 'id'), null, ['class' => 'form-control', 'placeholder' => __( 'messages.please_select' )]); !!}
               </div>
           </div>

           <div class="col-md-6">
               <div class="form-group">
                   {!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
                   {!! Form::text('name', null, ['class' => 'form-control', 'required']); !!}
               </div>
           </div>

           <div class="col-md-6">
               <div class="form-group">
                   {!! Form::label('account_number', __( 'account.account_number' ) .":*") !!}
                   {!! Form::text('account_number', null, ['class' => 'form-control' ]); !!}
               </div>
           </div>


           <div class="col-md-6">
               <div class="form-group">
                   {!! Form::label('account_nature', __( 'account.account_nature' ) .":*") !!}
                   <select class="form-control select2" name="account_nature">
                       <option value="-1">@lang('account.debit')</option>
                       <option value="1">@lang('account.credit')</option>
                   </select>

               </div>
           </div>
            <div class="col-lg-12 mt-15">
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