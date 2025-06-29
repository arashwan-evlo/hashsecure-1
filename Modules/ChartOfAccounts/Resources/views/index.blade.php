@extends('layouts.app')
@section('title','chartofaccounts')

@section('content')
<style>
    .node-treeview{
        cursor:pointer ;
    }
    .node-treeview:hover{
        background-color: #AE0E0E!important;
        color: white!important;
    }
    .active{
        background-color: #337ab7!important;;
        color: #FFF;
    }
</style>
    <section class="content-header">
    <h1>@lang('chartofaccounts::lang.chart_view' )   </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="mb-10">
        <button type="button" class="btn  btn-primary btn-modal" onclick="addchartaccount()">
            <i class="fa fa-plus"></i> @lang( 'chartofaccounts::lang.add_account' )</button>
        <div class="row">
            <div class="col-lg-3 mt-15">
                {!! Form::label('account_1', __( 'chartofaccounts::lang.main_account' ) ) !!}
                {!! Form::select('account_1', $accounts, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'account_1', 'placeholder' =>'أبحث']); !!}
             </div>
            <div class="col-lg-3 mt-15">
                {!! Form::label('account_2','الحساب الفرعي' ) !!}
                {!! Form::select('account_2', [], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'account_2', 'placeholder' =>'أبحث']); !!}
            </div>

            <div class="col-lg-3 mt-15">
                {!! Form::label('account_3','الحساب الفرعي' ) !!}
                {!! Form::select('account_3',[] , null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'account_3', 'placeholder' =>'أبحث']); !!}
            </div>
        </div>
    </div>



    <div class="box box-primary" style="padding-right: 10px" >

        <div class="row " >
              <div id="treeview"  class="col-lg-3 mt-15 ">

             </div>
<div class="col-lg-12">
    <div class="account-path" id="account-path"></div>
</div>
           {{-- <div class="col-lg-3 mt-5">
                   <ul class="list-group">
                    @foreach ($accounts as $account)
                        <li class="list-group-item node-treeview " data-nodeid="0" onclick="getaccount({{$account->id}})"  id="{{$account->id}}">
                            <span class="con expand-icon glyphicon glyphicon-plus-sign"></span>
                            <span class="hidden">#{{$account->code}}</span>
                            {{$account->name}}
                           --}}{{-- @if(count($account->childs))
                                @include('chartofaccounts::managechild',['childs' => $account->childs])
                            @endif--}}{{--
                         </li>
                       <div class="" id="ch_{{$account->id}}"></div>

                    @endforeach
                </ul>
            </div>--}}
             <div class="col-lg-9 mt-5 ">
                   <div id="acount-chiled"></div>
             </div>


        </div>
    </div>
   {{--Model div --}}
    <div class="modal fade modeldiv" tabindex="-1" role="dialog" id="modeldiv"
         aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@endsection

@section('javascript')
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>
 @include('chartofaccounts::javascript')

    <script>
     $(document).ready(function () {

    chart_view();


     });
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
     $('#modeldiv').on('shown.bs.modal', function() {
         $('#modeldiv')
             .find('.select2')
             .each(function() {
                 var $p = $(this).parent();
                 $(this).select2({ dropdownParent: $p });
             });
     });
     function getaccount(id) {
         var account_id=id;
          $.ajax({
             url: '/chartofaccounts/getaccount',
             type:'GET',
             data:{
                id:account_id
             },
             success: function(result) {
                 $('#acount-chiled').html(result.html);
                 //$('#ch_'+id).html(result.html);
               /* $('#ch_'+result.parent).html(result.html);//script*/
             },
         });

     }

     $(document).on('click','.node-treeview',function () {
         $('.node-treeview').each(function(i, obj) {
             $(this).removeClass('active');
         });
         $(this).addClass('active');
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
                     $('div.modeldiv').modal('hide');
                     chart_view();
                     toastr.success(result.msg);
                 } else {
                     toastr.error(result.msg);
                 }
             },
         });
     });

 </script>


@endsection
