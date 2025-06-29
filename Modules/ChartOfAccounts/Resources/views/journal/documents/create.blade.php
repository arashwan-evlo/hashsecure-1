<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\ChartOfAccounts\Http\Controllers\JournalDocumentsController@store'), 'method' => 'post', 'id' => 'store_journal_document', 'files' => true ]) !!}

            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'chartofaccounts::lang.add_document' )</h4>
        </div>

        <div class="modal-body">
            <input type="hidden" name="journal_id" value="{{$journal_id}}">
            <div class="form-group">
                {!! Form::label('name', __( 'chartofaccounts::lang.document_name' ) . ':*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'required' ]); !!}
            </div>


            <div class="form-group">
                {!! Form::label('document', __('purchase.attach_document') . ':') !!}
                {!! Form::file('document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                {{-- <p class="help-block">
                     @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                     @includeIf('components.document_help_text')
                 </p>--}}
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->