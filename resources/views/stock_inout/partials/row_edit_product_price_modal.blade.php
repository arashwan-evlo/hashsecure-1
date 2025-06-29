<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">{{$product->product_name}} - {{$product->sub_sku}}</h4>
		</div>
		<div class="modal-body">
			<div class="row">

				<div class="form-group col-xs-6 ">
					<label>الرقم البحري : </label>
					<input type="text" name="products[{{$row_index}}][sea_number]"
						   class="form-control product_unit_price input_number" value="0">
		    	</div>
            <div class="form-group col-xs-6 ">
					<label>رقم الباتش : </label>
				<input type="text" name="products[{{$row_index}}][batch_number]"
					   class="form-control product_unit_price input_number" value="0">
			</div>

				<div class="form-group col-xs-6 ">
					<label>عدد الوحدات الموجودة في البالتة : </label>
					<input type="text" name="products[{{$row_index}}][baleta_number]"
						   class="form-control product_unit_price input_number" value="0">
				</div>
				<div class="form-group col-xs-6 ">
					<label>رقم LPN : </label>
					<input type="text" name="products[{{$row_index}}][lpn_number]"
						   class="form-control product_unit_price input_number" value="0">
				</div>

				<div class="clearfix"></div>
				 <div class="form-group col-xs-6 ">
					<label>تاريخ الإنتاج : </label>
					 <input type="text" name="products[{{$row_index}}][production_date]"
							class="form-control expiry_datepicker exp_date" value="">

				 </div>
				<div class="form-group col-xs-6 ">
					<label>تاريخ الإنتهاء : </label>
					 <input type="text" name="products[{{$row_index}}][exp_date]"
							class="form-control expiry_datepicker exp_date" value="">

				 </div>

				<div class="form-group col-xs-12">
		      		<label>@lang('lang_v1.description')</label>
		      		<textarea class="form-control" name="products[{{$row_index}}][sell_line_note]" rows="3"></textarea>

		      	</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
		</div>
	</div>
</div>
