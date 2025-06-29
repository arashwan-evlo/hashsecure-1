<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Models\LocationGroup;
use App\Transaction;
use App\TransactionSellLine;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockInoutController extends Controller
{

    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    public function stock_add()
    {
        if (!auth()->user()->can('stockadjustment.create_in')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');



        $business_locations = BusinessLocation::forDropdown($business_id);

        $location_groups=LocationGroup::where('business_id',$business_id)->pluck('name','id');
        $location_groups->prepend(__('lang_v1.all'), '');

        return view('stock_inout.create')
            ->with(compact('business_locations','location_groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('stockadjustment.create_in')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            $input_data = $request->only([ 'location_id', 'transaction_date',
                'adjustment_type', 'additional_notes',
                'total_amount_recovered', 'final_total',
                'ref_no']);
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('StockAdjustmentController@index'));
            }

            $user_id = $request->session()->get('user.id');

            $input_data['type'] = 'stock_adjustment';
            $input_data['business_id'] = $business_id;
            $input_data['created_by'] = $user_id;
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date'], true);
            $input_data['total_amount_recovered'] = $this->productUtil->num_uf($input_data['total_amount_recovered']);

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('stock_adjustment');
            //Generate reference number
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_adjustment', $ref_count);
            }

            $products = $request->input('products');
            $final_total=0;
            if (!empty($products)) {
                $product_data = [];
                foreach ($products as $product) {
                    $adjustment_line = [
                        'product_id' => $product['product_id'],
                        'variation_id' => $product['variation_id'],
                        'quantity' => $this->productUtil->num_uf($product['quantity']),
                        'unit_price' => $this->productUtil->num_uf($product['unit_price']),
                        'sea_number' => $product['sea_number'],

                        'batch_number' => $product['batch_number'],
                        'exp_date' =>$product['exp_date']?$this->productUtil->uf_date($product['exp_date'], false):'',
                        'production_date' =>$product['production_date']?$this->productUtil->uf_date($product['production_date'], false):'',
                        'baleta_number' => $product['baleta_number'],
                        'lpn_number' => $product['lpn_number'],
                    ];
                    if (!empty($product['lot_no_line_id'])) {
                        //Add lot_no_line_id to stock adjustment line
                        $adjustment_line['lot_no_line_id'] = $product['lot_no_line_id'];
                    }
                      if (!empty($product['location_group_id'])) {
                        //Add lot_no_line_id to stock adjustment line
                        $adjustment_line['sub_location_id'] = $product['location_group_id'];
                    }


                    $product_data[] = $adjustment_line;


                    //Decrease available quantity
                    if($request->stock_type==="stock_out"){
                        $this->productUtil->decreaseProductQuantity(
                            $product['product_id'],
                            $product['variation_id'],
                            $input_data['location_id'],
                            $this->productUtil->num_uf($product['quantity'])
                        );
                        $this->productUtil->SublocationdecreaseQuantity(
                            $product['product_id'],
                            $product['variation_id'],
                            $product['location_group_id'],
                            $this->productUtil->num_uf($product['quantity'])
                        );




                    }else{
                        $this->productUtil->increaseProductQuantity(
                            $product['product_id'],
                            $product['variation_id'],
                            $input_data['location_id'],
                            $this->productUtil->num_uf($product['quantity'])
                        );
                        $this->productUtil->SublocationincreaseQuantity(
                            $product['product_id'],
                            $product['variation_id'],
                            $product['location_group_id'],
                            $this->productUtil->num_uf($product['quantity'])
                        );
                    }



                    $final_total=$final_total+$this->productUtil->num_uf($product['unit_price'])*$this->productUtil->num_uf($product['quantity']);

                }



                $input_data['sub_type']='purchase'; // إذن إضافة
                if($request->stock_type==="stock_out"){
                    $input_data['sub_type']='sell'; // إذن صرف
                }
                $input_data['total_before_tax']=$final_total;
                $input_data['final_total']=$final_total;


                $stock_adjustment = Transaction::create($input_data);
                $stock_adjustment->sell_lines()->createMany($product_data);

                //Map Stock adjustment & Purchase.
                $business = ['id' => $business_id,
                    'accounting_method' => $request->session()->get('business.accounting_method'),
                    'location_id' => $input_data['location_id']
                ];

                //$this->transactionUtil->mapPurchaseSell($business, $stock_adjustment->stock_adjustment_lines, 'stock_adjustment');

                $this->transactionUtil->activityLog($stock_adjustment, 'added', null, [], false);
            }

            $output = ['success' => 1,
                'msg' =>'تم إضافة الإذن ينجاح'
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $msg = trans("messages.something_went_wrong");

            if (get_class($e) == \App\Exceptions\PurchaseSellMismatch::class) {
                $msg = $e->getMessage();
            }

            $output = ['success' => 0,
                'msg' => $msg
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getProductRow(Request $request)
    {
        if (request()->ajax()) {
            $row_index = $request->input('row_index');
            $variation_id = $request->input('variation_id');
            $location_id = $request->input('location_id');

            $business_id = $request->session()->get('user.business_id');
            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id, $location_id,false);
            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);

            //Get lot number dropdown if enabled
            $lot_numbers = [];
            if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($variation_id, $business_id, $location_id, true);
                foreach ($lot_number_obj as $lot_number) {
                    $lot_number->qty_formated = $this->productUtil->num_f($lot_number->qty_available);
                    $lot_numbers[] = $lot_number;
                }
            }
            $product->lot_numbers = $lot_numbers;

            $sub_location=LocationGroup::where('business_location_id',$location_id)->pluck('name','id');

            return view('stock_inout.partials.product_table_row')
                ->with(compact('product', 'row_index','sub_location'));
        }
    }

    public function stock_add_index()
    {
        if (!auth()->user()->can('stockadjustment.create_in')) {
            abort(403, 'Unauthorized action.');
        }
        return view('stock_inout.index');

    }


    public function stock_out()
    {
        if (!auth()->user()->can('stockadjustment.create_out')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');



        $business_locations = BusinessLocation::forDropdown($business_id);
        $location_groups=LocationGroup::where('business_id',$business_id)->pluck('name','id');
        $location_groups->prepend(__('lang_v1.all'), '');

        return view('stock_out.create')
            ->with(compact('business_locations','location_groups'));
    }


    public function stock_products(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $stock_data=Transaction::where('transactions.business_id',$business_id)
            ->where('transactions.type','stock_adjustment')
            ->where('transactions.sub_type','purchase')
            ->join('transaction_sell_lines as TSL','TSL.transaction_id','transactions.id')
            ->join('location_groups','location_groups.id','TSL.sub_location_id')
            ->join('products','products.id','TSL.product_id')
            ->select('products.name','transactions.transaction_date','TSL.id','location_groups.name as sublocation'
                     ,'TSL.variation_id'
            ,'TSL.sea_number','batch_number','baleta_number','production_date','exp_date','lpn_number','quantity',
            )->orderby('transactions.id','desc');

        return Datatables::of($stock_data)
            ->addColumn('action', function ($row){
                $html =  '<button  class="btn  btn-primary " onclick="stock_product_row('.$row->variation_id.')" ><i class="glyphicon glyphicon-add"></i>' .__("messages.add").'</button>';

                return $html;
                  }
            )
            ->editColumn('production_date','@if(!empty($production_date)) {{@shorts_date($production_date)}} @endif')
            ->editColumn('exp_date','@if(!empty($exp_date)) {{@shorts_date($exp_date)}} @endif')
            ->editColumn('quantity','{{@format_quantity($quantity)}}')
            ->editColumn('baleta_number','{{@format_quantity($baleta_number)}}')




            ->removeColumn('id')
            ->rawColumns(['action'])
            ->make(true);

    }

}
