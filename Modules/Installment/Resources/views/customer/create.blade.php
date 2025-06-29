<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Installment\Http\Controllers\CustomerController@createinstallment'), 'method' => 'post','id'=>'add_installment' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">إضافة تقسيط</h4>
        </div>
        <div class="modal-body">

            <input type="hidden" name="contact_id" value="{{$transaction->contact_id}}">
            <input type="hidden" name="transaction_id" value="{{$transaction->id}}">

            <div class="row">
                <div class="col-lg-5">
                    <div class="form-group">
                        {!! Form::label('contactname','إسم العميل :') !!}
                        <input type="text" name='contactname' id="contactname" value=" {{ $transaction->name}}" class="form-control decimal intallparameter" readonly  >
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('total_fat','إجمالي الفاتورة :') !!}
                        <input type="text" name='total_fat' id="total_fat" value=" {{@number_format($total)}}" class="form-control decimal intallparameter" readonly >
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('total_paid','قيمة مسددة :') !!}
                        <input type="text" name='total_paid' id="total_paid" value=" {{ @number_format($total_paid)}}" class="form-control decimal intallparameter"  readonly >
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('total_req','المستحق :') !!}
                        <input type="text" name='total_req' id="total_req" value=" {{ @number_format($total-$total_paid)}}" class="form-control decimal intallparameter" readonly >
                    </div>
                </div>
            </div>

<hr>

            <div class="row">
                <div class="col-lg-12 ">
                    <div class="form-group">
                        {!! Form::label('system_id',' نظام التقسيط:') !!}
                        {!! Form::select('system_id', $systems, null, ['class' => 'form-control select2','id'=>'system_id']); !!}
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('advanced',' دفعة مقدمة:') !!}
                        <input type="text" name='advanced' id="advanced" value="0.0" class="form-control decimal intallparameter" required autocomplete="off" >
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('installment_value',' مبلغ القسط:') !!}
                        <input type="text" name='installment_value' id="installment_value" value=" {{ @number_format($total-$total_paid)}}" class="form-control decimal intallparameter" required autocomplete="off" >
                    </div>
                </div>



            </div>
            <?php
                $readonly='readonly';
                if(auth()->user()->can('installment.system_edit'))
                   $readonly='';
            ?>


                <div class="row">

                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('number',' عدد الأقساط :*') !!}
                            {!! Form::text('number', 12, ['class' => 'form-control decimal intallparameter', 'required','id'=>'number','autocomplete'=>"off" ]); !!}
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('period',' معدل السداد:') !!}
                            {!! Form::text('period', 1, ['class' => 'form-control integr intallparameter', 'required','id'=>'period','autocomplete'=>"off" ]); !!}
                        </div>
                    </div>
                    <div class="col-lg-4">
                        {!! Form::label('type',' ') !!}
                        <select class="form-control" name="type" id="type" >
                            <option value="day" >@lang('installment::lang.day')</option>
                            <option value="month" selected>@lang('installment::lang.month')</option>
                            <option value="year">@lang('installment::lang.year')</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('benefit',' نسبة الفائدة %:') !!}
                            {!! Form::text('benefit', null, ['class' => 'form-control decimal intallparameter', 'required','id'=>'benefit' ,$readonly]); !!}
                            <span style="color: red" id="benefit-type">قيمة الفائدة عن كل سنة</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('installment','قيمة القسط :') !!}
                            {!! Form::text('installment', '00.00', ['class' => 'form-control decimal','id'=>'installment','autocomplete'=>"off" ]); !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('benefit_value','إجمالي الفائدة :') !!}
                            {!! Form::text('benefit_value', '00.00', ['class' => 'form-control decimal','id'=>'benefit_value','readonly' ]); !!}
                        </div>
                    </div>

                    <div class="col-lg-4 hidden">
                        {!! Form::label('benefit_type','نوع الفائدة :') !!}
                        <select class="form-control" name="benefit_type" id="benefit_type">
                            <option value="simple">@lang('installment::lang.simple')</option>
                            <option value="complex">@lang('installment::lang.complex')</option>
                        </select>
                    </div>
                </div>
                 <div class="row">
    <div class="col-lg-4">
        <div class="form-group">
            {!! Form::label('latfines','غرامة التأخير % :') !!}
            {!! Form::text('latfines', 0, ['class' => 'form-control decimal intallparameter', 'required','id'=>'latfines' ,$readonly]); !!}
        </div>
    </div>
    <div class="col-lg-4">
        {!! Form::label('latfinestype',' ') !!}
        <select class="form-control" name="latfinestype" id="latfinestype">
            <option value="day">@lang('installment::lang.day')</option>
            <option value="month">@lang('installment::lang.month')</option>
            <option value="year">@lang('installment::lang.year')</option>
        </select>
    </div>
