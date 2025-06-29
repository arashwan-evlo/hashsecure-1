<table class="table table-striped table-bordered" id="account_types_table" style="width: 100%;">
    <thead>
    <tr>
        <th>@lang( 'lang_v1.name' )</th>
        <th>@lang( 'messages.action' )</th>
    </tr>
    </thead>
    <tbody>
    @foreach($account_types as $account_type)
        <tr class="account_type_{{$account_type->id}}">
            <th>{{$account_type->name}}</th>
            <td>
                <button type="button" class="btn btn-primary btn-modal btn-xs"
                        data-href="{{action('AccountTypeController@edit', $account_type->id)}}"
                        data-container="#account_type_modal">
                    <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>

                <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                    <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>

            </td>
        </tr>
        @foreach($account_type->sub_types as $sub_type)
            <tr>
                <td>&nbsp;&nbsp;-- {{$sub_type->name}}</td>
                <td>
                   <button type="button" class="btn btn-primary btn-modal btn-xs"
                            data-href="{{action('AccountTypeController@edit', $sub_type->id)}}"
                            data-container="#account_type_modal">
                        <i class="fa fa-edit"></i> @lang( 'messages.edit' )</button>

                    <button type="button" class="btn btn-danger btn-xs delete_account_type" >
                        <i class="fa fa-trash"></i> @lang( 'messages.delete' )</button>

                </td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>