@extends('layouts.app')
@section('title',__('chartofaccounts::lang.journal_routing'))

@section('content')
    <section class="content-header no-print">
        <h1>@lang('chartofaccounts::lang.journal_routing') </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">

       @component('components.widget', ['class' => 'box-primary', 'title' => __( 'chartofaccounts::lang.journal_routing')])

                    @slot('tool')
                        <div class="box-tools">
                            <a class="btn btn-block btn-primary edit-route" data-href="{{action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@routing_edit',("0"))}}"
                               data-container=".edit_modal">
                                <i class="fa fa-plus"></i> @lang('messages.add')</a>
                        </div>
                    @endslot


                    <table class="table table-bordered table-striped ajax_view" id="sell_table">
                        <thead>
                        <tr>

                            <th>العملية</th>
                            <th>الوصف</th>
                            <th>الحساب</th>
                            <th style="width: 100px">@lang('messages.action')</th>
                         </tr>
                        </thead>
                        <tbody id="journal_table"></tbody>
                    </table>

            @endcomponent
    </section>

    <div class="modal fade edit_modal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog"
         aria-labelledby="gridSystemModalLabel">
    </div>

    <!-- This will be printed -->
    <!-- <section class="invoice print_section" id="receipt_section">
    </section> -->

@stop

@section('javascript')
   {{-- @include('chartofaccounts::javascript')--}}

<script type="text/javascript">
        $(document).ready(function () {

            getaccount(1);


        });
        function getaccount(account_id) {

            $.ajax({
                url: '/journal/routing',
                type:'GET',
                data:{
                    id:account_id
                },
                success: function(result) {
                    $('#journal_table').html(result);
                    $(".select2").select2();
                },
            });

        }
        function addchartaccount() {
            $.ajax({
                url: '/chartofaccounts/addaccount',
                dataType: 'html',
                success: function(result) {
                    $('#modeldiv').html(result).modal('show');
                },
            });
        }

        function chart_view(){
            $.ajax({
                url: '/chartofaccounts/chart_view',
                success: function(result) {
                    $('#treeview').treeview({
                        data:result
                    })
                },
            });
        }

        $(document).on('click', '.edit-route', function(e) {
            e.preventDefault();
            var container = $(this).data('container');
            $.ajax({
                url: $(this).data('href'),
                dataType: 'html',
                success: function(result) {
                    $(container).html(result).modal('show');
                    $(".select2").select2();

                },
            });
        });








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
                        $('div.edit_modal').modal('hide');
                        toastr.success(result.msg);
                        getaccount(1);
                    } else {
                        toastr.error(result.msg);
                        __enable_submit_button(form.find('button[type="submit"]'));
                    }
                },
            });
        });

    </script>


@endsection
