@extends('layouts.app')
@section('title', __('essentials::lang.attendance'))

@section('content')
    <style>
        .attendance{
            border: 1px solid #8F4747ED;
            border-radius: 10px;
            background-color: brown;
            color: #fff;
            text-align: center;
            margin: auto;
            font-size: 14px;
            padding: 1px 13px;
        }
        .not_attendance{
            border: 1px solid rgba(13, 78, 22, 0.93);
            border-radius: 10px;
            background-color: #098312;
            color: #fff;
            text-align: center;
            margin: auto;
            font-size: 14px;
            padding: 1px 13px;
        }
    </style>
@include('essentials::layouts.nav_hrm')
<section class="content-header">
    <h1>@lang('essentials::lang.attendance_list')
    </h1>
</section>
<!-- Main content -->
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
<div class="row">

        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('employee_id', __('essentials::lang.employee') . ':') !!}
                {!! Form::select('employee_id', $employees, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('status_id', 'الحالة:') !!}
            {!! Form::select('status_id', [''=>'الكل','0'=>'لم يتم التوقيع','1'=>'تم التوقيع'], null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
        </div>
    </div>
</div>
        <a href="{{action('\Modules\Essentials\Http\Controllers\AttendanceController@shift_attendance') }}"  class=" btn  btn-primary "><i class="fas fa-plus" aria-hidden="true"></i> @lang("messages.add")</a>
    @endcomponent
    <div class="row">
        <div class="col-md-12">
                    @if($is_admin)
                        <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" id="user_shift_table">
                                    <thead>
                                        <tr>
                                            <th>@lang( 'messages.action' )</th>
                                            <th>@lang( 'lang_v1.name' )</th>
                                            <th>@lang( 'essentials::lang.shift' )</th>
                                            <th>من يوم</th>
                                            <th>@lang( 'restaurant.start_time' )</th>
                                            <th>@lang( 'restaurant.end_time' )</th>
                                             <th>وقت الحضور</th>
                                             <th>وقت الإنصراف</th>
                                             <th>الحالة</th>

                                        </tr>
                                    </thead>
                                </table>
                            </div>

                    @endif

                </div>
    </div>

</section>
<!-- /.content -->
<div class="modal fade" id="attendance_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade" id="edit_attendance_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade" id="user_shift_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>

<div class="modal fade" id="edit_shift_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>

<div class="modal fade" id="shift_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    @include('essentials::attendance.shift_modal')
</div>

@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                }
            );
            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#date_range').val('');
                shift_table.ajax.reload();
            });


            $(document).on('change', '#employee_id, #date_range, #status_id', function() {
                shift_table.ajax.reload();
            });

            $(document).on('submit', 'form#attendance_form', function(e) {
                e.preventDefault();
                    $(this).find('button[type="submit"]').attr('disabled', true);
                    var form=$(this);
                    var data = $(this).serialize();
                    $.ajax({
                        method: $(this).attr('method'),
                        url: $(this).attr('action'),
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                $('div#edit_shift_modal').modal('hide');
                                toastr.success(result.msg);
                                shift_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                                $(this).find('button[type="submit"]').attr('disabled', false);
                                __enable_submit_button(form.find('button[type="submit"]'));
                            }
                        },
                    });

            });





            shift_table = $('#user_shift_table').DataTable({
                processing: true,
                serverSide: true,

                ajax: {
                    "url": "{{action('\Modules\Essentials\Http\Controllers\AttendanceController@shift_attendance_list')}}",
                    "data": function(d) {
                        if ($('#employee_id').length) {
                            d.employee_id = $('#employee_id').val();
                        }
                        if($('#date_range').val()) {
                            var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                            d.status=$('#status_id').val();
                        }
                    }
                },
                columnDefs: [
                    {
                        targets: 4,
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    { data: 'action', name: 'action' },
                    { data: 'user', name: 'users.first_name' },
                    { data: 'name', name: 'essentials_shifts.name' },
                    { data: 'shift_date', name: 'essentials_attendances.shift_date'},
                    { data: 'start_time', name: 'essentials_shifts.start_time'},
                    { data: 'end_time', name: 'essentials_shifts.end_time' },
                    { data: 'clock_in_time', name: 'essentials_attendances.clock_in_time' },
                    { data: 'clock_out_time', name: 'essentials_attendances.clock_out_time' },
                    { data: 'attendance_status', searchable: false, orderable: false },



                ],
            });

            $('#edit_shift_modal').on('shown.bs.modal', function(e) {
                $('#edit_shift_modal').find('.date_picker').each( function(){
                    $(this).datetimepicker({
                        format: moment_date_format,
                        ignoreReadonly: true,
                    });
                });
            });



                $('#shift_modal .select2, #edit_shift_modal .select2').select2();

                if ($('select#shift_type').val() == 'fixed_shift') {
                    $('div.time_div').show();
                } else if ($('select#shift_type').val() == 'flexible_shift') {
                    $('div.time_div').hide();
                }

                $('select#shift_type').change(function() {
                    var shift_type = $(this).val();
                    if (shift_type == 'fixed_shift') {
                        $('div.time_div').fadeIn();
                    } else if (shift_type == 'flexible_shift') {
                        $('div.time_div').fadeOut();
                    }
                });
            });


            $('#user_shift_modal').on('shown.bs.modal', function(e) {
                $('#user_shift_modal').find('.date_picker').each( function(){
                    $(this).datetimepicker({
                        format: moment_date_format,
                        ignoreReadonly: true,
                    });
                });
            });

            @if($is_admin)

                $('#attendance_by_shift_date_filter').datetimepicker({
                    format: moment_date_format,
                    ignoreReadonly: true,
                });
                var attendanceDateRangeSettings = dateRangeSettings;
                attendanceDateRangeSettings.startDate = moment().subtract(6, 'days');
                attendanceDateRangeSettings.endDate = moment();

                $('#attendance_by_date_filter').daterangepicker(
                    dateRangeSettings,
                    function (start, end) {
                        $('#attendance_by_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    }
                );

            @endif






        $(document).on('click', '.delete-attendance', function() {
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: 'post',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                               shift_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });

        $('#edit_attendance_modal').on('hidden.bs.modal', function(e) {
            $('#edit_attendance_modal #clock_in_time').data("DateTimePicker").destroy();
            $('#edit_attendance_modal #clock_out_time').data("DateTimePicker").destroy();
        });

        $('#attendance_modal').on('shown.bs.modal', function(e) {
            $('#attendance_modal .select2').select2();
        });
        $('#edit_attendance_modal').on('shown.bs.modal', function(e) {
            $('#edit_attendance_modal .select2').select2();
            $('#edit_attendance_modal #clock_in_time, #edit_attendance_modal #clock_out_time').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });




        });

        function get_attendance_summary() {
            $('#user_attendance_summary').addClass('hide');
            var user_id = $('#employee_id').length ? $('#employee_id').val() : '';
            
            var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            $.ajax({
                url: '{{action("\Modules\Essentials\Http\Controllers\AttendanceController@getUserAttendanceSummary")}}?user_id=' + user_id + '&start_date=' + start + '&end_date=' + end ,
                dataType: 'html',
                success: function(response) {
                    $('#total_work_hours').html(response);
                    $('#user_attendance_summary').removeClass('hide');
                },
            });
        }

    //Set mindate for clockout time greater than clockin time
    $('#attendance_modal').on('dp.change', '#clock_in_time', function(){
        if ($('#clock_out_time').data("DateTimePicker")) {
            $('#clock_out_time').data("DateTimePicker").options({minDate: $(this).data("DateTimePicker").date()});
            $('#clock_out_time').data("DateTimePicker").clear();
        }
    });

    $(document).on('submit', 'form#add_shift_form', function(e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();

        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    if ($('div#edit_shift_modal').hasClass('in')) {
                        $('div#edit_shift_modal').modal("hide");
                    } else if ($('div#shift_modal').hasClass('in')) {
                        $('div#shift_modal').modal('hide');    
                    }
                    toastr.success(result.msg);
                    shift_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });

    $(document).on('submit', 'form#add_user_shift_form', function(e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();

        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    $('div#user_shift_modal').modal('hide');
                    toastr.success(result.msg);
                } else {
                    toastr.error(result.msg);
                }
                $('form#add_user_shift_form').find('button[type="submit"]').attr('disabled', false);
            },
        });
    });



    $(document).on('click', 'button.remove_attendance_row', function(e) {
        $(this).closest('tr').remove();
    });



</script>
@endsection
