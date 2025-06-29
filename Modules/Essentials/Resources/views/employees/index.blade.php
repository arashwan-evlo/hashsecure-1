@extends('layouts.app')
@section('title', __('essentials::lang.employees'))

@section('content')
@include('essentials::layouts.nav_hrm')
<section class="content-header">
    <h1>@lang('essentials::lang.employees')
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters'), 'class' => 'box-solid'])
            @if($is_admin)
            {{--<div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('user_id_filter', __('essentials::lang.employee') . ':') !!}
                    {!! Form::select('user_id_filter', $employees, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>--}}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('department_id', __('essentials::lang.department') . ':') !!}
                    {!! Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('designation_id', __('essentials::lang.designation') . ':') !!}
                    {!! Form::select('designation_id', $designations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('status', __( 'essentials::lang.Status' ) . ':') !!}
                            {!! Form::select('status', $employee_status,null, ['class' => 'form-control select2','id'=>'status']); !!}
                        </div>
                    </div>
            @endif
         {{--   <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('month_year_filter', __( 'essentials::lang.month_year' ) . ':') !!}
                    <div class="input-group">
                        {!! Form::text('month_year_filter', null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.month_year' ) ]); !!}
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>--}}
        @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid', 'title' => __( 'essentials::lang.employees' )])
                @if($is_admin)
                    @slot('tool')
                        <div class="box-tools">
                            <a href="#"  class="employee_edit btn btn-primary"   data-href="{{action('\Modules\Essentials\Http\Controllers\DashboardController@employee_edit')}}">
                                <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>

                        </div>
                    @endslot
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="payrolls_table">
                        <thead>
                            <tr>
                                <th>@lang( 'essentials::lang.employee' )</th>
                                <th>@lang( 'lang_v1.mobile_number' )</th>
                                <th>@lang( 'business.alternate_number' )</th>
                                <th>@lang( 'business.email' )</th>
                                <th>@lang( 'essentials::lang.Status')</th>
                                <th>@lang( 'essentials::lang.department' )</th>
                                <th>@lang( 'essentials::lang.designation' )</th>
                                <th>@lang( 'essentials::lang.salary' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
    @if($is_admin)
        @includeIf('essentials::payroll.payroll_modal')
    @endif

</section>
<!-- /.content -->
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog"
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_employee_modal" tabindex="-1" role="dialog"
    aria-labelledby="gridSystemModalLabel">
</div>

@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready( function(){

            payrolls_table = $('#payrolls_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{action('\Modules\Essentials\Http\Controllers\DashboardController@employee')}}",
                    data: function (d) {

                        if ($('#department_id').length) {
                            d.department_id = $('#department_id').val();
                        }
                        if ($('#designation_id').length) {
                            d.designation_id = $('#designation_id').val();
                        }
                        d.status=$('#status').val();
                    },
                },
                columnDefs: [
                    {
                        targets: 5,
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    { data: 'user', name: 'user' },
                    { data: 'contact_number', name: 'contact_number'},
                    { data: 'alt_number', name: 'alt_number'},
                    { data: 'email', name: 'email'},
                    { data: 'status', name: 'status'},
                    { data: 'department', name: 'dept.name' },
                    { data: 'designation', name: 'dsgn.name' },
                    { data: 'salary', name: 'salary' },
                    { data: 'action', name: 'action' },
                ]
            });

            $(document).on('change', '#department_id, #designation_id, #status', function() {
                payrolls_table.ajax.reload();
            });

            if ($('#add_payroll_step1').length) {
                $('#add_payroll_step1').validate();
                $('#employee_id').select2({
                    dropdownParent: $('#payroll_modal')
                });
            }



            $(document).on('click', '.delete-payroll', function(e) {
                e.preventDefault();
                swal({
                    title: LANG.sure,
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then(willDelete => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        var data = $(this).serialize();

                        $.ajax({
                            method: 'DELETE',
                            url: href,
                            dataType: 'json',
                            data: data,
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    payrolls_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                        });
                    }
                });
            });

            $(document).on('click', '.employee_edit', function(e) {
                e.preventDefault();
                var container = $('.edit_employee_modal');

                $.ajax({
                    method:'get',
                    url: $(this).data('href'),
                    dataType: 'html',
                     success: function(result) {
                        container.html(result).modal('show');
                      },
                });
            });

            $(document).on('submit', 'form#employee_update_form', function(e) {
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
                            $('div.edit_employee_modal').modal('hide');
                            toastr.success(result.msg);
                            payrolls_table.ajax.reload();
                        } else {
                            __enable_submit_button(form.find('button[type="submit"]'));
                            toastr.error(result.msg);
                        }
                    },
                });
            });

        });



        $(document).on('click','#allow_login',function(event){
            if($(this).is(':checked'))
                $('div.user_auth_fields').removeClass('hide');
            else
                $('div.user_auth_fields').addClass('hide');
        });

    </script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection
