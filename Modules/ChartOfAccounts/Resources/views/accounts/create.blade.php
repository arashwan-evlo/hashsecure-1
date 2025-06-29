<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@saveacount'), 'method' => 'post','id' => 'add_chart_account' ]) !!}
        <input type="hidden" value="{{$account->id}}" class="form-control" name="account_id" id="account_id">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>

            @if(!empty($account->id))
                <h4 class="modal-title">@lang( 'chartofaccounts::lang.edit_account' ) : <span style="color: #6F1212ED;">{{$account->account_code}} - {{$account->name}}</span></h4>
            @else
                <h4 class="modal-title">@lang( 'chartofaccounts::lang.add_account' )</h4>
            @endif

        </div>

        <div class="modal-body">

            <div class="row">

                <?php
                $disabled='';
               if($account->parent_id==0 && $account->id){
                   $disabled='disabled';
                   $account->parent_id=$account->id;
               }

               ?>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('parent_id', __('chartofaccounts::lang.themain_account') ) !!}
                        {!! Form::select('parent_id', $accounts,$account->parent_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'parent_id',$disabled ]); !!}

                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', __( 'chartofaccounts::lang.account_name' ) . ':*') !!}
                        {!! Form::text('name', $account->name, ['class' => 'form-control', 'required' ]); !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('account_code', __( 'chartofaccounts::lang.account_code' ) . ':') !!}
                        <input type="text" value="{{$account->account_code}}" name="account_code" id="account_code"
                               class="form-control" style="max-width:200px">

                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('account_nature', __( 'chartofaccounts::lang.journal_cat' ) ) !!}
                        <select name="account_nature" id="account_nature " class="form-control">
                            <option value="-1" @if($account->account_nature==-1) selected @endif>@lang('chartofaccounts::lang.journal_debt')</option>
                            <option value="1" @if($account->account_nature==1) selected @endif>@lang('chartofaccounts::lang.journal_cridet')</option>
                        </select>
                    </div>

                </div>
                <div class="col-md-6">
                                   <div class="form-group">
                                       {!! Form::label('account_type_id', __( 'chartofaccounts::lang.account_type' ) ) !!}
                                       {!! Form::select('account_type_id', $account_type, $account->account_type_id, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'account_type_id','require',$disabled ]); !!}

                                   </div>
                               </div>

                    <div class="col-lg-12 mt-15">
                        {!! Form::label('notes','ملاحظات: ') !!}
                        <input type="text" name="notes" value="" id="notes" class="form-control">

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