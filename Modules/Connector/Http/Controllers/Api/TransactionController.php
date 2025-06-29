<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\BusinessLocation;
use App\Models\OrderStatus;
use App\Product;
use App\Utils\CashRegisterUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    protected $transactionUtil;

    public function __construct(TransactionUtil $transactionUtil )
    {
        $this->transactionUtil=$transactionUtil;
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        //return CommonResource::collection($categories);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;
        try{
        //get location
        $locations=BusinessLocation::where('business_id',$business_id)->first();
         $location_id=$locations['id'];


            $input['tax_rate_id']='';
            $discount=['discount_type'=>'',
                       'discount_amount'=>0
                         ];

            foreach ($request->products as $product){
                $variation_id=$product['id'];

                $variation=Product::join('variations','variations.product_id','products.id')
                                 ->where('variations.id',$variation_id)
                                 ->select('products.id as product_id','variations.id as variation_id'
                                  ,'sell_price_inc_tax'
                                 )->first();

                $input['products'][]=[
                    'product_id'=>$variation['product_id'],
                    'variation_id'=>$variation['variation_id'],
                    'quantity'=>$product['quantity'],
                    'unit_price_inc_tax'=>$product['price'],
                    'item_tax'=>0,
                    'tax_id'=>null,
                    //For sell lines
                    'line_discount_type'=>'fixed',
                    'line_discount_amount'=>0,
                    'unit_price'=>$product['price'],// before discount
                ];
            }

            $productUtil=new ProductUtil();
            $invoice_total = $productUtil->calculateInvoiceTotal($input['products'], $input['tax_rate_id'], $discount);
            $user_id=$user->id;

            $input=[
                "type"=>"sell",
                "location_id" => $location_id,
                "sub_type" => null,
                "pay_term_number" => null,
                "pay_term_type" => null,
                "transaction_date" => \Carbon::now(),
                "price_group" => "0",
                "contact_id" => $request->contact_id,
                "search_product" => null,
                "sell_price_tax" => "includes",
                "status"=>"final",
                "is_suspend"=>1,
                "final_total"=>$invoice_total['final_total'],
                'sale_note'=>$request->sale_note,
                'discount_amount'=>0,
                'commission_agent'=>'',
                'products'=> $input['products']

            ];

            $transaction=$this->transactionUtil->createSellTransaction($business_id,$input,$invoice_total,$user_id, $uf_data = true);
            $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id']);
              //Payment
           $cashRegisterUtil=new CashRegisterUtil();
            $input['payment']=$request->payments;
           $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment']);

            $payment_status = $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            $transaction->payment_status = $payment_status;


            //
         $order=   OrderStatus::create([
                   'business_id'=>$business_id,
                    'transaction_id'=>$transaction->id,
                    'is_active'=>1,
                    'status'=>0,
                    ]);





            $output = ['success' => true,
                'msg' => 'تم إضافة الطلب بنجاح',
                'id'=>$transaction->id,
                'order_number'=>$transaction->invoice_no,
                'order'=>[
                    'id'=>$order->id,
                    'status'=>'pending',
                    'created_at'=>$order->created_at,
                    'updated_at'=>$order->updated_at
                ],
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
        return view('connector::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('connector::edit');
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
