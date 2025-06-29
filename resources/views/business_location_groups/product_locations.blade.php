@extends('layouts.app')
@section('title','أماكن التخزين')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>إدارة المخزون     </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary', 'title' =>'إدارة المخزون'])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal"
                            data-href="{{action('BusinessLocationController@product_location_add')}}"
                            data-container=".location_add_modal">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>


            @endslot
            <div class="table-responsive">
                <table class="table table-bordered table-hover " id="business_location_group">
                    <thead>
                    <tr>
                        <th>الموقع (المخزن)</th>
                        <th> مكان التخزين</th>
                        <th> المنتج</th>
                        <th>الرصيد</th>
                        <th style="width: 123px">@lang( 'messages.action' )</th>
                    </tr>
                    </thead>
                    <tbody id="data_table">

                    </tbody>
                </table>
            </div>
        @endcomponent

        <div class="modal fade location_add_modal" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>
        <div class="modal fade location_edit_modal" tabindex="-1" role="dialog"
             aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->

@endsection

@section('javascript')
    <script type="text/javascript">

        $(document).ready(function (){
            getdata();
        });


        function getdata(){
            $.ajax({
                method:'get',
                url: '/product_locations',
                success: function(result) {
                    if (result.success === true) {
                        $('#data_table').html(result.html);
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        }


        $(document).on('submit','#business_location_add_group',function (e){

            e.preventDefault();
            var form = $(this);
            var data = $(form).serialize();
            $.ajax({
                method: $(form).attr('method'),
                url: $(form).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success === true) {
                        toastr.success(result.msg);
                        $('.location_add_modal').modal('hide');
                        getdata();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });

        });


        $(document).on('click', 'button.delete_button', function() {
            swal({
                title: LANG.sure,
                text: 'سوف يتم حذف الرصيد دون الـتأثير علي المخزون',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    console.log(href);
                    $.ajax({
                        method: 'POST',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success === true) {
                                toastr.success(result.msg);
                                getdata();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });

        $('.location_add_modal').on('shown.bs.modal', function() {
            $('.location_add_modal')
                .find('.select2')
                .each(function() {
                    __select2($(this));
                });
        });



    </script>

@endsection
