<?php

    $status=['0'=>'الشحن غير متاح',

        '1'=>'الشحن متاح'
        ]
    ?>


<table class="table table-bordered table-responsive">
    <thead>
    <tr>
        <th style="width: 30px">#</th>
        <th style="width: 350px">الإسم</th>
        <th style="width: 100px">التكلفة</th>
        <th style="width: 100px">الحالة</th>

        <th style="width: 100px"> </th>
    </tr>

    </thead>



@foreach($governorates as $row)
    <tr>
        <td>
            {{$loop->iteration}}
        </td>
        <td>

               {{$row->name}}

        </td>
        <td>
           {{$row->price}}
        </td>
        <td>
            <span class="active_{{$row->status}}">
                 {{$status[$row->status]}}
            </span>

        </td>
        <td>
            <a data-href="{{action('\Modules\Sitemanager\Http\Controllers\SitemanagerController@governorate_edit',[$row->id])}}" class="btn btn-primary add_media"
               data-container=".div_modal">
                <i class="fa fa-edit"> تعديل</i>
            </a>

            <a data-href="{{action('\Modules\Sitemanager\Http\Controllers\SitemanagerController@governorate_delete',[$row->id])}}" class="btn btn-danger delete_image">
                <i class="fa fa-trash"> حذف</i>
            </a>


        </td>

    </tr>

@endforeach

</table>