
@foreach($query as $route)

<tr>
    <td>{{$route->operation}}</td>
    <td>{{$route->description}}</td>
    <td>{{$route->name}}</td>
    <td>
        <a data-href="{{action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@routing_edit',($route->id))}}" class="btn btn-primary btn-sm edit-route" data-container=".edit_modal"><i class="fa fa-edit"></i> تعديل</a>
    </td>
</tr>
@endforeach