<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\DashboardController@employee_update'), 'method' => 'post', 'id' => 'employee_update_form' ]) !!}

        <input type="hidden" name="id" value="{{$employees->id}}">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
           @if(!empty($employees->id))
            <h5 class="modal-title">تعديل بيانات موظف: {{$employees->user}}</h5>
            @else
                <h5 class="modal-title">إضافة موظف </h5>
            @endif
        </div>

        <div class="modal-body">
            <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('surname', __( 'business.prefix' ) . ':') !!}
                    {!! Form::text('surname', $employees->surname, ['class' => 'form-control' ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
                    {!! Form::text('first_name', $employees->first_name, ['class' => 'form-control', 'required' ]); !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
                    {!! Form::text('last_name', $employees->last_name, ['class' => 'form-control' ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-8">
                <div class="form-group">
                    {!! Form::label('email', __( 'business.email' ) . ':') !!}
                    {!! Form::text('email', $employees->email, ['class' => 'form-control' ]); !!}
                </div>
            </div>

                <div class="clearfix"></div>
                <div class="form-group col-md-6">
                    {!! Form::label('contact_number', __( 'lang_v1.mobile_number' ) . ':') !!}
                    {!! Form::text('contact_number', !empty($employees->contact_number) ? $employees->contact_number : null, ['class' => 'form-control' ]); !!}
                </div>
                <div class="form-group col-md-6">
                    {!! Form::label('alt_number', __( 'business.alternate_number' ) . ':') !!}
                    {!! Form::text('alt_number', !empty($employees->alt_number) ? $employees->alt_number : null, ['class' => 'form-control' ]); !!}
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('essentials_designation_id','التعيين :') !!}
                        {!! Form::select('essentials_designation_id', $designations,$employees->essentials_designation_id, ['class' => 'form-control select2']); !!}
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('essentials_department_id',' القسم :') !!}
                        {!! Form::select('essentials_department_id', $departments,$employees->essentials_department_id, ['class' => 'form-control select2']); !!}
                    </div>
                </div>




                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('salary', 'الراتب الأساسي:') !!}
                        {!! Form::text('salary', $employees->salary, ['class' => 'form-control input_number ','id'=>'salary' ]); !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('status', __( 'essentials::lang.Status' ) . ':') !!}
                        {!! Form::select('status', $employee_status,$employees->status, ['class' => 'form-control select2']); !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('allow_login', 1, $employees->allow_login,
                                [ 'class' => 'input-icheck', 'id' => 'allow_login']); !!} {{ __( 'lang_v1.allow_login' ) }}
                            </label>
                        </div>
                    </div>
                </div>

          </div>


           <div class="row user_auth_fields @if($employees->allow_login==0) hide @endif">
                <div class="col-md-8">
                    <div class="form-group">
                        {!! Form::label('username', __( 'business.username' ) . ':') !!}
                        <div class="input-group">
                            {!! Form::text('username', $employees->username, ['class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
                         </div>
                        <p class="help-block" id="show_username"></p>
                      </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('password', __( 'business.password' ) . ':') !!}
                        {!! Form::password('password', ['class' => 'form-control' ]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('confirm_password', __( 'business.confirm_password' ) . ':') !!}
                        {!! Form::password('confirm_password', ['class' => 'form-control',  ]); !!}
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