<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        @php
            $form_id = 'contact_add_form';
            if(isset($quick_add)){
              $form_id = 'quick_add_contact';
            }else{
                $quick_add=false;
            }

            if(isset($store_action)) {
              $url = $store_action;
              $type = 'lead';
              $customer_groups = [];
            } else {
              $url = action('ContactController@store');
              $type = isset($selected_type) ? $selected_type : '';
              $sources = [];
              $life_stages = [];
             $users = [];
            }
        @endphp
        {!! Form::open(['url' => $url, 'method' => 'post', 'id' => $form_id ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('contact.add_contact')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-4 contact_type_div">
                    <div class="form-group">
                        {!! Form::label('type', __('contact.contact_type') . ':*' ) !!}
                        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                            {!! Form::select('type', $types, $type , ['class' => 'form-control', 'id' => 'contact_type','placeholder' => __('messages.please_select'), 'required']); !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('supplier_business_name', __('business.business_name') . ':') !!}
                        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-briefcase"></i>
                        </span>
                            {!! Form::text('supplier_business_name', null, ['class' => 'form-control']); !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
                        {!! Form::text('first_name', null, ['class' => 'form-control', 'required']); !!}
                    </div>
                </div>

                <div class="col-md-3 hidden">
                    <div class="form-group">
                        {!! Form::label('middle_name', __( 'lang_v1.middle_name' ) . ':') !!}
                        {!! Form::text('middle_name', null, ['class' => 'form-control' ]); !!}
                    </div>
                </div>
                <div class="col-md-3 hidden">
                    <div class="form-group">
                        {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
                        {!! Form::text('last_name', null, ['class' => 'form-control' ]); !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
                        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-mobile"></i>
                        </span>
                            {!! Form::text('mobile', null, ['class' => 'form-control', 'required']); !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
                        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-phone"></i>
                        </span>
                            {!! Form::text('alternate_number', null, ['class' => 'form-control']); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('landline', __('contact.landline') . ':') !!}
                        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-phone"></i>
                        </span>
                            {!! Form::text('landline', null, ['class' => 'form-control']); !!}
                        </div>
                    </div>
                </div>

<div class="clearfix"></div>

                @if($quick_add!=true)
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('routing_type','نوع التوجيه') !!}
                            <select class="form-control select2" id="routing_type" name="routing_type">
                                <option value="main_account"> إنشاء حساب فرعي جديد</option>
                                <option value="sub_account">الربط مع حساب فرعي</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" id="main_account_dev">
                        <div class="form-group">
                            {!! Form::label('account_id', __('account.main_accounts') ) !!}
                            {!! Form::select('account_id', $accounts,null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'parent_id' ]); !!}

                        </div>
                    </div>

                    <div class="col-md-4 hidden " id="sub_account_dev">
                        <div class="form-group ">
                            {!! Form::label('sub_account_id','الحساب الفرعي : ') !!}
                            {!! Form::select('sub_account_id', $sub_accounts,null, ['class' => 'form-control select2 ', 'style' => 'width:100%', 'id' => 'sub_account_id' ]); !!}

                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('contact_id', __('account.account_code') . ':') !!}
                            <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-id-badge"></i>
                            </span>
                                {!! Form::text('contact_id', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 customer_fields">
                        <div class="form-group">
                            <label>المجموعة التسعيرية</label>
                            <div class="input-group">
                      <span class="input-group-addon">
                          <i class="fa fa-users"></i>
                      </span>
                                <?php
                                $business_id = request()->session()->get('user.business_id');
                                $price_groups = \App\SellingPriceGroup::where('business_id', $business_id)
                                    ->active()
                                    ->get(); ?>
                                <select id="price_groups" class="form-control" name="price_group_id">
                                    <option value="">افتراضي</option>

                                    @foreach($price_groups as $row)
                                        <option value="{{$row->id}}">{{$row->name}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 customer_fields">
                        <div class="form-group">
                            {!! Form::label('customer_group_id', __('lang_v1.customer_group') . ':') !!}
                            <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-users"></i>
                          </span>
                                {!! Form::select('customer_group_id', $customer_groups, '', ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="clearfix customer_fields"></div>
                @endif

                <div class="col-md-3 hidden">
                    <div class="form-group">
                        {!! Form::label('prefix', __( 'business.prefix' ) . ':') !!}
                        {!! Form::text('prefix', null, ['class' => 'form-control' ]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('email', __('business.email') . ':') !!}
                        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                        </span>
                            {!! Form::email('email', null, ['class' => 'form-control']); !!}
                        </div>
                    </div>
                </div>
                <!-- lead additional field -->
                <div class="col-md-4 lead_additional_div">
                    <div class="form-group">
                        {!! Form::label('crm_source', __('lang_v1.source') . ':' ) !!}
                        <div class="input-group">
                      <span class="input-group-addon">
                          <i class="fas fa fa-search"></i>
                      </span>
                            {!! Form::select('crm_source', $sources, null , ['class' => 'form-control', 'id' => 'crm_source','placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                </div>

                <div class="col-md-4 lead_additional_div">
                    <div class="form-group">
                        {!! Form::label('crm_life_stage', __('lang_v1.life_stage') . ':' ) !!}
                        <div class="input-group">
                      <span class="input-group-addon">
                          <i class="fas fa fa-life-ring"></i>
                      </span>
                            {!! Form::select('crm_life_stage', $life_stages, null , ['class' => 'form-control', 'id' => 'crm_life_stage','placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6 lead_additional_div">
                    <div class="form-group">
                        {!! Form::label('user_id', __('lang_v1.assigned_to') . ':*' ) !!}
                        <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-user"></i>
                          </span>
                            {!! Form::select('user_id[]', $users, null , ['class' => 'form-control select2', 'id' => 'user_id', 'multiple', 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary center-block more_btn"
                            data-target="#more_div">@lang('lang_v1.more_info') <i class="fa fa-chevron-down"></i>
                    </button>
                </div>

                <div id="more_div" class="hide">
                    {!! Form::hidden('position', null, ['id' => 'position']); !!}
                    <div class="col-md-12">
                        <hr/>
                    </div>
                    <div class="col-sm-4 hidden">
                        <div class="form-group">
                            {!! Form::label('dob', __('lang_v1.dob') . ':') !!}
                            <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </span>

                                {!! Form::text('dob', null, ['class' => 'form-control dob-date-picker','placeholder' => __('lang_v1.dob'), 'readonly']); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('tax_number', __('lang_v1.commercial_register') . ':') !!}
                            <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-info"></i>
                          </span>
                                {!! Form::text('commercial_register', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('tax_number', __('contact.tax_no') . ':') !!}
                            <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fa fa-info"></i>
                          </span>
                                {!! Form::text('tax_number', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>

                    @php
                        $common_settings = session()->get('business.common_settings');
                        $default_credit_limit = !empty($common_settings['default_credit_limit']) ? $common_settings['default_credit_limit'] : null;
                    @endphp
                    <div class="col-md-4 customer_fields">
                        <div class="form-group">
                            {!! Form::label('credit_limit', __('lang_v1.credit_limit') . ':') !!}
                            <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fas fa-money-bill-alt"></i>
                          </span>
                                {!! Form::text('credit_limit', $default_credit_limit ?? null, ['class' => 'form-control input_number']); !!}
                            </div>
                            <p class="help-block">@lang('lang_v1.credit_limit_help')</p>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-4 opening_balance">
                        <div class="form-group">
                            {!! Form::label('opening_balance', __('lang_v1.opening_balance') . ':') !!}
                            <div class="input-group">
                          <span class="input-group-addon">
                              <i class="fas fa-money-bill-alt"></i>
                          </span>
                                {!! Form::text('opening_balance', 0, ['class' => 'form-control input_number']); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 opening_balance">
                        <div class="form-group">
                            {!! Form::label('opening_balance','نوع الرصيد الإفتتاحي : ') !!}
                            <select class="form-control select2" name="opening_balance_type" style="width: 100%">
                                <option value="debit">مدين</option>
                                <option value="credit">دائن</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-4  " >
                        <div class="form-group ">
                            {!! Form::label('opening_balance_account_id','  الحساب الفرعي  : ') !!}
                            {!! Form::select('opening_balance_account_id', $sub_accounts,$journal->account_id, ['class' => 'form-control select2 ', 'style' => 'width:100%', 'id' => 'account_id' ]); !!}

                        </div>
                    </div>


                    <div class="col-md-4 pay_term hidden">
                        <div class="form-group">
                            <div class="multi-input">
                                {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} 
                                <br/>
                                {!! Form::number('pay_term_number', null, ['class' => 'form-control width-40 pull-left']); !!}

                                {!! Form::select('pay_term_type', ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')], '', ['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select')]); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('address_line_1', __('lang_v1.address_line_1') . ':') !!}
                            {!! Form::text('address_line_1', null, ['class' => 'form-control', 'rows' => 3]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('address_line_2', __('lang_v1.address_line_2') . ':') !!}
                            {!! Form::text('address_line_2', null, ['class' => 'form-control', 'rows' => 3]); !!}
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('city', __('business.city') . ':') !!}
                            <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-map-marker"></i>
                        </span>
                                {!! Form::text('city', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('state', __('business.state') . ':') !!}
                            <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                                {!! Form::text('state', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('country', __('business.country') . ':') !!}
                            <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-globe"></i>
                    </span>
                                {!! Form::text('country', null, ['class' => 'form-control']); !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('zip_code', __('business.zip_code') . ':') !!}
                            <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-map-marker"></i>
                    </span>
                                {!! Form::text('zip_code', null, ['class' => 'form-control',
                                ]); !!}
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <div class="col-md-12 shipping_addr_div">
                        <strong>{{__('lang_v1.shipping_address')}}</strong><br>
                        {!! Form::text('shipping_address', null, ['class' => 'form-control',
                               'id' => 'shipping_address']); !!}
                        <div id="map"></div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                    </div>
                    @php
                        $custom_labels = json_decode(session('business.custom_labels'), true);
                        $contact_custom_field1 = !empty($custom_labels['contact']['custom_field_1']) ? $custom_labels['contact']['custom_field_1'] : __('lang_v1.contact_custom_field1');
                        $contact_custom_field2 = !empty($custom_labels['contact']['custom_field_2']) ? $custom_labels['contact']['custom_field_2'] : __('lang_v1.contact_custom_field2');
                        $contact_custom_field3 = !empty($custom_labels['contact']['custom_field_3']) ? $custom_labels['contact']['custom_field_3'] : __('lang_v1.contact_custom_field3');
                        $contact_custom_field4 = !empty($custom_labels['contact']['custom_field_4']) ? $custom_labels['contact']['custom_field_4'] : __('lang_v1.contact_custom_field4');
                        $contact_custom_field5 = !empty($custom_labels['contact']['custom_field_5']) ? $custom_labels['contact']['custom_field_5'] : __('lang_v1.custom_field', ['number' => 5]);
                        $contact_custom_field6 = !empty($custom_labels['contact']['custom_field_6']) ? $custom_labels['contact']['custom_field_6'] : __('lang_v1.custom_field', ['number' => 6]);
                        $contact_custom_field7 = !empty($custom_labels['contact']['custom_field_7']) ? $custom_labels['contact']['custom_field_7'] : __('lang_v1.custom_field', ['number' => 7]);
                        $contact_custom_field8 = !empty($custom_labels['contact']['custom_field_8']) ? $custom_labels['contact']['custom_field_8'] : __('lang_v1.custom_field', ['number' => 8]);
                        $contact_custom_field9 = !empty($custom_labels['contact']['custom_field_9']) ? $custom_labels['contact']['custom_field_9'] : __('lang_v1.custom_field', ['number' => 9]);
                        $contact_custom_field10 = !empty($custom_labels['contact']['custom_field_10']) ? $custom_labels['contact']['custom_field_10'] : __('lang_v1.custom_field', ['number' => 10]);
                    @endphp
                    @if( !empty($custom_labels['contact']['custom_field_1']))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field1', $contact_custom_field1 . ':') !!}
                                {!! Form::text('custom_field1', null, ['class' => 'form-control'] ); !!}
                            </div>
                        </div>
                    @endif
                    @if( !empty($custom_labels['contact']['custom_field_2']))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field2', $contact_custom_field2 . ':') !!}
                                {!! Form::text('custom_field2', null, ['class' => 'form-control'] ); !!}
                            </div>
                        </div>
                    @endif
                    @if( !empty($custom_labels['contact']['custom_field_3']))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field3', $contact_custom_field3 . ':') !!}
                                {!! Form::text('custom_field3', null, ['class' => 'form-control'] ); !!}
                            </div>
                        </div>
                    @endif
                    @if( !empty($custom_labels['contact']['custom_field_4']))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field4', $contact_custom_field4 . ':') !!}
                                {!! Form::text('custom_field4', null, ['class' => 'form-control'] ); !!}
                            </div>
                        </div>
                    @endif
                    @if( !empty($custom_labels['contact']['custom_field_1']))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field5', $contact_custom_field5 . ':') !!}
                                {!! Form::text('custom_field5', null, ['class' => 'form-control'] ); !!}
                            </div>
                        </div>
                    @endif
                    @if( !empty($custom_labels['contact']['custom_field_6']))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field6', $contact_custom_field6 . ':') !!}
                                {!! Form::text('custom_field6', null, ['class' => 'form-control'] ); !!}
                            </div>
                        </div>
                    @endif
                    @if( !empty($custom_labels['contact']['custom_field_7']))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field7', $contact_custom_field7 . ':') !!}
                                {!! Form::text('custom_field7', null, ['class' => 'form-control'] ); !!}
                            </div>
                        </div>
                    @endif
                    @if( !empty($custom_labels['contact']['custom_field_8']))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field8', $contact_custom_field8 . ':') !!}
                                {!! Form::text('custom_field8', null, ['class' => 'form-control'] ); !!}
                            </div>
                        </div>
                    @endif
                    @if( !empty($custom_labels['contact']['custom_field_9']))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field9', $contact_custom_field9 . ':') !!}
                                {!! Form::text('custom_field9', null, ['class' => 'form-control'] ); !!}
                            </div>
                        </div>
                    @endif
                    @if( !empty($custom_labels['contact']['custom_field_10']))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('custom_field10', $contact_custom_field10 . ':') !!}
                                {!! Form::text('custom_field10', null, ['class' => 'form-control'] ); !!}
                            </div>
                        </div>
                    @endif


                </div>
            </div>
            {{--@include('layouts.partials.module_form_part')--}}
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->