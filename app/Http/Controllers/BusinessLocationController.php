<?php

namespace App\Http\Controllers;

use App\Account;
use App\BusinessLocation;
use App\InvoiceLayout;
use App\InvoiceScheme;
use App\Models\LocationGroup;
use App\Models\LocatonGroupVariation;
use App\Product;
use App\SellingPriceGroup;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\Util;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class BusinessLocationController extends Controller
{
    protected $moduleUtil;
    protected $commonUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param ModuleUtil $moduleUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, Util $commonUtil,ProductUtil $productUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->productUtil=$productUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $locations = BusinessLocation::where('business_locations.business_id', $business_id)
                ->leftjoin(
                    'invoice_schemes as ic',
                    'business_locations.invoice_scheme_id',
                    '=',
                    'ic.id'
                )
                ->leftjoin(
                    'invoice_layouts as il',
                    'business_locations.invoice_layout_id',
                    '=',
                    'il.id'
                )
                ->leftjoin(
                    'invoice_layouts as sil',
                    'business_locations.sale_invoice_layout_id',
                    '=',
                    'sil.id'
                )
                ->leftjoin(
                    'selling_price_groups as spg',
                    'business_locations.selling_price_group_id',
                    '=',
                    'spg.id'
                )
                ->leftjoin(
                    'location_groups',
                    'business_locations.location_group_id',
                    '=',
                    'location_groups.id'
                )

                ->select([ 'business_locations.id',
                    'business_locations.name',
                    'location_id', 'city','location_groups.name as group_name',
                    'spg.name as price_group', 'ic.name as invoice_scheme',
                    'il.name as invoice_layout', 'sil.name as sale_invoice_layout',
                    'business_locations.is_active']);

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $locations->whereIn('business_locations.id', $permitted_locations);
            }

            return Datatables::of($locations)
                ->addColumn(
                    'action',
                    '<button type="button" data-href="{{action(\'\App\Http\Controllers\BusinessLocationController@edit\', [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".location_edit_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <a href="{{route(\'location.settings\', [$id])}}" class="btn btn-success btn-xs"><i class="fa fa-wrench"></i> @lang("messages.settings")</a>
                    <button type="button" data-href="{{action(\'\App\Http\Controllers\BusinessLocationController@activateDeactivateLocation\', [$id])}}" class="btn btn-xs activate-deactivate-location @if($is_active) btn-danger @else btn-success @endif"><i class="fa fa-power-off"></i> @if($is_active) @lang("lang_v1.deactivate_location") @else @lang("lang_v1.activate_location") @endif </button>
                    @if(!$is_active)
                      
                      @endif
                    '
                )
                ->removeColumn('id')
                ->removeColumn('is_active')
                ->rawColumns([8])
                ->make(false);
        }

        return view('business_location.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not, then check for location quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (!$this->moduleUtil->isQuotaAvailable('locations', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('locations', $business_id);
        }

        $invoice_layouts = InvoiceLayout::where('business_id', $business_id)
                            ->get()
                            ->pluck('name', 'id');

        $invoice_schemes = InvoiceScheme::where('business_id', $business_id)
                            ->get()
                            ->pluck('name', 'id');

        $price_groups = SellingPriceGroup::forDropdown($business_id);

        $payment_types = $this->commonUtil->payment_types(null, false, $business_id);

        //Accounts
        $accounts = [];
        if ($this->commonUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false);
        }

        $location_groups=LocationGroup::where('business_id',$business_id)->pluck('name','id');
        return view('business_location.create')
                    ->with(compact(
                        'invoice_layouts',
                        'invoice_schemes',
                        'price_groups',
                        'payment_types',
                        'accounts','location_groups'
                    ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not, then check for location quota
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            } elseif (!$this->moduleUtil->isQuotaAvailable('locations', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('locations', $business_id);
            }

            $input = $request->only(['name', 'landmark', 'city', 'state', 'country', 'zip_code', 'invoice_scheme_id',
                'invoice_layout_id', 'mobile', 'alternate_number', 'email', 'website', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'location_id', 'selling_price_group_id', 'default_payment_accounts', 'featured_products', 'sale_invoice_layout_id', 'sale_invoice_scheme_id']);

            $input['business_id'] = $business_id;

            $input['default_payment_accounts'] = !empty($input['default_payment_accounts']) ? json_encode($input['default_payment_accounts']) : null;

            //Update reference count
            $ref_count = $this->moduleUtil->setAndGetReferenceCount('business_location');

            if (empty($input['location_id'])) {
                $input['location_id'] = $this->moduleUtil->generateReferenceNumber('business_location', $ref_count);
            }

            $location = BusinessLocation::create($input);

            //Create a new permission related to the created location
            Permission::create(['name' => 'location.' . $location->id ]);

            $output = ['success' => true,
                            'msg' => __("business.business_location_added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StoreFront  $storeFront
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StoreFront  $storeFront
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $location = BusinessLocation::where('business_id', $business_id)
                                    ->find($id);
        $invoice_layouts = InvoiceLayout::where('business_id', $business_id)
                            ->get()
                            ->pluck('name', 'id');
        $invoice_schemes = InvoiceScheme::where('business_id', $business_id)
                            ->get()
                            ->pluck('name', 'id');

        $price_groups = SellingPriceGroup::forDropdown($business_id);

        $payment_types = $this->commonUtil->payment_types(null, false, $business_id);

        //Accounts
        $accounts = [];
        if ($this->commonUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false);
        }
        $featured_products = $location->getFeaturedProducts(true, false);

        $location_groups=LocationGroup::where('business_id',$business_id)->pluck('name','id');

        return view('business_location.edit')
                ->with(compact(
                    'location',
                    'invoice_layouts',
                    'invoice_schemes',
                    'price_groups',
                    'payment_types',
                    'accounts',
                    'featured_products','location_groups'
                ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StoreFront  $storeFront
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'landmark', 'city', 'state', 'country',
                'zip_code', 'invoice_scheme_id',
                'invoice_layout_id', 'mobile', 'alternate_number', 'email', 'website', 'custom_field1',
                'custom_field2', 'custom_field3', 'custom_field4',
                'location_id', 'selling_price_group_id', 'default_payment_accounts',
                'featured_products', 'sale_invoice_layout_id', 'sale_invoice_scheme_id'
                ,'location_group_id'
            ]);


            $business_id = $request->session()->get('user.business_id');

            $input['default_payment_accounts'] = !empty($input['default_payment_accounts']) ? json_encode($input['default_payment_accounts']) : null;

            $input['featured_products'] = !empty($input['featured_products']) ? json_encode($input['featured_products']) : null;

            BusinessLocation::where('business_id', $business_id)
                            ->where('id', $id)
                            ->update($input);

            $output = ['success' => true,
                'msg' => __('business.business_location_updated_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StoreFront  $storeFront
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
    * Checks if the given location id already exist for the current business.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function checkLocationId(Request $request)
    {
        $location_id = $request->input('location_id');

        $valid = 'true';
        if (!empty($location_id)) {
            $business_id = $request->session()->get('user.business_id');
            $hidden_id = $request->input('hidden_id');

            $query = BusinessLocation::where('business_id', $business_id)
                            ->where('location_id', $location_id);
            if (!empty($hidden_id)) {
                $query->where('id', '!=', $hidden_id);
            }
            $count = $query->count();
            if ($count > 0) {
                $valid = 'false';
            }
        }
        echo $valid;
        exit;
    }

    /**
     * Function to activate or deactivate a location.
     *
     * @param int $location_id
     * @return json
     */
    public function activateDeactivateLocation($location_id)
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $business_location = BusinessLocation::where('business_id', $business_id)
                            ->findOrFail($location_id);

            $business_location->is_active = !$business_location->is_active;
            $business_location->save();

            $msg = $business_location->is_active ? __('lang_v1.business_location_activated_successfully') : __('lang_v1.business_location_deactivated_successfully');

            $output = ['success' => true,
                            'msg' => $msg
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }



    public function locationgroups(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

       if($request->ajax()){

           $data=BusinessLocation::where('business_locations.business_id',$business_id)->where('is_active',1)
                 ->join('location_groups','location_groups.business_location_id','business_locations.id')
               ->select('location_groups.id','location_groups.name as name'
                        ,'business_locations.name as location','location_groups.status','location_groups.notes'
                  )->get();

           $html="";
           $status=[0=>'غير نشط',1=>'نشط'];
           foreach ($data as $row){
               $html .='<tr>
                     <td>'.$row-> location.'</td>
                     <td>'.$row->name.'</td>
                     <td>'.$status[$row->status].'</td>
                     <td>'.$row->notes.'</td>
                    
                     <td>';
              if(auth()->user()->can("location_group_edit"))
                  $html .=' <button data-href='.action('BusinessLocationController@addgroup', [$row->id]).' class="btn btn-xs btn-primary btn-modal"   data-container=".location_add_modal"><i class="glyphicon glyphicon-edit"></i>'.__("messages.edit").'</button>';

              if(auth()->user()->can("location_group_edit"))
                  $html .=' <button data-href='.action('BusinessLocationController@group_delete', [$row->id]).' class="btn btn-xs btn-danger delete_button"><i class="glyphicon glyphicon-trash"></i>'.__("messages.delete").'</button>';


               $html .='</td>
                     <tr>';


           }





           $output=['success'=>true,
               'html'=>$html,
               'msg'=>'test'];
           return $output;

       }


        return view('business_location_groups.index');
    }

    public function addgroup($id=null)
    {
        $business_id = request()->session()->get('user.business_id');
        if($id){
            $data=LocationGroup::where('id',$id)->first();
        }else{
            $data=new LocationGroup();
        }

        $locations=BusinessLocation::forDropdown($business_id);

        $html=view('business_location_groups.create',compact(['data','locations']))->render();

        return $html;
    }


    public function store_group(Request $request)
    {
        $input=$request->except('_token');
        $business_id = request()->session()->get('user.business_id');
        $input['business_id']=$business_id;
       $data=LocationGroup::updateorcreate(
           ['id'=>$request->id],
            $input);

       $output=['success'=>true,
           'msg'=>'تم الحفظ بنجاح'];

       return $output;

    }

    public function group_delete($id)
    {
        try{
            DB::beginTransaction();
           $data=LocationGroup::where('id',$id)->first();
           $group_id=$data->id;

           $dd=BusinessLocation::where('location_group_id',$group_id)->update(['location_group_id'=>0]);
            $data->delete();
            $output = ['success' => true,
                'msg' => __("brand.deleted_success")
            ];
            DB::commit();

          } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;

    }


    public function product_locations(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if($request->ajax()){

            $data=BusinessLocation::where('business_locations.business_id',$business_id)->where('is_active',1)
                ->join('location_groups','location_groups.business_location_id','business_locations.id')
                ->join('locaton_group_variations','locaton_group_variations.location_group_id','location_groups.id')
                ->join('variations','variations.id','locaton_group_variations.variation_id')
                ->join('products','variations.product_id','products.id')
                ->select('locaton_group_variations.id','location_groups.name as name','products.name as product_name'
                    ,'business_locations.name as location','location_groups.status','location_groups.notes'
                    ,'locaton_group_variations.quantity'
                )->get();

            $html="";
            $status=[0=>'غير نشط',1=>'نشط'];
            foreach ($data as $row){
                $html .='<tr>
                     <td>'.$row-> location.'</td>
                     <td>'.$row->name.'</td>
                     <td>'.$row->product_name.'</td>
                     <td>'.$row->quantity.'</td>
                    
                     <td>';
                if(auth()->user()->can("location_group_edit"))
                    $html .=' <button data-href='.action('BusinessLocationController@product_location_edit', [$row->id]).' class="btn btn-xs btn-primary btn-modal"   data-container=".location_add_modal"><i class="glyphicon glyphicon-edit"></i>'.__("messages.edit").'</button>';

                if(auth()->user()->can("location_group_edit"))
                    $html .=' <button data-href='.action('BusinessLocationController@group_variation_delete', [$row->id]).' class="btn btn-xs btn-danger delete_button"><i class="glyphicon glyphicon-trash"></i>'.__("messages.delete").'</button>';


                $html .='</td>
                     <tr>';


            }
        $output=['success'=>true,
                'html'=>$html,
                'msg'=>'test'];
            return $output;

    }



        return view('business_location_groups.product_locations');
    }

    public function product_location_add($id=null)
    {
        $business_id = request()->session()->get('user.business_id');
        $products=Product::where('business_id',$business_id)
            ->join('variations as v', 'v.product_id', '=', 'products.id')
            ->pluck('products.name','v.id');

        $locations=BusinessLocation::forDropdown($business_id,false);
        $firstKey = array_key_first($locations->toArray());
        $location_group=LocationGroup::where('business_location_id',$firstKey)->pluck('name','id');

        $location_groups=LocationGroup::where('location_groups.business_id',$business_id)
            ->join('business_locations','business_locations.id','location_groups.business_location_id')
            ->select(
                DB::raw('CONCAT(COALESCE(location_groups.name,"")," : ",COALESCE(business_locations.name, "")) as name'),
                'location_groups.id')
            ->pluck('name','id');




        $html=view('business_location_groups.create_product',compact(['products','locations','location_groups']));

        return $html;

    }


    public function product_location_edit($id=null)
    {
        $business_id = request()->session()->get('user.business_id');
        $products=Product::where('business_id',$business_id)
            ->join('variations as v', 'v.product_id', '=', 'products.id')
            ->pluck('products.name','v.id');

        $locations=BusinessLocation::forDropdown($business_id,false);
        $firstKey = array_key_first($locations->toArray());
        $location_group=LocationGroup::where('business_location_id',$firstKey)->pluck('name','id');

        $location_groups=LocationGroup::where('location_groups.business_id',$business_id)
            ->join('business_locations','business_locations.id','location_groups.business_location_id')
            ->select(
                DB::raw('CONCAT(COALESCE(location_groups.name,"")," : ",COALESCE(business_locations.name, "")) as name'),
                'location_groups.id')
            ->pluck('name','id');

        $data=BusinessLocation::where('business_locations.business_id',$business_id)->where('locaton_group_variations.id',$id)
            ->join('location_groups','location_groups.business_location_id','business_locations.id')
            ->join('locaton_group_variations','locaton_group_variations.location_group_id','location_groups.id')
            ->join('variations','variations.id','locaton_group_variations.variation_id')
            ->join('products','variations.product_id','products.id')
            ->select('locaton_group_variations.id','location_groups.name as name','products.name as product_name'
                ,'business_locations.name as location','location_groups.status','location_groups.notes'
                ,'locaton_group_variations.quantity','locaton_group_variations.variation_id','location_groups.id as group_id'
            )->first();


        $html=view('business_location_groups.create_product',compact(['products','locations','location_groups','data']));

        return $html;

    }


    public function product_location_store(Request $request)
    {

    $input=$request->except('_token');
    $old_quantity=0;
    $variation_id=0;
    try{
        DB::beginTransaction();
    if($request->id>0){
        $data=LocatonGroupVariation::where('id',$request->id)->first();
        $old_quantity=$data->quantity;
        $data->quantity=$request->quantity;
        $data->save();
        $variation_id=$data->variation_id;

         $output=['success'=>true,
            'msg'=>'تم تعديل الرصيد بنجاح'];
    }else{
        $variation_id=$request->variation_id;
        $data=LocatonGroupVariation::updateorcreate(
            ['location_group_id'=>$request->location_group_id,
                'variation_id'=>$request->variation_id ],
            $input);
     $output=['success'=>true,
            'msg'=>'تم إضافة الرصيد بنجاح'];
    }

    $quantity=$request->quantity-$old_quantity;
    $location=LocationGroup::where('id',$data->location_group_id)->first();
    $location_id=$location->business_location_id;

    $product=Variation::where('id',$variation_id)->first();
    $product_id=$product->product_id;
    $this->productUtil->increaseProductQuantity($product_id,$variation_id,$location_id,$quantity);
        DB::commit();

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

        $output = ['success' => false,
            'msg' => __("messages.something_went_wrong")
        ];
    }

    return $output;

    }


    public function group_variation_delete($id)
    {
        try {
            DB::beginTransaction();

            $data = LocatonGroupVariation::where('id', $id)->first();
            $quantity=$data->quantity;
            $variation_id=$data->variation_id;
            $location=LocationGroup::where('id',$data->location_group_id)->first();
            $location_id=$location->business_location_id;
            $product=Variation::where('id',$variation_id)->first();
            $product_id=$product->product_id;
            $this->productUtil->decreaseProductQuantity($product_id,$variation_id,$location_id,$quantity);
            $data->delete();
            DB::commit();
            $output=['success'=>true,
                'msg'=>'تم حذف الرصيد بنجاح'];

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return $output;
    }


}