</div>



                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('installmentdate','تاريخ أول قسط : ') !!}
                            <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                {!! Form::text('installmentdate',Carbon::now()->format('Y-m-d'), ['class' => 'form-control date-picker','required', 'readonly','id'=>'installmentdate' ]); !!}
                            </div>
                        </div>
                    </div>
                </div>



                <div class="row">
                     <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('total','إجمالي السداد :') !!}
                            {!! Form::text('total', '00.00', ['class' => 'form-control decimal','id'=>'total','readonly' ]); !!}
                        </div>
                    </div>



                </div>

            <div class="modal-footer">
             <button type="submit" id="submit" class="btn  btn-primary " > <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
             <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>






    </div><!-- /.modal-content -->
        {!! Form::close() !!}
</div><!-- /.modal-dialog -->
</div>


<script>
    $(document).ready(function () {
        $('#system_id').on('change',function () {
            var system_id=$('#system_id').val();
            $.ajax({
                method: 'GET',
                url: '/installment/getsystemdata',
                data:{
                    id:system_id
                },
                success: function(result) {
                    $('#system_name').val(result['name']);
                    $('#number').val(result['number']);
                    $('#period').val(result['period']);
                    $('#type').val(result['type']);
                    $('#benefit').val(result['benefit']);
                    $('#benefit_type').val(result['benefit_type']);


                    $('#latfines').val(result['latfines']);
                    $('#latfinestype').val(result['latfinestype']);

                    calcinstallment();
                }
            });
        });

        function calcinstallment(){
           var advanced=__read_number($('input#advanced'));
           var total_req=__read_number($('#total_req'));
            __write_number($('#installment_value'),total_req*1-advanced*1);
            var installment_value=__read_number($('#installment_value'));
            if(installment_value=='')
                return true;

            var number=__read_number($('#number'));
            if(number=='')
                return true;
            var period=__read_number($('#period'));
            if(period=='')
                return true;

            var type=$('#type').val();

            var benefit=$('#benefit').val();
            if(benefit=='')
                return true;

            var benefit_type=$('#benefit_type').val();

            var benefit_peryear=benefit/1;
            var benefit_permonth=benefit/12;
            var benefit_perday=benefit/365;


            var total_benefit=0;
            if(type=='year')
                total_benefit=period*benefit_peryear*number/100;
            if(type=='month')
                total_benefit=period*benefit_permonth*number/100;

            if(type=='day')
                total_benefit=period*benefit_perday*number/100;


            var benefit_value=installment_value*total_benefit;

            __write_number($('#benefit_value'),benefit_value);
            var installment=benefit_value/number+installment_value/number;

            __write_number($('#installment'),installment);

            var total=(installment*number).toFixed(2)
            __write_number($('#total'),total);


        }

        $('.intallparameter').on('keyup',function () {
            calcinstallment();
        });

        $('#installment').on('keyup',function () {
            var advanced=$('#advanced').val();
            var total_req=$('#installment_value').val();

            var  installment_value=  $('#installment').val();
            var number=$('#number').val();
            if(number=='' || number==0){
                toastr.error('عفوا برجاء تحديد عدد الأقساط');
                return true;
            }

            var total=number*installment_value;
            $('#total').val(total);

            var benefit_value=total-total_req;
            $('#benefit_value').val(benefit_value);

           var benefit=benefit_value*100/total_req;
           $('#benefit').val(benefit);



        })

        $('#type').on('change',function () {
            calcinstallment();
        });



        $('.date-picker').datepicker({
            autoclose: true,
            format:'yyyy-m-d',
        });

        $(document).on('submit', 'form#add_installment', function(e) {
            e.preventDefault();
            document.getElementById("submit").disabled = true;
            var form = $(this);
            __disable_submit_button(form.find('button[type="submit"]'));

            if($('#installmentdate').val().trim()==''){
                toastr.error('عفوا برجاء إداخال تاريخ بداية القسط');
                __enable_submit_button(form.find('button[type="submit"]'));
               return true;
            }



            var data = form.serialize();
            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                beforeSend: function (xhr) {
                    __disable_submit_button(form.find('button[type="submit"]'));
                },
                success: function (result) {
                    if (result.success == true) {
                        $('div.div_modal').modal('hide');
                        toastr.success(result.msg);
                        sell_table.ajax.reload();
                        } else {
                        __enable_submit_button(form.find('button[type="submit"]'));
                        toastr.error(result.msg);
                    }
                },
            });
        });
        $(".integer").keydown(function (e) {

            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }

        });
        $(".decimal").keydown(function (e) {
            var val= $(this).val();
            if(val.split('.').length>1&& e.keyCode==110){
                e.preventDefault();
            }
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190,110]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }

        });

        $('.decimal_old').keyup(function(){
            var val = $(this).val();
            if(isNaN(val)){
                val = val.replace(/[^0-9\.]/g,'');
                if(val.split('.').length>2)
                    val =val.replace(/\.+$/,"");
            }
            $(this).val(val);
        });
    });

</script>
