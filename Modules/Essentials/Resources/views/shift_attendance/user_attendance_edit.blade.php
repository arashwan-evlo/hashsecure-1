<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\AttendanceController@user_attendance_update'), 'method' => 'post', 'id' => 'attendance_form' ]) !!}


    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
     @if(!empty($shift->user))
      <h5 class="modal-title">@lang( 'essentials::lang.edit_attendance' ) : {{ $shift->user }}</h5>
        @else
          <h5 class="modal-title">@lang( 'essentials::lang.add_shift' ) </h5>
        @endif
    </div>

    <div class="modal-body">
      <div class="row">
  <input type="hidden" name="id" value="{{$shift->id}}">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('essentials_shift_id', __('essentials::lang.select_shift') . ':') !!}
            {!! Form::select('essentials_shift_id', $shifts, $shift->shift_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('essentials::lang.select_shift')]); !!}
          </div>
        </div>
          <div class="col-md-6 @if(!empty($shift->user_id)) hidden @endif">
              <div class="form-group">
                  {!! Form::label('user_id', __('essentials::lang.employee') . ':') !!}
                  {!! Form::select('user_id', $employees, $shift->user_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
              </div>
          </div>
<div class="clearfix"></div>

        <div class="col-md-6">
          {!! Form::label('start_date', __( 'essentials::lang.start_date' ) . ':') !!}
          <div class="input-group date">
            {!! Form::text('start_date',!empty($shift->shift_date)? Carbon::parse($shift->shift_date)->format('d-m-y'):$currentDateTime , ['class' => 'form-control date_picker', 'placeholder' => __( 'business.start_date' ), 'readonly']); !!}
            <span class="input-group-addon"><i class="fas fa-clock"></i></span>
          </div>
        </div>



    	</div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->