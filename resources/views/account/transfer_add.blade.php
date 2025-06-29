<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('AccountController@transfer_post'), 'method' => 'post', 'id' => 'fund_transfer_form', 'files' => true ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
           @if(!empty($data['account_from']))
             <h4 class="modal-title">تعديل عملية تحويل</h4>
               @else
             <h4 class="modal-title">@lang( 'account.fund_transfer' )</h4>
               @endif
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('from_account',"التحويل من حساب (سحب):*") !!}
                        {!! Form::select('from_account', $accounts, $data['account_from'], ['class' => 'form-control', 'required' ]); !!}
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group" style="max-width: 100px;">
                        {!! Form::label('amount_from', __( 'sale.amount' ) .":*") !!}
                        {!! Form::text('amount_from',number_format($data['amount_from'],2,'.',''), ['class' => 'form-control input_number', 'required','placeholder' => __( 'sale.amount' ) ]); !!}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('to_account', "إلي حساب (إيداع):*") !!}
                        {!! Form::select('to_account', $accounts, $data['account_to'], ['class' => 'form-control', 'required' ]); !!}
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 hidden">
                    <div class="form-group" style="max-width: 100px;">
                        {!! Form::label('amount_to', __( 'sale.amount' ) .":*") !!}
                        {!! Form::text('amount_to',number_format($data['amount_to'],2,'.',''), ['class' => 'form-control input_number', 'required','placeholder' => __( 'sale.amount' ) ]); !!}
                    </div>
                </div>


            </div>

            <div class="form-group">
                {!! Form::label('operation_date', __( 'messages.date' ) .":*") !!}
                <div class="input-group date" id='od_datetimepicker'>
                    {!! Form::text('operation_date',$data['transaction_date'], ['class' => 'form-control date-picker', 'required', 'placeholder' => __( 'messages.date' ) ]); !!}
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('note', __( 'brand.note' )) !!}
                {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]); !!}
            </div>

            <div class="form-group">
                {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                {!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                <p class="help-block">
                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                    @includeIf('components.document_help_text')
                </p>
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