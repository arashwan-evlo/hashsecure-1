@extends('layouts.app')
@section('title',__('chartofaccounts::lang.cash_receipt'))

@section('content')
    @include('chartofaccounts::layouts.style')
    <!-- Main content -->
    <section class="content">
        <section class="content">

            @component('components.widget', ['class' => 'box-primary', 'title' => __('chartofaccounts::lang.cash_receipt')])
               <div class="row">
                   <div class="col-sm-6">
                       <div class="form-group">
                           {!! Form::label('transaction_date_range', __('report.date_range') . ':') !!}
                           <div class="input-group">
                               <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                               {!! Form::text('transaction_date_range', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]) !!}
                           </div>
                       </div>
                   </div>
               </div>


                <div class="dt-buttons btn-group">
                    <a class="btn btn-primary  btn-md  "
                    href="{{action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@cash_receipt_add')}}"
                       >

                        <span><i class="fas fa-plus-circle fa-lg" aria-hidden="true"></i> @lang( 'messages.add' ) @lang(('chartofaccounts::lang.cash_receipt')) </span>
                    </a>

                </div>

<div class="" style="margin-top: 20px">

    <div ></div>

    <table class="table table-bordered table-hover" >
        <thead>
        <tr>
            <th>NO</th>
            <th>#</th>
            <th>تاريخ السند</th>
            <th>ملاحظات</th>
            <th>الحساب</th>
            <th>دائن</th>
            <th>مدين</th>




        </tr>
        </thead>
        <tbody id="data_table">

        </tbody>

    </table>
</div>


            @endcomponent



            <div class="modal fade brands_modal" tabindex="-1" role="dialog"
                 aria-labelledby="gridSystemModalLabel">
            </div>
        </section>
        @endsection

@section('javascript')
             @include('chartofaccounts::javascript')

            <script>
                $(document).ready(function () {
                    getdata();
                    $('#transaction_date_range').daterangepicker(
                        dateRangeSettings,
                        function (start, end) {
                            $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                            getdata();

                        }
                    );



             function getdata() {
                 $.ajax({
                     url: '{{action("\Modules\ChartOfAccounts\Http\Controllers\JournalController@get_cash_receipt")}}',
                     method: "GET",
                      data: {

                     },
                     success: function (result) {
                         $('#data_table').html(result);
                     }


                 });
             }



                    $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
                        $('#transaction_date_range').val('');

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




                $(document).on('click', '.btn-modal-delete', function(e) {
                    e.preventDefault();
                     swal({
                        title:   'سوف يتم حذف القيد ',
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


                });





            </script>
@endsection