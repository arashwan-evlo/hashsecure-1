@extends('layouts.app')
@section('title', __('account.cost_centers'))

@section('content')
@include('cost_center.style')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'account.cost_centers' )

    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'account.cost_centers' )])
        @can('brand.create')
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                        data-href="{{action('CostCenterController@create')}}"
                        data-container=".brands_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endslot
        @endcan
        @can('brand.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" >
                    <thead>
                        <tr>
                            <th colspan="10">مركز التكلفة </th>
                             <th>الوصف</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                    <tbody id="data_table">

                    </tbody>
                </table>
            </div>
        @endcan


    @endcomponent

    <div class="modal fade brands_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function (){

            getdata();


        });

        function getdata(){
            $.ajax({
                url: "/account/costcenter",
                type: 'GET',
                data: {
                    type: $('#product_list_filter_type').val(),

                },
                success: function (data) {
                    $('#data_table').html(data)

                }
            });
        }


        $(document).on('submit', 'form#add_form', function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();

            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                beforeSend: function(xhr) {
                    __disable_submit_button(form.find('button[type="submit"]'));
                },
                success: function(result) {
                    if (result.success == true) {
                        $('div.brands_modal').modal('hide');
                        toastr.success(result.msg);
                        getdata();

                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });

        $(document).on('click', '.btn-modal-delete', function(e) {
            e.preventDefault();
            swal({
                title:   'سوف يتم حذف مركز التكلفة ',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).attr('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        data:{
                            account_id:$('#selected_account').val()
                        },
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                getdata();
                                toastr.success(result.msg);

                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });


    </script>
@endsection
