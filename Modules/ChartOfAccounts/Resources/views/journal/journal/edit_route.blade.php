<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@routing_update'), 'method' => 'POST', 'id' => 'add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

           @if(!empty($data->id))
            <h4 class="modal-title">تعديل بيانات التوجية : {{$data->operation}}</h4>
            @else
                <h4 class="modal-title">إضافة توجية</h4>
            @endif
        </div>

        <input type="hidden" value="{{$data->id}}" name="id">
        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('operation', 'العملية:*') !!}
                {!! Form::text('operation', $data->operation, ['class' => 'form-control', 'required', 'placeholder' => __( 'brand.brand_name' )]); !!}
            </div>

                <div class="form-group">
                    {!! Form::label('account_id', 'الحساب : ' ) !!}
                    {!! Form::select('account_id', $accounts,$data->account_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sub_account' ]); !!}
                </div>

            <div class="form-group">
                {!! Form::label('description','وصف العملية : ') !!}
                {!! Form::text('description', $data->description, ['class' => 'form-control', 'required' ]); !!}
            </div>



        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->