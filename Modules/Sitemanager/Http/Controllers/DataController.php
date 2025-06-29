<?php

namespace Modules\Sitemanager\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return array[]
     */

    public function superadmin_package()
    {
        return [
            [
                'name' => 'sitemanager_module',
                'label' =>  __('sitemanager::lang.sitemanager'),
                'default' => false
            ]
        ];
    }

    public function user_permissions()
    {
        return [
            [
                'value' => 'sitemanager.stocking_module', // which in database
                'label' =>  __('sitemanager::lang.order_manager'), // use lang file in Resurces\lang\..... lang name\lang.php user value in 'creat'=>' sdhsdhsdhsdgs',
                'default' => false
            ],

            [
                'value' => 'sitemanager.stocking_create', // which in database
                'label' =>  __('sitemanager::lang.users_manager'), // use lang file in Resurces\lang\..... lang name\lang.php user value in 'creat'=>' sdhsdhsdhsdgs',
                'default' => false
            ],

            [
                'value' => 'sitemanager.stocking_edit',
                'label' => __('sitemanager::lang.products_manager'),
                'default' => false
            ],


        ];
    }

    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        $is_mfg_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'sitemanager_module');
        if ($is_mfg_enabled){
            if(auth()->user()->can('inventory.stocking_module')){
                Menu::modify('admin-sidebar-menu', function ($menu)  {
                    $menu->dropdown(
                        __('sitemanager::lang.sitemanager'),
                        function ($sub) {
                            $sub->url(
                                action('\Modules\Sitemanager\Http\Controllers\SitemanagerController@index'),
                                __('sitemanager::lang.products_manager'),
                                ['icon' => 'fa fas fa-user', 'active' => request()->segment(1) == 'inventory']
                            );
                            $sub->url(
                                action('\Modules\Sitemanager\Http\Controllers\SitemanagerController@index'),
                                __('sitemanager::lang.users_manager'),
                                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'stocktacking' && request()->segment(2) == 'create']
                            );
                            $sub->url(
                                action('\Modules\Sitemanager\Http\Controllers\SitemanagerController@index'),
                                __('sitemanager::lang.order_manager'),
                                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'stocktacking' && request()->segment(2) == 'create']
                            );

                            $sub->url(
                                action('\Modules\Sitemanager\Http\Controllers\SitemanagerController@media'),
                                __('sitemanager::lang.media_manager'),
                                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'sitemanager' && request()->segment(2) == 'media']
                            );


                            $sub->url(
                                action('\Modules\Sitemanager\Http\Controllers\SitemanagerController@shipping_governorate'),
                                __('sitemanager::lang.shipp_governorate'),
                                ['icon' => 'fa fas fa-plus-circle', 'active' => request()->segment(1) == 'sitemanager' && request()->segment(2) == 'media']
                            );

                        },

                        ['icon' => 'fa fas fa-users-cog', 'style' => 'background-color: #fdfdfd !important;']
                    )->order(200);

                });
            }


        }

    }




    public function index()
    {
        return view('sitemanager::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('sitemanager::create');
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
        return view('sitemanager::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('sitemanager::edit');
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
