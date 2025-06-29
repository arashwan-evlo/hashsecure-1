<div class="modal-dialog modal-md" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('BusinessLocationController@store_group'), 'method' => 'post', 'id' => 'business_location_add_group' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'business.add_business_location' )</h4>
    </div>

    <div class="modal-body">
      <input type="hidden" name="id" value="{{$data->id}}">
      <div class="row">

        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('business_location_id', __( 'invoice.name' ) . ':*') !!}
            {!! Form::select('business_location_id', $locations,$data->business_location_id, ['class' => 'form-control', 'required' ]); !!}
          </div>
        </div>


        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('name', __( 'invoice.name' ) . ':*') !!}
              {!! Form::text('name', $data->name, ['class' => 'form-control', 'required' ]); !!}
          </div>
        </div>

        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('notes',' ملاحظات :') !!}
            {!! Form::text('notes', $data->notes, ['class' => 'form-control' ]); !!}
          </div>
        </div>

        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('status', __( 'invoice.name' ) . ':*') !!}
            {!! Form::select('status',[0=>'غير نشط',1=>'نشط'], $data->status, ['class' => 'form-control', 'required' ]); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
