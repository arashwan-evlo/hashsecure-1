@extends('layouts.app')
@section('title',__('chartofaccounts::lang.chart_view'))

@section('content')
    @include('chartofaccounts::layouts.style')
    <!-- Main content -->
    <section class="content">
        <section class="content">

            @component('components.widget', ['class' => 'box-primary', 'title' => __('chartofaccounts::lang.chart_view')])
                <input type="hidden" id="selected_account" value="" name="selected_account">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('view_type','طريقة العرض: ') !!}
                            {!! Form::select('view_type', ['tree'=>'شجرة','table'=>'جدول'],null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'view_type' ]); !!}

                        </div>
                    </div>
                 </div>

                <div class="dt-buttons btn-group">
                    <a class="btn btn-primary  btn-md  btn-modal-add"  href="#"
                       data-href="{{action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@addacount')}}"
                       data-container=".brands_modal">

                        <span><i class="fas fa-plus-circle fa-lg" aria-hidden="true"></i> @lang( 'messages.add' )</span>
                    </a>
                    <a class="btn btn-success  btn-md  btn-modal-edit"  href="#"
                       data-href="{{action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@addacount')}}"
                       data-container=".brands_modal">

                        <span><i class="fa fa-edit" aria-hidden="true"></i> @lang( 'messages.edit' )</span>
                    </a>

                    <a class="btn bg-navy btn-default   btn-md  btn-modal-view"  href="#"
                       data-href="{{action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@addacount')}}"
                       data-container=".brands_modal">
                        <span><i class="fa fa-eye" aria-hidden="true"></i> @lang( 'messages.view' )</span>
                    </a>

                    <a class="btn btn-danger  btn-md  btn-modal-delete"  href="#"
                       data-href="{{action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@deleteaccount')}}"
                       data-container=".brands_modal">

                        <span><i class="fa fa-trash" aria-hidden="true"></i> @lang( 'messages.delete' )</span>
                    </a>


                </div>

            @endcomponent

            <div>
                <h4>الحساب: <span id="selectedaccount"></span></h4>

            </div>

            <div class="box box-primary">
                <div class="tree-table" id="tree-table"></div>
            </div>

            <div class="modal fade brands_modal" tabindex="-1" role="dialog"
                 aria-labelledby="gridSystemModalLabel">
            </div>
        </section>
        @endsection

        @section('javascript')
            <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>
            @include('chartofaccounts::javascript')

            <script>
                $(document).ready(function () {
                    getaccounts();
                });


                function getaccounts(){
                    var view_type=$("#view_type").val();
                    var loader=' <span class="account_loader">  <i class="fas fa-sync fa-spin fa-fw margin-bottom"></i></span>';
                    //$('#tree-table').html(loader);
                    $.ajax({
                        method: "GET",
                        url: '/chartofaccounts/chart_view',
                        dataType: "json",
                        data:{
                            view_type:view_type,
                        },
                        success: function (result) {
                            if (result.success === true) {
                                $('#tree-table').html(result.html);

                            } else {
                                toastr.error(result.msg);
                            }

                        }
                    });

                }


                $(document).on('change','#view_type',function(){
                    getaccounts();
                });

                $(document).on('submit', 'form#add_chart_account', function(e) {
                    e.preventDefault();
                    var data = $('form#add_chart_account').serialize();
                    var url = $('form#add_chart_account').attr('action');
                    var form = $(this);
                    $.ajax({
                        method: 'POST',
                        url: url,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success) {
                                getaccounts();
                                $('.brands_modal').modal('hide');
                                toastr.success(result.msg);

                            } else {
                                __enable_submit_button(form.find('button[type="submit"]'));
                                toastr.error(result.msg);
                            }
                        }
                    });
                });

                $(document).on('click','.account',function(){

                    $('.account').each(function(){
                        $(this).removeClass('selected');
                    });

                    var id=$(this).attr('account_id');
                    var account=$(this).text();
                    $(this).addClass('selected');
                    $('#selected_account').val(id);
                    $('#selectedaccount').text(account);


                });

                $(document).on('click', '.btn-modal-add', function(e) {
                    e.preventDefault();
                    var container = $(this).data('container');
                    $.ajax({
                        url: $(this).data('href'),
                        method:'get',
                        data:{
                            parent_id:$('#selected_account').val(),
                            account_id:0
                        },
                        datatype:'html',
                        success: function(result) {
                            $(container)
                                .html(result)
                                .modal('show');
                            $(document).find('.select2').each(function() {
                                $(this).select2();
                            });
                        },
                    });
                });

                $(document).on('click', '.btn-modal-edit', function(e) {
                    e.preventDefault();
                    var container = $(this).data('container');
                    $.ajax({
                        url: $(this).data('href'),
                        data:{
                            account_id:$('#selected_account').val()
                        },
                        datatype:'html',
                        success: function(result) {
                            $(container)
                                .html(result)
                                .modal('show');
                            $(document).find('.select2').each(function() {
                                $(this).select2();
                            });
                        },
                    });
                });


                $(document).on('change','#parent_id',function(){
                    $.ajax({
                        url:"/chartofaccounts/getaccountcode/"+$('#parent_id').val()+"/"+$('#account_id').val(),
                        method:'GET',

                        success: function(result) {
                            $('#account_code').val(result);
                        }

                    });

                });

                $(document).on('click', '.btn-modal-delete', function(e) {
                    e.preventDefault();
                    var account= $('#selectedaccount').text();
                    var container = $(this).data('container');
                    swal({
                        title:   'سوف يتم حذف الحساب: ' + account ,
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                    }).then(willDelete => {
                        if (willDelete) {
                            var href = $(this).data('href');
                            $.ajax({
                                method: 'GET',
                                url: href,
                                data:{
                                    account_id:$('#selected_account').val()
                                },
                                dataType: 'json',
                                success: function(result) {
                                    if (result.success) {
                                        getaccounts();
                                        toastr.success(result.msg);

                                    } else {
                                        toastr.error(result.msg);
                                    }
                                },
                            });
                        }
                    });
                });


                const button = document.querySelectorAll("details");
                for (var i=0; i < button.length; i++) {
                    button[i].setAttribute("open", "");
                }






            </script>
@endsection