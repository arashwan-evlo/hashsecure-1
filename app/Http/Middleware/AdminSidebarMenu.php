<?php

namespace App\Http\Middleware;

use App\Utils\ModuleUtil;
use Carbon\Carbon;
use Closure;
use Menu;

class AdminSidebarMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if ($request->ajax()) {
            return $next($request);
        }



     Menu::create('admin-sidebar-menu', function ($menu) {

         $business_id = session()->get('user.business_id');
         $package = \Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
         $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];

           $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
        /*  dd(session('business.id'), auth()->user()->hasRole('Admin#' . session('business.id')));*/
            //Home  5
            $menu->url(action('HomeController@index'), __('home.home'), ['icon' => 'fa fas fa-tachometer-alt', 'active' => request()->segment(1) == 'home'])->order(5);

            //User management dropdown  10


                 if (auth()->user()->can('user.view') || auth()->user()->can('user.create') || auth()->user()->can('roles.view')) {
                     $menu->dropdown(
                         __('user.user_management'),
                         function ($sub) {
                             if (auth()->user()->can('user.view')) {
                                 $sub->url(
                                     action('ManageUserController@index'),
                                     __('user.users'),
                                     ['icon' => 'fa fas fa-user', 'active' => request()->segment(1) == 'users']
                                 );
                             }
                             if (auth()->user()->can('roles.view')) {
                                 $sub->url(
                                     action('RoleController@index'),
                                     __('user.roles'),
                                     ['icon' => 'fa fas fa-briefcase', 'active' => request()->segment(1) == 'roles']
                                 );
                             }


                             if (auth()->user()->can('roles.view')) {
                                 $sub->url(
                                     action('TaxonomyController@index') . '?type=hrm_department&&users=yes',
                                     __('user.departments'),
                                     ['icon' => 'fa fas fa-briefcase', 'active' => request()->segment(1) == 'taxonomies']
                                 );
                             }


                             if (auth()->user()->can('user.create')) {
                                 $sub->url(
                                     action('SalesCommissionAgentController@index'),
                                     __('lang_v1.sales_commission_agents'),
                                     ['icon' => 'fa fas fa-handshake', 'active' => request()->segment(1) == 'sales-commission-agents']
                                 );
                             }


                         },
                         ['icon' => 'fa fas fa-users']
                     )->order(10);
                 }

            //Contacts dropdown 15
         if(isset($package->package_details['conatcts_module'])) {
             if (auth()->user()->can('supplier.view') || auth()->user()->can('customer.view')) {
                 $menu->dropdown(
                     __('contact.contacts'),
                     function ($sub) {
                         if (auth()->user()->can('supplier.view')) {
                             $sub->url(
                                 action('ContactController@index', ['type' => 'supplier']),
                                 __('report.supplier'),
                                 ['icon' => 'fa fas fa-star', 'active' => request()->input('type') == 'supplier']
                             );
                         }
                         if (auth()->user()->can('customer.view')) {
                             $sub->url(
                                 action('ContactController@index', ['type' => 'customer']),
                                 __('report.customer'),
                                 ['icon' => 'fa fas fa-star', 'active' => request()->input('type') == 'customer']
                             );
                             $sub->url(
                                 action('CustomerGroupController@index'),
                                 __('lang_v1.customer_groups'),
                                 ['icon' => 'fa fas fa-users', 'active' => request()->segment(1) == 'customer-group']
                             );
                         }
                         if (auth()->user()->can('supplier.create') || auth()->user()->can('customer.create')) {
                             $sub->url(
                                 action('ContactController@getImportContacts'),
                                 __('lang_v1.import_contacts'),
                                 ['icon' => 'fa fas fa-download', 'active' => request()->segment(1) == 'contacts' && request()->segment(2) == 'import']
                             );
                         }

                         if (!empty(env('GOOGLE_MAP_API_KEY'))) {
                             $sub->url(
                                 action('ContactController@contactMap'),
                                 __('lang_v1.map'),
                                 ['icon' => 'fa fas fa-map-marker-alt', 'active' => request()->segment(1) == 'contacts' && request()->segment(2) == 'map']
                             );
                         }
                     },
                     ['icon' => 'fa fas fa-address-book', 'id' => "tour_step4"]
                 )->order(15);
             }
         }
            //Products dropdown  20

         if(isset($package->package_details['product_module'])) {
             if (auth()->user()->can('product.view') || auth()->user()->can('product.create') ||
                 auth()->user()->can('brand.view') || auth()->user()->can('unit.view') ||
                 auth()->user()->can('category.view') || auth()->user()->can('brand.create') ||
                 auth()->user()->can('unit.create') || auth()->user()->can('category.create')) {
                 $menu->dropdown(
                     __('sale.products'),
                     function ($sub) {
                         if (auth()->user()->can('product.view')) {
                             $sub->url(
                                 action('ProductController@index'),
                                 __('lang_v1.list_products'),
                                 ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'products' && request()->segment(2) == '']
                             );
                         }
                         if (auth()->user()->can('product.create')) {
                             $sub->url(
                                 action('ProductController@create'),
                                 __('product.add_product'),
                                 ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'create']
                             );
                         }

                         /* if (auth()->user()->can('product.create')) {
                              $sub->url(
                                  action('ProductController@create'),
                                  __('product.product_movements'),
                                  ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'create']
                              );
                          }*/

                         if (auth()->user()->can('product.view')) {
                             $sub->url(
                                 action('LabelsController@show', ['product_id=null']),
                                 __('barcode.print_labels'),
                                 ['icon' => 'fa fas fa-barcode', 'active' => request()->segment(1) == 'labels' && request()->segment(2) == 'show']
                             );
                         }
                         if (auth()->user()->can('product.create')) {
                             $sub->url(
                                 action('VariationTemplateController@index'),
                                 __('product.variations'),
                                 ['icon' => 'fa fas fa-circle', 'active' => request()->segment(1) == 'variation-templates']
                             );
                             $sub->url(
                                 action('ImportProductsController@index'),
                                 __('product.import_products'),
                                 ['icon' => 'fa fas fa-download', 'active' => request()->segment(1) == 'import-products']
                             );
                         }


                         if (auth()->user()->can('product.create')) {
                             $sub->url(
                                 action('SellingPriceGroupController@index'),
                                 __('lang_v1.selling_price_group'),
                                 ['icon' => 'fa fas fa-circle', 'active' => request()->segment(1) == 'selling-price-group']
                             );
                         }
                         if (auth()->user()->can('unit.view') || auth()->user()->can('unit.create')) {
                             $sub->url(
                                 action('UnitController@index'),
                                 __('unit.units'),
                                 ['icon' => 'fa fas fa-balance-scale', 'active' => request()->segment(1) == 'units']
                             );
                         }
                         if (auth()->user()->can('category.view') || auth()->user()->can('category.create')) {
                             $sub->url(
                                 action('TaxonomyController@index') . '?type=product',
                                 __('category.categories'),
                                 ['icon' => 'fa fas fa-tags', 'active' => request()->segment(1) == 'taxonomies' && request()->get('type') == 'product']
                             );
                         }
                         if (auth()->user()->can('brand.view') || auth()->user()->can('brand.create')) {
                             $sub->url(
                                 action('BrandController@index'),
                                 __('brand.brands'),
                                 ['icon' => 'fa fas fa-gem', 'active' => request()->segment(1) == 'brands']
                             );
                         }

                         $sub->url(
                             action('WarrantyController@index'),
                             __('lang_v1.warranties'),
                             ['icon' => 'fa fas fa-shield-alt', 'active' => request()->segment(1) == 'warranties']
                         );


                     },
                     ['icon' => 'fa fas fa-cubes', 'id' => 'tour_step5']
                 )->order(20);
             }
         }

            /* Product Gallery   21 */
           /* if(auth()->user()->can('product.gallary')){
                $menu->dropdown(
                    __('lang_v1.product_gallery'),
                    function ($sub) {
                            $sub->url(
                                action('ProductGallery@gallery'),
                                __('lang_v1.product_gallery'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1)=='gallery' && request()->segment(2)=='gallery' ]
                             );

                            $sub->url(
                                action('ProductGallery@stock_report'),
                                __('report.stock_report'),
                                ['icon' => 'fa fas fa-list', 'active' => request()->segment(1)=='gallery' && request()->segment(2)=='stock_report' ]
                            );



                          $sub->url(
                                action('ProductGallery@setting'),
                                __('lang_v1.gallery_setting'),
                                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'gallery' && request()->segment(2) == 'setting']
                             );
                         },
                    ['icon' => 'fa fas fa-truck']
                )->order(21);
            }*/


            //Purchase dropdown   25
         if(isset($package->package_details['purchase_module'])) {
             if (in_array('purchases', $enabled_modules) && (auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') || auth()->user()->can('purchase.update'))) {
                 $menu->dropdown(
                     __('purchase.purchases'),
                     function ($sub) {
                         if (auth()->user()->can('purchase.view') || auth()->user()->can('view_own_purchase')) {
                             $sub->url(
                                 action('PurchaseController@index'),
                                 __('purchase.list_purchase'),
                                 ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'purchases' && request()->segment(2) == null]
                             );
                         }
                         if (auth()->user()->can('purchase.create')) {
                             $sub->url(
                                 action('PurchaseController@create'),
                                 __('purchase.add_purchase'),
                                 ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'purchases' && request()->segment(2) == 'create']
                             );
                         }
                         if (auth()->user()->can('purchase_return.view')) {
                             $sub->url(
                                 action('PurchaseReturnController@index'),
                                 __('lang_v1.list_purchase_return'),
                                 ['icon' => 'fa fas fa-undo', 'active' => request()->segment(1) == 'purchase-return']
                             );
                         }

                         //        var retu_purchase ='<li><a href="{{URL::to('//product-purchase-report')}}" ><i class="fa fas fa-list"></i><strong>  </strong></a> </li>';
                         if (auth()->user()->can('purchase_return.create')) {
                             $sub->url(
                                 action('ReportController@getproductPurchaseReport'),
                                 __('مرتجع مشتريات '),
                                 ['icon' => 'fa fas fa-undo', 'active' => request()->segment(2) == 'product-purchase-report']
                             );
                         }
                     },
                     ['icon' => 'fa fas fa-arrow-circle-down', 'id' => 'tour_step6']
                 )->order(25);
             }
         }
            //Sell dropdown  30
         if(isset($package->package_details['sell_module'])) {
             if ($is_admin || auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'access_sell_return'])) {
                 $menu->dropdown(
                     __('sale.sale'),
                     function ($sub) use ($enabled_modules, $is_admin) {
                         if ($is_admin || auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
                             $sub->url(
                                 action('SellController@index'),
                                 __('lang_v1.all_sales'),
                                 ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == null]
                             );
                         }
                         if (in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                             $sub->url(
                                 action('SellController@create'),
                                 __('sale.add_sale'),
                                 ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == 'create' && empty(request()->get('status'))]
                             );
                         }
                         if (auth()->user()->can('sell.view')) {
                             $sub->url(
                                 action('SellPosController@index'),
                                 __('sale.list_pos'),
                                 ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'pos' && request()->segment(2) == null]
                             );
                         }
                         if (auth()->user()->can('sell.create')) {
                             if (in_array('pos_sale', $enabled_modules)) {
                                 $sub->url(
                                     action('SellPosController@create'),
                                     __('sale.pos_sale'),
                                     ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'pos' && request()->segment(2) == 'create']
                                 );
                             }
                         }
                         if (in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                             $sub->url(
                                 action('SellController@create', ['status' => 'draft']),
                                 __('lang_v1.add_draft'),
                                 ['icon' => 'fa fas fa-plus-circle', 'active' => request()->get('status') == 'draft']
                             );
                         }
                         if (auth()->user()->can('list_drafts')) {
                             $sub->url(
                                 action('SellController@getDrafts'),
                                 __('lang_v1.list_drafts'),
                                 ['icon' => 'fa fas fa-pen-square', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == 'drafts']
                             );
                         }
                         /*if (in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                             $sub->url(
                                 action('SellController@create', ['status' => 'quotation']),
                                 __('lang_v1.add_quotation'),
                                 ['icon' => 'fa fas fa-plus-circle', 'active' => request()->get('status') == 'quotation']
                             );
                         }
                         if (auth()->user()->can('list_quotations')) {
                             $sub->url(
                                 action('SellController@getQuotations'),
                                 __('lang_v1.list_quotations'),
                                 ['icon' => 'fa fas fa-pen-square', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == 'quotations']
                             );
                         }*/

                         if (auth()->user()->can('access_sell_return')) {
                             $sub->url(
                                 action('SellReturnController@index'),
                                 __('lang_v1.list_sell_return'),
                                 ['icon' => 'fa fas fa-undo', 'active' => request()->segment(1) == 'sell-return' && request()->segment(2) == null]
                             );
                         }

                         if ($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
                             $sub->url(
                                 action('SellController@shipments'),
                                 __('lang_v1.shipments'),
                                 ['icon' => 'fa fas fa-truck', 'active' => request()->segment(1) == 'shipments']
                             );
                         }

                         if (auth()->user()->can('discount.access')) {
                             $sub->url(
                                 action('DiscountController@index'),
                                 __('lang_v1.discounts'),
                                 ['icon' => 'fa fas fa-percent', 'active' => request()->segment(1) == 'discount']
                             );
                         }

                         //dd($enabled_modules);
                         if (in_array('subscription', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                             $sub->url(
                                 action('SellPosController@listSubscriptions'),
                                 __('lang_v1.subscriptions'),
                                 ['icon' => 'fa fas fa-recycle', 'active' => request()->segment(1) == 'subscriptions']
                             );
                         }

                         if (auth()->user()->can('sell.create')) {
                             $sub->url(
                                 action('ImportSalesController@index'),
                                 __('lang_v1.import_sales'),
                                 ['icon' => 'fa fas fa-file-import', 'active' => request()->segment(1) == 'import-sales']
                             );
                         }
                     },
                     ['icon' => 'fa fa-registered', 'id' => 'tour_step7']
                 )->order(30);
             }
         }

            //Kitchen menu 31
         if(isset($package->package_details['kitchen_module'])) {
             if (in_array('kitchen', $enabled_modules)) {
                 if (auth()->user()->can('restaurant.view')) {
                     $menu->dropdown(
                         __('restaurant.restaurant_managment'),
                         function ($sub) {
                             if (auth()->user()->can('purchase.view') || auth()->user()->can('view_own_purchase')) {
                                 $sub->url(
                                     action('Restaurant\KitchenController@index_order'),
                                     __('restaurant.kitchen_order'),
                                     ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'kitchen']
                                 );
                             }

                             $sub->url(
                                 action('Restaurant\OrderController@index'),
                                 __('restaurant.orders'),
                                 ['icon' => 'fa fas fa-list']
                             );
                             $sub->url(
                                 action('Restaurant\BookingController@index'),
                                 __('restaurant.bookings'),
                                 ['icon' => 'fas fa fa-cutlery', 'active' => request()->segment(1) == 'bookings']
                             );

                             $sub->url(
                                 action('ReportController@getTableReport'),
                                 __('restaurant.table_report'),
                                 ['icon' => 'fas fa fa-cutlery', 'active' => request()->segment(2) == 'table-report']
                             );

                             $sub->url(
                                 action('ReportController@getServiceStaffReport'),
                                 __('restaurant.service_staff_report'),
                                 ['icon' => 'fa fas fa-user-secret', 'active' => request()->segment(2) == 'service-staff-report']
                             );

                             $sub->url(
                                 action('Restaurant\TableController@index'),
                                 __('restaurant.tables'),
                                 ['icon' => 'fa fas fa-table', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'tables']
                             );

                             if ((auth()->user()->can('product.view') || auth()->user()->can('product.create'))) {
                                 $sub->url(
                                     action('Restaurant\ModifierSetsController@index'),
                                     __('restaurant.modifiers'),
                                     ['icon' => 'fa fas fa-pizza-slice', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'modifiers']
                                 );
                             }

                             $sub->url(
                                 action('Restaurant\KitchenController@index'),
                                 __('restaurant.restaurant_department'),
                                 ['icon' => 'fa fas fa-table', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'kitchen']
                             );


                             if (auth()->user()->can('kitchen.create')) {
                                 $sub->url(
                                     action('Restaurant\KitchenController@products'),
                                     'أصناف المطبخ',
                                     ['icon' => 'fa fas fa-map-marker', 'active' => request()->segment(2) == 'kitchen_products']
                                 );
                             }

                             /*if (auth()->user()->can('kitchen.create')) {
                                 $sub->url(
                                     action(''),
                                     __('restaurant.orders'),
                                     ['icon' => 'fa fas fa-list-alt', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'orders'])->order(75);
                             }*/


                             //new sub menu


                         },
                         ['icon' => 'fa fa-registered', 'id' => 'tour_step7']
                     )->order(35);
                 }
             }

         }




            //Stock transfer dropdown  35
         if(isset($package->package_details['stock_module'])) {
             if (in_array('stock_transfers', $enabled_modules) && (auth()->user()->can('stockadjustment.view') )) {
                 $menu->dropdown(
                     __('lang_v1.stock_management'),
                     function ($sub) {



                         if (auth()->user()->can('business_settings.access')) {
                             $sub->url(
                                 action('BusinessLocationController@index'),
                                 __('business.business_locations') . ' ( المخازن ) ',
                                 ['icon' => 'fa fas fa-map-marker', 'active' => request()->segment(1) == 'business-location']
                             );

                             $sub->url(
                                 action('BusinessLocationController@locationgroups'),
                                 'أماكن التخزين',
                                 ['icon' => 'fa fas fa-map-marker', 'active' => request()->segment(1) == 'location-groups']
                             );
                         }

                             if (auth()->user()->can('stockadjustment.view')) {
                                 $sub->url(
                                     action('StockAdjustmentController@index'),
                                     __('stock_adjustment.stock_adjustments'),
                                     ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'stock-adjustments' && request()->segment(2) == null]
                                 );
                             }

                         if (auth()->user()->can('stockadjustment.create_in')) {
                             $sub->url(
                                 action('StockInoutController@stock_add'),
                                 'إذن إضافة ',
                                 ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'stock_add']
                             );
                         }


                         if (auth()->user()->can('stockadjustment.create_out')) {
                             $sub->url(
                                 action('StockInoutController@stock_out'),
                                 'إذن صرف ',
                                 ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'stock_out']
                             );

                         }
                             if (auth()->user()->can('stockadjustment.create_broken')) {
                                 $sub->url(
                                     action('StockAdjustmentController@create'),
                                     __('stock_adjustment.add'),
                                     ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'stock-adjustments' && request()->segment(2) == 'create']
                                 );
                             }


                         if (auth()->user()->can('stockadjustment.transafare')) {
                             $sub->url(
                                 action('StockTransferController@transfare_products'),
                                 __('lang_v1.list_stock_transfer_products'),
                                 ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'stock-transfers' && request()->segment(2) == 'transfare_products']
                             );
                         }

                         if (auth()->user()->can('stockadjustment.transafare')) {
                             $sub->url(
                                 action('StockTransferController@index'),
                                 __('lang_v1.list_stock_transfers'),
                                 ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'stock-transfers' && request()->segment(2) == null]
                             );
                         }





                         if (auth()->user()->can('stockadjustment.import')) {
                             $sub->url(
                                 action('ImportOpeningStockController@index'),
                                 __('lang_v1.import_opening_stock'),
                                 ['icon' => 'fa fas fa-download', 'active' => request()->segment(1) == 'import-opening-stock']
                             );
                         }
                     },
                     ['icon' => 'fa fas fa-truck']
                 )->order(35);
             }
         }
            //Expense dropdown 45
         if(isset($package->package_details['expances_module'])) {
             if (in_array('expenses', $enabled_modules) && (auth()->user()->can('expenses.view'))) {
                 $menu->dropdown(
                     __('expense.expenses'),
                     function ($sub) {
                         $sub->url(
                             action('ExpenseController@index'),
                             __('lang_v1.list_expenses'),
                             ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'expenses' && request()->segment(2) == null]
                         );

                         if (auth()->user()->can('expenses.create')) {
                             $sub->url(
                                 action('ExpenseController@create'),
                                 __('expense.add_expense'),
                                 ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'expenses' && request()->segment(2) == 'create']
                             );
                         }


                         if (auth()->user()->can('expenses.categories')) {
                             $sub->url(
                                 action('ExpenseCategoryController@index'),
                                 __('expense.expense_categories'),
                                 ['icon' => 'fa fas fa-circle', 'active' => request()->segment(1) == 'expense-categories']
                             );
                         }
                     },

                     ['icon' => 'fa fas fa-minus-circle']
                 )->order(45);
             }
         }
            //Accounts dropdown 50 account.access
         //if(isset($package->package_details['account_module']))
         //Use chart of account module
       /*  if(isset($package->package_details['account_module'])) {
             if (auth()->user()->can('account.access') && in_array('account', $enabled_modules)) {
                 $menu->dropdown(
                     __('lang_v1.payment_accounts'),
                     function ($sub) {
                         $sub->url(
                             action('AccountController@index'),
                             __('account.list_accounts'),
                             ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'account']
                         );
                         $sub->url(
                             action('AccountReportsController@balanceSheet'),
                             __('account.balance_sheet'),
                             ['icon' => 'fa fas fa-book', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'balance-sheet']
                         );
                         $sub->url(
                             action('AccountReportsController@trialBalance'),
                             __('account.trial_balance'),
                             ['icon' => 'fa fas fa-balance-scale', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'trial-balance']
                         );
                         $sub->url(
                             action('AccountController@cashFlow'),
                             __('lang_v1.cash_flow'),
                             ['icon' => 'fa fas fa-exchange-alt', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'cash-flow']
                         );
                         $sub->url(
                             action('AccountReportsController@paymentAccountReport'),
                             __('account.payment_account_report'),
                             ['icon' => 'fa fas fa-file-alt', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'payment-account-report']
                         );

                         $sub->url(
                             action('AccountController@gettransfer'),
                             __('lang_v1.account_transfer'),
                             ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'account']
                         );
                     },
                     ['icon' => 'fa fas fa-money-check-alt']
                 )->order(50);
             }
         }*/
            //Reports dropdown 55

             if (auth()->user()->can('purchase_n_sell_report.view') || auth()->user()->can('contacts_report.view')
                 || auth()->user()->can('stock_report.view') || auth()->user()->can('tax_report.view')
                 || auth()->user()->can('trending_product_report.view') || auth()->user()->can('sales_representative.view') || auth()->user()->can('register_report.view')
                 || auth()->user()->can('expense_report.view')) {
                 $menu->dropdown(
                     __('report.reports'),
                     function ($sub) use ($enabled_modules, $is_admin) {
                         if (auth()->user()->can('profit_loss_report.view')) {
                             $sub->url(
                                 action('ReportController@getProfitLoss'),
                                 __('report.profit_loss'),
                                 ['icon' => 'fa fas fa-file-invoice-dollar', 'active' => request()->segment(2) == 'profit-loss']
                             );
                         }
                         $sub->url(
                             action('ReportController@getsells'),
                             'يومية المبيعات',
                             ['icon' => 'fa fas fa-arrow-circle-down', 'active' => request()->segment(2) == 'getsells']
                         );

                         if (config('constants.show_report_606') == true) {
                             $sub->url(
                                 action('ReportController@purchaseReport'),
                                 'Report 606 (' . __('lang_v1.purchase') . ')',
                                 ['icon' => 'fa fas fa-arrow-circle-down', 'active' => request()->segment(2) == 'purchase-report']
                             );
                         }


                         if (config('constants.show_report_606') == true) {
                             $sub->url(
                                 action('ReportController@purchaseReport'),
                                 'Report 606 (' . __('lang_v1.purchase') . ')',
                                 ['icon' => 'fa fas fa-arrow-circle-down', 'active' => request()->segment(2) == 'purchase-report']
                             );
                         }

                         if (config('constants.show_report_607') == true) {
                             $sub->url(
                                 action('ReportController@saleReport'),
                                 'Report 607 (' . __('business.sale') . ')',
                                 ['icon' => 'fa fas fa-arrow-circle-up', 'active' => request()->segment(2) == 'sale-report']
                             );
                         }
                         if ((in_array('purchases', $enabled_modules) || in_array('add_sale', $enabled_modules) || in_array('pos_sale', $enabled_modules)) && auth()->user()->can('purchase_n_sell_report.view')) {
                             $sub->url(
                                 action('ReportController@getPurchaseSell'),
                                 __('report.purchase_sell_report'),
                                 ['icon' => 'fa fas fa-exchange-alt', 'active' => request()->segment(2) == 'purchase-sell']
                             );
                         }

                         if (auth()->user()->can('tax_report.view')) {
                             $sub->url(
                                 action('ReportController@getTaxReport'),
                                 __('report.tax_report'),
                                 ['icon' => 'fa fas fa-percent', 'active' => request()->segment(2) == 'tax-report']
                             );
                         }
                         if (auth()->user()->can('contacts_report.view')) {
                             $sub->url(
                                 action('ReportController@getCustomerSuppliers'),
                                 __('report.contacts'),
                                 ['icon' => 'fa fas fa-address-book', 'active' => request()->segment(2) == 'customer-supplier']
                             );
                             $sub->url(
                                 action('ReportController@getCustomerGroup'),
                                 __('lang_v1.customer_groups_report'),
                                 ['icon' => 'fa fas fa-users', 'active' => request()->segment(2) == 'customer-group']
                             );
                         }
                         if (auth()->user()->can('stock_report.view')) {
                             $sub->url(
                                 action('ReportController@getStockReport'),
                                 __('report.stock_report'),
                                 ['icon' => 'fa fas fa-hourglass-half', 'active' => request()->segment(2) == 'stock-report']
                             );
                             if (session('business.enable_product_expiry') == 1) {
                                 $sub->url(
                                     action('ReportController@getStockExpiryReport'),
                                     __('report.stock_expiry_report'),
                                     ['icon' => 'fa fas fa-calendar-times', 'active' => request()->segment(2) == 'stock-expiry']
                                 );
                             }
                             if (session('business.enable_lot_number') == 1) {
                                 $sub->url(
                                     action('ReportController@getLotReport'),
                                     __('lang_v1.lot_report'),
                                     ['icon' => 'fa fas fa-hourglass-half', 'active' => request()->segment(2) == 'lot-report']
                                 );
                             }

                             if (in_array('stock_adjustment', $enabled_modules)) {
                                 $sub->url(
                                     action('ReportController@getStockAdjustmentReport'),
                                     __('report.stock_adjustment_report'),
                                     ['icon' => 'fa fas fa-sliders-h', 'active' => request()->segment(2) == 'stock-adjustment-report']
                                 );
                             }
                         }

                         if (auth()->user()->can('trending_product_report.view')) {
                             $sub->url(
                                 action('ReportController@getTrendingProducts'),
                                 __('report.trending_products'),
                                 ['icon' => 'fa fas fa-chart-line', 'active' => request()->segment(2) == 'trending-products']
                             );
                         }

                         if (auth()->user()->can('purchase_n_sell_report.view')) {
                             $sub->url(
                                 action('ReportController@itemsReport'),
                                 __('lang_v1.items_report'),
                                 ['icon' => 'fa fas fa-tasks', 'active' => request()->segment(2) == 'items-report']
                             );

                             $sub->url(
                                 action('ReportController@getproductPurchaseReport'),
                                 __('lang_v1.product_purchase_report'),
                                 ['icon' => 'fa fas fa-arrow-circle-down', 'active' => request()->segment(2) == 'product-purchase-report']
                             );

                             $sub->url(
                                 action('ReportController@getproductSellReport'),
                                 __('lang_v1.product_sell_report'),
                                 ['icon' => 'fa fas fa-arrow-circle-up', 'active' => request()->segment(2) == 'product-sell-report']
                             );

                             $sub->url(
                                 action('ReportController@purchasePaymentReport'),
                                 __('lang_v1.purchase_payment_report'),
                                 ['icon' => 'fa fas fa-search-dollar', 'active' => request()->segment(2) == 'purchase-payment-report']
                             );

                             $sub->url(
                                 action('ReportController@sellPaymentReport'),
                                 __('lang_v1.sell_payment_report'),
                                 ['icon' => 'fa fas fa-search-dollar', 'active' => request()->segment(2) == 'sell-payment-report']
                             );
                         }
                         if (in_array('expenses', $enabled_modules) && auth()->user()->can('expense_report.view')) {
                             $sub->url(
                                 action('ReportController@getExpenseReport'),
                                 __('report.expense_report'),
                                 ['icon' => 'fa fas fa-search-minus', 'active' => request()->segment(2) == 'expense-report']
                             );
                         }
                         if (auth()->user()->can('register_report.view')) {
                             $sub->url(
                                 action('ReportController@getRegisterReport'),
                                 __('report.register_report'),
                                 ['icon' => 'fa fas fa-briefcase', 'active' => request()->segment(2) == 'register-report']
                             );
                         }
                         if (auth()->user()->can('sales_representative.view')) {
                             $sub->url(
                                 action('ReportController@getSalesRepresentativeReport'),
                                 __('report.sales_representative'),
                                 ['icon' => 'fa fas fa-user', 'active' => request()->segment(2) == 'sales-representative-report']
                             );
                         }


                         if ($is_admin) {
                             $sub->url(
                                 action('ReportController@activityLog'),
                                 __('lang_v1.activity_log'),
                                 ['icon' => 'fa fas fa-user-secret', 'active' => request()->segment(2) == 'activity-log']
                             );
                         }


                     },
                     ['icon' => 'fa fas fa-chart-bar', 'id' => 'tour_step8']
                 )->order(55);
             }




            //Modules menu 61
            if (auth()->user()->can('manage_modules')) {
                $menu->url(action('Install\ModulesController@index'), __('lang_v1.modules'), ['icon' => 'fa fas fa-plug', 'active' => request()->segment(1) == 'manage-modules'])->order(61);
            }

            //Booking menu 65



            //Notification template menu
            /*if (auth()->user()->can('send_notifications')) {
                $menu->url(action('NotificationTemplateController@index'), __('lang_v1.notification_templates'), ['icon' => 'fa fas fa-envelope', 'active' => request()->segment(1) == 'notification-templates'])->order(80);
            }*/

            //Settings Dropdown
          /*dd(auth()->user()->can('business_settings.access') ,
                auth()->user()->can('barcode_settings.access') ,
                auth()->user()->can('invoice_settings.access') ,
                auth()->user()->can('tax_rate.view') ,
                auth()->user()->can('tax_rate.create') ,
                auth()->user()->can('access_package_subscriptions'),
                auth()->user()->can('access_types_of_service'));*/



         if(isset($package->package_details['tax_rate_module'])) {
             if (auth()->user()->can('tax_rate.view') || auth()->user()->can('tax_rate.create')) {
                 $menu->url(
                     action('TaxRateController@index'),
                     __('tax_rate.tax_rates'),
                     ['icon' => 'fa fas fa-tachometer-alt', 'active' => request()->segment(1) == 'tax-rates']
                 )
                     ->order(80);

             }
         }

            if (auth()->user()->can('business_settings.access') ||
                auth()->user()->can('barcode_settings.access') ||
                auth()->user()->can('invoice_settings.access') ||
                auth()->user()->can('access_package_subscriptions')||
                auth()->user()->can('access_types_of_service')||
                auth()->user()->can('access_printers')||
                auth()->user()->can('business_settings.backup_database')
            ) {

                $menu->dropdown(
                    __('business.settings'),
                    function ($sub) use ($enabled_modules) {
                        if (auth()->user()->can('business_settings.access')) {
                            $sub->url(
                                action('BusinessController@getBusinessSettings'),
                                __('business.business_settings'),
                                ['icon' => 'fa fas fa-cogs', 'active' => request()->segment(1) == 'business', 'id' => "tour_step2"]
                            );

                        }
                        if (auth()->user()->can('invoice_settings.access')) {
                            $sub->url(
                                action('InvoiceSchemeController@index'),
                                __('invoice.invoice_settings'),
                                ['icon' => 'fa fas fa-file', 'active' => in_array(request()->segment(1), ['invoice-schemes', 'invoice-layouts'])]
                            );
                        }
                        if (auth()->user()->can('barcode_settings.access')) {
                            $sub->url(
                                action('BarcodeController@index'),
                                __('barcode.barcode_settings'),
                                ['icon' => 'fa fas fa-barcode', 'active' => request()->segment(1) == 'barcodes']
                            );
                        }
                        if (auth()->user()->can('access_printers')) {
                            $sub->url(
                                action('PrinterController@index'),
                                __('printer.receipt_printers'),
                                ['icon' => 'fa fas fa-share-alt', 'active' => request()->segment(1) == 'printers']
                            );
                        }

                        if (in_array('types_of_service', $enabled_modules) && auth()->user()->can('access_types_of_service')) {
                            $sub->url(
                                action('TypesOfServiceController@index'),
                                __('lang_v1.types_of_service'),
                                ['icon' => 'fa fas fa-user-circle', 'active' => request()->segment(1) == 'types-of-service']
                            );
                        }

                        //Backup menu 60
                        if (auth()->user()->can('business_settings.backup_database')) {
                            $sub->url(
                                action('BackUpController@index'),
                                __('lang_v1.backup'),
                                ['icon' => 'fa fas fa-hdd', 'active' => request()->segment(1) == 'backup']
                            );
                        }
                        if (auth()->user()->can('send_notifications')) {
                            $sub->url(
                                action('NotificationTemplateController@index')
                                , __('lang_v1.notification_templates'),
                                ['icon' => 'fa fas fa-envelope', 'active' => request()->segment(1) == 'notification-templates']
                            );
                        }
                    },
                    ['icon' => 'fa fas fa-cog', 'id' => 'tour_step3']
                )->order(85);
            }
        });

        //Add menus from modules
        $moduleUtil = new ModuleUtil;
        $moduleUtil->getModuleData('modifyAdminMenu');


        return $next($request);
    }
}
