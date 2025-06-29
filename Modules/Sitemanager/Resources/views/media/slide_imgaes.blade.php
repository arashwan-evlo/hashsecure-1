
<table class="table table-bordered table-responsive">
    <thead>
    <tr>
        <th style="width: 30px">#</th>
        <th style="width: 50px">الصورة</th>
        <th style="width: 300px">العنوان</th>
        <th style="width: 100px"> </th>
    </tr>

    </thead>



@foreach($images as $imag)
    <tr>
        <td>
            {{$imag->order}}
        </td>
        <td>
            <a href="{{asset('/uploads/media/'.$imag->image_url)}}" >
            <img src="{{asset('/uploads/media/'.$imag->image_url)}}" style="width: 50px;height: 50px">
            </a>
        </td>
        <td>
            {{$imag->title}}
        </td>
        <td>
            <a data-href="{{action('\Modules\Sitemanager\Http\Controllers\SitemanagerController@media_edit',[$imag->id])}}" class="btn btn-primary add_media"
               data-container=".div_modal">
                <i class="fa fa-edit"> تعديل</i>
            </a>

            <a data-href="{{action('\Modules\Sitemanager\Http\Controllers\SitemanagerController@media_delete',[$imag->id])}}" class="btn btn-danger delete_image">
                <i class="fa fa-trash"> حذف</i>
            </a>


        </td>

    </tr>

@endforeach

</table>>