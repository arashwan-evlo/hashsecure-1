<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('CostCenterController@store'), 'method' => 'post', 'id' => 'add_form' ]) !!}


      <input type="hidden" value="{{$center->id}}" name="id">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
     @if(!empty($center->id))
            <h5 class="modal-title">تعديل مركز التكلفة : {{$center->name}}</h5>
        @else
            <h5 class="modal-title">@lang( 'account.cost_center_add' )</h5>
        @endif

    </div>

    <div class="modal-body">

      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('name', __( 'account.cost_center_name' ) . ':*') !!}
            {!! Form::text('name', $center->name, ['class' => 'form-control', 'required' ]); !!}
          </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('type', 'نوع مركز التلكفة: ') !!}
           <select name="type" id="type" class="form-control select2">
             <option value="0" @if($center->type==0) selected @endif>@lang('account.cost_center_main')</option>
             <option value="1" @if($center->type==1) selected @endif>@lang('account.cost_center_branch')</option>
           </select>
          </div>
        </div>

        <div class="col-md-6" id="div_parent_id">
          <div class="form-group">
            {!! Form::label('parent_id','مركز التكلفة الرئيسي') !!}
            {!! Form::select('parent_id', $parents,$center->parent_id, ['class' => 'form-control select2','placeholder'=>'', 'style' => 'width: 100%;']); !!}
          </div>
        </div>
     <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('code', __( 'account.cost_center_code' ) . ':*') !!}
            {!! Form::text('code', $center->code, ['class' => 'form-control', 'required' ]); !!}
          </div>
        </div>
      </div>


      <div class="form-group">
         {!! Form::label('description', __( 'brand.short_description' ) . ':') !!}
          {!! Form::text('description', $center->description, ['class' => 'form-control']); !!}
      </div>



    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->