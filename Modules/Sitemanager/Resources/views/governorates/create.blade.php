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
           @if(empty($data->id))
            <h4 class="modal-title">إضافة محافظة شحن</h4>
            @else
                <h4 class="modal-title">تعديل بيانات شحن</h4>
             @endif
        </div>
             <form action="{{action('\Modules\Sitemanager\Http\Controllers\SitemanagerController@governorate_store')}}" method="POST" id="media_sotre" accept-charset="UTF-8" >
                @csrf
                 <input type="hidden" name="id" value="{{$data->id}}">
                 <div class="row" style="margin: 5px;min-width: 150px">
                     <div class="col-md-12">
                         <div class="form-group " >
                             <label for="name"> الإسم *:</label>
                             <input type="text" class="form-control " name="name"  value="{{$data->name}}" required='required' >
                             </div>
                     </div>

                     <div class="col-md-4">
                         <div class="form-group " >
                             <label for="price"> التكلفة *:</label>
                             <input type="text" class="form-control " name="price"  value="{{$data->price}}" required='required' >
                         </div>
                     </div>
                    <div class="col-md-4">
                         <div class="form-group " >
                             <label for="time_val"> وقت التوصيل *:</label>
                             <input type="text" class="form-control " name="time_val"  value="{{$data->time_val}}" required='required' >
                         </div>
                     </div>

                         <div class="col-sm-4">
                             <label for="formGroupExampleInput2"> الحالة</label>
                             <select class="form-control" name="status">
                                 <option value="1" @if($data->status==1) selected @endif>الشحن متاح </option>
                                 <option value="0" @if($data->status==0) selected @endif>الشحن غير متاح</option>
                             </select>
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