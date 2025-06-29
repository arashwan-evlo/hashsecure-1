<?php

namespace Modules\ChartOfAccounts\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Utils\ModuleUtil;
use Menu;

class DataController extends Controller
{

    public function superadmin_package()
    {
        return [
            [
                'name' => 'chartofaccounts_module',
                'label' => "شجرة الحسابات",
                'default' => false
            ]
        ];
    }
    public function modifyAdminMenu()
    {

        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();

        $is_mfg_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'chartofaccounts_module');

     if ($is_mfg_enabled){

                 Menu::modify('admin-sidebar-menu', function ($menu) {
                     if(auth()->user()->can('ChartofAccounts.view')) {
                         $menu->dropdown(
                             __('chartofaccounts::lang.chartofaccounts'),
                             function ($sub) {
                                 $sub->url(
                                     action('AccountController@index'),
                                     __('account.list_accounts'),
                                     ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'account-11' && request()->segment(2) == 'account']
                                 );

                                 $sub->url(
                                     action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@chart_view'),
                                     __('chartofaccounts::lang.chart_view'),
                                     ['icon' => 'fa fas fa-address-book', 'active' => request()->segment(2) == 'chart_view']
                                 );


                                 $sub->url(
                                     action('CostCenterController@index'),
                                     __('account.cost_centers'),
                                     ['icon' => 'fa fas fa-user', 'active' => request()->segment(2) == 'costcenter' && request()->segment(1) == 'account']
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
                                     action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@index'),
                                     __('chartofaccounts::lang.journals'),
                                     ['icon' => 'fa fas fa-user', 'active' => request()->segment(2) == 'assets']
                                 );


                                 $sub->url(
                                     action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@index'),
                                     __('chartofaccounts::lang.assets'),
                                     ['icon' => 'fa fas fa-user', 'active' => request()->segment(2) == 'assets']
                                 );

                                 $sub->url(
                                     action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@routing'),
                                     __('chartofaccounts::lang.accounts_routing'),
                                     ['icon' => 'fa fas fa-user', 'active' => request()->segment(2) == 'routing']
                                 );

                             },
                             ['icon' => 'fa fas  fa-users']
                         )->order(47);
                     }

                     if(auth()->user()->can('account.access')) {
                 /* New dropdown menu */
                     $menu->dropdown(
                         __('chartofaccounts::lang.financial_movement'),
                         function ($sub){
                             $sub->url(
                                 action('AccountController@index'),
                                 __('chartofaccounts::lang.account_master'),
                                 ['active' => request()->segment(1) == 'account' ]
                             );

                             $sub->url(
                                 action('AccountController@cashFlow'),
                                 __('lang_v1.cash_flow'),
                                 [ 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'cash-flow']
                             );

                             $sub->url(
                                 action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@cash_receipt'),
                                 __('chartofaccounts::lang.cash_receipt'),
                                 ['active' => request()->segment(1) == 'journal' && request()->segment(2) == 'cash_receipt']
                             );


                             $sub->url(
                                 action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@payment_receipt'),
                                 __('chartofaccounts::lang.payment_receipt'),
                                 ['active' =>request()->segment(1) == 'account' && request()->segment(1) == 'account' ]
                             );




                         },




                         ['icon'=>'fa fas fa-exchange-alt']
                     )->order(48);
                     }
              });

        }

    }
    public function user_permissions()
    {
        return [
            [
                'value' => 'ChartofAccounts.view',
                'label' =>  __('chartofaccounts::lang.chartofaccounts'),
                'default' => false
            ],

            [
                'value' => 'journal.create',
                'label' => 'إضافة قيد يومية',
                'default' => false
            ],

            [
                'value' => 'journal.delete',
                'label' => 'حذف قيد يومية',
                'default' => false
            ],

        ];
    }


    public function index()
    {
        return view('chartofaccounts::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('chartofaccounts::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('chartofaccounts::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('chartofaccounts::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
