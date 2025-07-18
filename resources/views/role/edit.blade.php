@extends('layouts.app')
@if($role->id)
    @section('title', __('role.edit_role'))
@else
  @section('title', __('role.add_role'))
@endif
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        @if($role->id)
          <h1>@lang( 'role.edit_role' )</h1>
        @else
            <h1>@lang( 'role.add_role' )</h1>
        @endif

    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget', ['class' => 'box-primary'])
            @if($role->id)
                {!! Form::open(['url' => action('RoleController@update', [$role->id]), 'id' => 'role_form' ]) !!}
                <input name="_method" type="hidden" value="PUT">
            @else
                {!! Form::open(['url' => action('RoleController@store'), 'method' => 'post', 'id' => 'role_add_form' ]) !!}
                <input name="_method" type="hidden" value="post">
            @endif

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', __( 'user.role_name' ) . ':*') !!}
                        {!! Form::text('name', str_replace( '#' . auth()->user()->business_id, '', $role->name) , ['class' => 'form-control', 'required', 'placeholder' => __( 'user.role_name' ) ]); !!}
                    </div>
                </div>
            </div>
            @if(in_array('service_staff', $enabled_modules))
                <div class="row">
                    <div class="col-md-2">
                        <h4>@lang( 'lang_v1.user_type' )</h4>
                    </div>
                    <div class="col-md-9 col-md-offset-1">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('is_service_staff', 1, $role->is_service_staff,
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.service_staff' ) }}
                                </label>
                                @show_tooltip(__('restaurant.tooltip_service_staff'))


                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-3">
                    <label>@lang( 'user.permissions' ):</label>
                </div>
            </div>
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.user' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.view', in_array('user.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.create', in_array('user.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.update', in_array('user.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'user.delete', in_array('user.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.user.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

               {{----}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'user.roles' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.view', in_array('roles.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_role' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.create', in_array('roles.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.add_role' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.update', in_array('roles.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.edit_role' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'roles.delete', in_array('roles.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.delete_role' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.supplier' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supplier.view', in_array('supplier.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_supplier' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supplier.view_own', in_array('supplier.view_own', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_supplier' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supplier.create', in_array('supplier.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supplier.update', in_array('supplier.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'supplier.delete', in_array('supplier.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.supplier.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.customer' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'customer.view', in_array('customer.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_all_customer' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'customer.view_own', in_array('customer.view_own', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_customer' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'customer.create', in_array('customer.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'customer.update', in_array('customer.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'customer.delete', in_array('customer.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.customer.delete' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'business.product' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.view', in_array('product.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.view' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.create', in_array('product.create', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.create' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.update', in_array('product.update', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.update' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.delete', in_array('product.delete', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.delete' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'product.opening_stock', in_array('product.opening_stock', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.add_opening_stock' ) }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'stcok_compares', in_array('stcok_compares', $role_permissions),
                                [ 'class' => 'input-icheck']); !!}مقارنة المخازن
                            </label>
                        </div>
                    </div>



                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'view_purchase_price', in_array('view_purchase_price', $role_permissions),['class' => 'input-icheck']); !!}
                                {{ __('lang_v1.view_purchase_price') }}
                            </label>
                            @show_tooltip(__('lang_v1.view_purchase_price_tooltip'))
                        </div>
                    </div>
                </div>
            </div>

            @if(in_array('purchases', $enabled_modules) || in_array('stock_adjustment', $enabled_modules) )
                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'role.purchase' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">




                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.view', in_array('purchase.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض المشتريات
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.porduct_qty_setting', in_array('purchase.porduct_qty_setting', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} ضبط كمية المنتجات والمخزون الهالك
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.create', in_array('purchase.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.edit_composeit_discount', in_array('purchase.edit_composeit_discount', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} تعديل قيم الخصم المركب اثناء الشراء
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.update', in_array('purchase.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.delete', in_array('purchase.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase.delete' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.payments', in_array('purchase.payments', $role_permissions),['class' => 'input-icheck']); !!}
                                    اضافة دفع شراء
                                </label>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase_payment.edit', in_array('purchase_payment.edit', $role_permissions),['class' => 'input-icheck']); !!}
                                    تعديل دفع الشراء
                                </label>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase_payment.delete', in_array('purchase_payment.delete', $role_permissions),['class' => 'input-icheck']); !!}
                                    حذف دفغ الشراء
                                </label>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase.update_status', in_array('purchase.update_status', $role_permissions),['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.update_status') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'view_own_purchase', in_array('view_own_purchase', $role_permissions),['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.view_own_purchase') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase_return.view', in_array('purchase_return.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} مرتجع المشتريات
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'purchase_return.create', in_array('purchase_return.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} اضافة مرتجع المشتريات
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-----------------for stocktransfer -------------------->
                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>تحويلات المخازن </h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">



                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_transfer', in_array('stock_transfer', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}عرض تحويلات المخازن
                                </label>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_transfer.create_pending', in_array('stock_transfer.create_pending', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} انشاء وتعديل  تحويل مخازن -طلب -
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_transfer.create_confirmed', in_array('stock_transfer.create_confirmed', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} انشاء وتعديل  تحويل مخازن -  تأكيد-
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_transfer.create_in_transit', in_array('stock_transfer.create_in_transit', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} انشاء وتعديل  تحويل مخازن - تسليم -

                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_transfer.create_completed', in_array('stock_transfer.create_completed', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} انشاء وتعديل  تحويل مخازن - استلام --

                                </label>
                            </div>
                        </div>

                    </div>
                </div>

            @endif

                <div class="row check_group">
                    <div class="col-md-2">
                        <h4> إدارة المخزون </h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stockadjustment.view', in_array('stockadjustment.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} إدارة المخزون
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stockadjustment.create_in', in_array('stockadjustment.create_in', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} إذن أضافة
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stockadjustment.create_out', in_array('stockadjustment.create_out', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}  إذن صرف
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stockadjustment.create_broken', in_array('stockadjustment.create_broken', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} إضافة مخزون تالف
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stockadjustment.transafare', in_array('stockadjustment.transafare', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} قائمة تحويلات المخزون

                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stockadjustment.import', in_array('stockadjustment.import', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} إستيراد المخزون الإفتتاحي

                                </label>
                            </div>
                        </div>

                    </div>
                </div>

        {{--Sells Permissions--}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'sale.sale' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sales.print_invoice', in_array('sales.print_invoice', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} طباعة الفاتورة
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'sales.pos_meswada', in_array('sales.pos_meswada', $role_permissions),
                                [ 'class' => 'input-icheck']); !!}     عمل مسودة
                            </label>
                        </div>
                    </div>


                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.edit_composite_discount', in_array('sales.edit_composite_discount', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}      تعديل الخصم المركب في نقطة البيع
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.price_offer', in_array('sales.price_offer', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}    عمل عرض سعر
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.puse_sell', in_array('sales.puse_sell', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     تعليق عملية بيع
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.puse_show', in_array('sales.puse_show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}عرض عمليات البيع المعلقة
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.sell_agel', in_array('sales.sell_agel', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     بيع اجل
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.pay_card', in_array('sales.pay_card', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     بطاقة دفع
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.multi_pay_ways', in_array('sales.multi_pay_ways', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     طرق تحصيل متعددة
                                </label>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.sell_in_cash', in_array('sales.sell_in_cash', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     بيع كاش
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.less_than_purchase_price', in_array('sales.less_than_purchase_price', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     البيع باقل من سعر الشراء
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.show', in_array('sales.show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض قائمة المبيعات
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.show_current_stock_in_pos', in_array('sales.show_current_stock_in_pos', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض المخزون الحالي  في نقطة البيع
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales.show_purchase_price_in_pos', in_array('sales.show_purchase_price_in_pos', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض سعر الشراء في نقطة البيع
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'today_sells_total.show', in_array('today_sells_total.show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض الاجمالي اليومي
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.view', in_array('sell.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.installment', in_array('sell.installment', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} جميع الاقساط
                                </label>
                            </div>
                        </div>
                        @if(in_array('pos_sale', $enabled_modules))
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'sell.create', in_array('sell.create', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.create' ) }}
                                    </label>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.update', in_array('sell.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.delete', in_array('sell.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sell.delete' ) }}
                                </label>
                            </div>
                        </div>
                        @if(in_array('add_sale', $enabled_modules))
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'direct_sell.access', in_array('direct_sell.access', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __( 'role.direct_sell.access' ) }}
                                    </label>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'list_drafts', in_array('list_drafts', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.list_drafts' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'list_quotations', in_array('list_quotations', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.list_quotations' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'view_own_sell_only', in_array('view_own_sell_only', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_own_sell_only' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell.payments', in_array('sell.payments', $role_permissions),['class' => 'input-icheck']); !!}
                                    اضافة دفع بيع
                                </label>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell_payment.edit', in_array('sell_payment.edit', $role_permissions),['class' => 'input-icheck']); !!}
                                    تعديل دفع البيع
                                </label>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell_payment.delete', in_array('sell_payment.delete', $role_permissions),['class' => 'input-icheck']); !!}
                                    حذف دفغ البيع
                                </label>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_price_from_sale_screen', in_array('edit_product_price_from_sale_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_price_from_sale_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_price_from_pos_screen', in_array('edit_product_price_from_pos_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_price_from_pos_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_discount_from_sale_screen', in_array('edit_product_discount_from_sale_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_discount_from_sale_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'edit_product_discount_from_pos_screen', in_array('edit_product_discount_from_pos_screen', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.edit_product_discount_from_pos_screen') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'discount.access', in_array('discount.access', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.discount.access') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'access_shipping', in_array('access_shipping', $role_permissions), ['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.access_shipping') }}
                                </label>
                            </div>
                        </div>
                        @if(in_array('types_of_service', $enabled_modules))
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'access_types_of_service', in_array('access_types_of_service', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_types_of_service' ) }}
                                    </label>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'access_sell_return', in_array('access_sell_return', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'مرتجع المبيعات' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'importsales.create', in_array('importsales.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} استيراد مبيعات من نظام اخر
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'recent_transaction.view', in_array('recent_transaction.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     اخر المعاملات في شاشة نقاط البيع
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'customer_balance_due_in_pos', in_array('customer_balance_due_in_pos', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}    عرض الرصيد المستحق علي العميل في شاشة نقاط البيع
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'pos_lite',  in_array('pos_lite', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}    نقطة بيع لايت
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'pos_repair',  in_array('pos_repair', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}    نقطة بيع الصيانة
                                </label>
                            </div>
                        </div>




                </div>
            </div>


            {{--Expenses--}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'role.expenses' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expenses.view',  in_array('expenses.view', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.expenses.view' ) }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expenses.categories', in_array('expenses.categories', $role_permissions),
                                ['class' => 'input-icheck']); !!} {{ __('role.expenses.categories') }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expenses.create', in_array('expenses.create', $role_permissions),
                                ['class' => 'input-icheck']); !!} {{ __('role.expenses.create') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expenses.edit',  in_array('expenses.edit', $role_permissions),
                                ['class' => 'input-icheck']); !!} {{ __('role.expenses.edit') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'expenses.delete',  in_array('expenses.delete', $role_permissions),
                                ['class' => 'input-icheck']); !!} {{ __('role.expenses.delete') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>


            {{-- End of Expense --}}





                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'cash_register.cash_register' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'view_cash_register', in_array('view_cash_register', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_cash_register' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'close_cash_register', in_array('close_cash_register', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.close_cash_register' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'register_payment_details.view', in_array('register_payment_details.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}    عرض تفاصيل الدفعات  في كشف الوردية
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'register_product_details.view',in_array('register_product_details.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!}     عرض تفاصيل المنتجات المباعة في كشف الوردية
                                </label>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'role.brand' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'brand.view', in_array('brand.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'brand.create', in_array('brand.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'brand.update', in_array('brand.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'brand.delete', in_array('brand.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.brand.delete' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'role.tax_rate' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'tax_rate.view', in_array('tax_rate.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'tax_rate.create', in_array('tax_rate.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'tax_rate.update', in_array('tax_rate.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'tax_rate.delete', in_array('tax_rate.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_rate.delete' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'role.unit' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'unit.view', in_array('unit.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'unit.create', in_array('unit.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'unit.update', in_array('unit.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'unit.delete', in_array('unit.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.unit.delete' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'category.category' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'category.view', in_array('category.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'category.create', in_array('category.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.create' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'category.update', in_array('category.update', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.update' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'category.delete', in_array('category.delete', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.category.delete' ) }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'role.report' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'total_report_main', in_array('total_report_main', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.total_report_main' ) }}
                                </label>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'product.ProductStockAlert', in_array('product.ProductStockAlert', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.product.ProductStockAlert' ) }}
                                </label>
                            </div>
                        </div>


                        @if(in_array('purchases', $enabled_modules) || in_array('add_sale', $enabled_modules) || in_array('pos_sale', $enabled_modules))
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'purchase_n_sell_report.view', in_array('purchase_n_sell_report.view', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __( 'role.purchase_n_sell_report.view' ) }}
                                    </label>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'tax_report.view', in_array('tax_report.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.tax_report.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'contacts_report.view', in_array('contacts_report.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.contacts_report.view' ) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'profit_loss_report.view', in_array('profit_loss_report.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.profit_loss_report.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_report.view', in_array('stock_report.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.stock_report.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'stock_missing_report.view', in_array('stock_missing_report.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} تقرير النواقص
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'trending_product_report.view', in_array('trending_product_report.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.trending_product_report.view' ) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'register_report.view', in_array('register_report.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} تقرير الوردية
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sales_representative.view', in_array('sales_representative.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.sales_representative.view' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'view_product_stock_value', in_array('view_product_stock_value', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.view_product_stock_value' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'less_trending_product_report.view', in_array('less_trending_product_report.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} المنتجات الراكدة
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'sell_purchase_lines_report.view', in_array('sell_purchase_lines_report.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} تقرير حركة صنف
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row check_group">
                    <div class="col-md-2">
                        <h4>@lang( 'role.settings' )</h4>
                    </div>
                    <div class="col-md-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'business_settings.backup_database', in_array('business_settings.backup_database', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} نسخ احتياطي
                                </label>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'business_settings.access', in_array('business_settings.access', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.business_settings.access' ) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'barcode_settings.access', in_array('barcode_settings.access', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.barcode_settings.access' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'invoice_settings.access', in_array('invoice_settings.access', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'role.invoice_settings.access' ) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'access_printers', in_array('access_printers', $role_permissions),['class' => 'input-icheck']); !!}
                                    {{ __('lang_v1.access_printers') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

        <div style="">
            {{--dashboard--}}
            <div class="row check_group hidden">
                <div class="col-md-3">
                    <h4>@lang( 'role.dashboard' )</h4>
                </div>
                <div class="col-md-8">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'dashboard.data', in_array('dashboard.data', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'role.dashboard.data' ) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{--account.account--}}
            <div class="row check_group">
                <div class="col-md-2">
                    <h4>@lang( 'account.account' )</h4>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                        </label>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.access', in_array('account.access', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.access_accounts' ) }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.fund-transfer', in_array('account.fund-transfer', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} تحويل أموال
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('permissions[]', 'account.add', in_array('account.add', $role_permissions),
                                [ 'class' => 'input-icheck']); !!} أيداع أموال
                            </label>
                        </div>
                    </div>

                </div>


            </div>
        </div>


                @if(in_array('booking', $enabled_modules))
                    <div class="row check_group">
                        <div class="col-md-2">
                            <h4>@lang( 'restaurant.bookings' )</h4>
                        </div>
                        <div class="col-md-2">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'crud_all_bookings', in_array('crud_all_bookings', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_all_booking' ) }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'crud_own_bookings', in_array('crud_own_bookings', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __( 'restaurant.add_edit_view_own_booking' ) }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                @endif
                <div class="row check_group ">
                    <div class="col-md-3">
                        <h4>@lang( 'lang_v1.access_selling_price_groups' )</h4>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'access_default_selling_price', in_array('access_default_selling_price', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.default_selling_price') }}
                                </label>
                            </div>
                        </div>
                        @if(count($selling_price_groups) > 0)
                            @foreach($selling_price_groups as $selling_price_group)
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('spg_permissions[]', 'selling_price_group.' . $selling_price_group->id, in_array('selling_price_group.' . $selling_price_group->id, $role_permissions),
                                            [ 'class' => 'input-icheck']); !!} {{ $selling_price_group->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

            {{--Projects Setting--}}
              {{--  <div class="row">
                    <div class="col-md-3">
                        <h4> Projects Setting </h4>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'projcts.show', in_array('projcts.show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض المشروعات
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h4> مديول كاتالوج </h4>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'catalouge.show', in_array('catalouge.show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} عرض الكتالوج
                                </label>
                            </div>
                        </div>
                    </div>

                </div>--}}
           {{--Repair--}}
            {{--    <div class="row">
                    <div class="col-md-3">
                        <h4> مديول الصيانة </h4>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'repair_setting.view', in_array('repair_setting.view', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} الاعدادت
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'repair_device_model.create', in_array('repair_device_model.create', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} اضافة موديل جهاز
                                </label>
                            </div>
                        </div>
                    </div>
                </div>--}}

            {{--HRM Setting--}}
               {{-- <div class="row check_group">
                    <div class="col-md-3">
                        <h4> HRM Setting </h4>
                    </div>
                    <div class="col-md-8">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    {!! Form::checkbox('permissions[]', 'hrm_show', in_array('hrm_show', $role_permissions),
                                    [ 'class' => 'input-icheck']); !!} HRM
                                </label>
                            </div>
                        </div>
                    </div>

                </div>--}}
                @if(in_array('tables', $enabled_modules))
                    <div class="row check_group">
                        <div class="col-md-3">
                            <h4>@lang( 'restaurant.restaurant' )</h4>
                        </div>
                        <div class="col-md-8">
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'restaurant.view', in_array('restaurant.view', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.restaurant.view') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('permissions[]', 'access_tables', in_array('access_tables', $role_permissions),
                                        [ 'class' => 'input-icheck']); !!} {{ __('lang_v1.access_tables') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @include('role.partials.module_permissions')
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.update' )</button>
                    </div>
                </div>

            {!! Form::close() !!}
        @endcomponent
    </section>
    <!-- /.content -->
@endsection
