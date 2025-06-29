<div class="modal-dialog" role="document">
    <style>
        .flex-container {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
    <div class="modal-content" >
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'sitemanager::lang.add_media' )</h4>
        </div>
             <form action="{{action('\Modules\Sitemanager\Http\Controllers\SitemanagerController@media_sotre')}}" method="POST" id="media_sotre" accept-charset="UTF-8" enctype="multipart/form-data">
                @csrf
                 <input type="hidden" name="id" value="{{$data->id}}">
                 <div class="row" style="margin: 5px;min-width: 150px">
                     <div class="col-md-12">
                         <div class="form-group " >
                             <label for="formGroupExampleInput"> عنوان الصورة *:</label>
                             <input type="text" class="form-control " name="title"  value="{{$data->title}}" required='required' >
                             </div>
                     </div>

                     @if(!empty($data->image_url))
                     <div class="col-md-6">
                         <img src="{{asset('/uploads/media/'.$data->image_url)}}" style="width: 200px;height: 200px">
                     </div>
                     @endif



                     <div class="col-md-6">
                         <div class="form-group">
                             {!! Form::label('image', __('sitemanager::lang.select_media') . ':') !!}
                             {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*']); !!}
                             <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br> @lang('lang_v1.aspect_ratio_should_be_1_1')</p></small>
                         </div>
                     </div>

                  <div class="clearfix"></div>
                         <div class="col-sm-4">
                             <label for="formGroupExampleInput2"> الحالة</label>
                             <select class="form-control" name="status">
                                 <option value="1">مفعل </option>
                                 <option value="0">غير مفعل</option>
                             </select>
                         </div>
                     <div class="col-md-4">
                         <div class="form-group " >
                             <label for="order"> الترتيب *:</label>
                             <input type="text" class="form-control " name="order"  value="{{$data->order}}" required='required' >
                         </div>
                     </div>

                 </div>


                <div class="modal-footer">
                     <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                     <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                 </div>


            </form>
    </div>
</div>
<script>
    $('.date-picker').datepicker({
        autoclose: true,
    /*    endDate: 'today',*/
        format:'yyyy-m-d',
    });
</script>