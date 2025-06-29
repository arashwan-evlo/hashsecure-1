<div class="modal-dialog modal-md" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('BusinessLocationController@product_location_store'), 'method' => 'post', 'id' => 'business_location_add_group' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
     @if(!empty($data->id))
      <h4 class="modal-title">تعديل رصيد مخزون</h4>
      @else
        <h4 class="modal-title">إضافة رصيد</h4>
      @endif
    </div>

    <?php
      $disable="";
      if(!empty($data->id)){
          $disable="disabled";
      }

      ?>

    <div class="modal-body">
      <input type="hidden" name="id" value="{{$data->id??0}}">
      <div class="row">

        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('variation_id','المنتج :') !!}
            {!! Form::select('variation_id', $products,$data->variation_id??0, ['class' => 'form-control select2', 'required',$disable ]); !!}
          </div>
        </div>





        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('location_group_id','مكان التخزين :') !!}
            {!! Form::select('location_group_id', $location_groups,$data->group_id??0, ['class' => 'form-control select2', 'required',$disable ]); !!}
          </div>
        </div>

        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('quantity','الرصيد :*') !!}
            {!! Form::text('quantity', $data->quantity??0, ['class' => 'form-control ', 'required' ]); !!}
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
