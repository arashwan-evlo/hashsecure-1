<?php

namespace Modules\Installment\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\Business;
use App\Contact;
use App\InvoiceLayout;
use App\Transaction;
use App\TransactionPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use DB;
use Modules\Installment\Entities\installmentdb;
use Modules\Installment\Entities\Installments;
use Yajra\DataTables\DataTables;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;

class InstallmentController extends Controller
{
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $business_id=auth()->user()->business_id;
        $customers=Contact::where('business_id','=',$business_id)->where('type','!=','supplier')->pluck('name','id');
        $customers->prepend(__('lang_v1.select_all'), '0');
     return view('installment::reports.index',['customers'=>$customers,'installment_id'=>$request->id]);
    }

    public function instalments(Request $request){
        $business_id = request()->session()->get('user.business_id');
        $installments =Installments::where('installments.business_id', $business_id)
              ->where(function ($query) use($request){
                  if($request->id>0)
                      $query->where('installments.contact_id','=',$request->id);


                  if($request->installment_id)
                      $query->where('installment_id','=',$request->installment_id);

                if($request->installment_status==1)
                    $query->where('payment_id','>',0);

                if($request->installment_status==2){
                    $query->where('installmentdate','>=',Carbon::now()->addDays(-1));
                    $query->where('payment_id','=',0);
                }



                      if ($request->installment_status == 3) {
                          $query->where('installmentdate', '<', Carbon::now()->addDays(-1));
                          $query->where('payment_id', '=', 0);
                      }


                  if(!$request->installment_id) {
                      if ($request->datefrom && $request->dateto)
                          $query->whereBetween('installmentdate', [$request->datefrom, $request->dateto]);
                  }
            })
            ->join('contacts','installments.contact_id','=','contacts.id')
            ->select([ 'installments.id','installments.payment_id',
                'installments.installment_number',
                'contacts.name','installments.installmentdate','installments.installment_value',
                'installments.benefit_value',
                DB::raw('(installments.installment_value+installments.benefit_value) as total_value'),
                'installments.paid_value',
                'installments.paid_date'
            ])
            ->orderby('installmentdate');

        return DataTables::of($installments)
            ->editcolumn('id',function ($row){
                $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                    __("messages.actions") .
                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                 $html .= '<li><a href="#" class="print-invoice" data-href="'.action('\Modules\Installment\Http\Controllers\InstallmentController@printinstallment', [$row->id]).'"><i class="fas fa-print" aria-hidden="true"></i> '. __("messages.print").' </a></li>';
                 $html .= '<li><a href="#" data-href='.action('\Modules\Installment\Http\Controllers\InstallmentController@addpayment', [$row->id]).'" class="add_payment" ><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> '.__("installment::lang.pebt_Collection").' </a></li>';
               $html .='<li  class="divider" > </li>';
                 $html .= '<li><a href="#" data-href='.action('\Modules\Installment\Http\Controllers\InstallmentController@paymentdelete', [$row->id]).'" class="paymentdelete" ><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> حذف التحصيل </a></li>';
                 $html .= '<li><a href="#" data-href='.action('\Modules\Installment\Http\Controllers\InstallmentController@installmentdelete', $row->id) . '" class="installmentdelete" > <i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") .' </a></li>';

                $html .= '</ul></div>';

                return $html;
            })
             ->editcolumn('paid_status',function ($row) {
                $currentdat=Carbon::now();
                 $daylats=Carbon::parse($row->installmentdate)->diffInDays($currentdat);
                 if(Carbon::parse($row->installmentdate)>$currentdat)
                     $daylats=0;

                 $monthlats=Carbon::parse($row->installmentdate)->diffInMonths($currentdat);
                 $yearlats=Carbon::parse($row->installmentdate)->diffInYears($currentdat);

                 if($row->payment_id>0)
                     $st='<span class="label bg-light-green">محصل</span>';
                if($daylats>0 && $row->payment_id==0)
                    $st='<span class="label  bg-red">متأخر </span>';
                 if($daylats==0 && $row->payment_id==0)
                            $st='<span class="label  bg-yellow"> مستحق الدفع </span>';
                 $total_paied=$row->installment_value+$row->benefit_value-$row->paid_value;
                 if($total_paied>0 && $row->payment_id>0){
                     $st='<span class="label bg-aqua">جزئي </span>';
                 }


                return $st;
            })
              ->addColumn(
                'latdays',function ($row){
                        return $row->paid_date;
            }
            )



            ->removeColumn('payment_id')
            ->rawColumns(['id','latdays','paid_status',9])
            ->make(true);
    }


    /*
     * show payment.blade.php
     * */
    public function addpayment(Request $request){

       $data=Installments::where('id','=',$request->id)->first();
        $currentdat=Carbon::now();
        $daylats=0;
        $latfines_value=0;

        $contact=Contact::where('id','=',$data->contact_id)->first();

        if(Carbon::parse($data->installmentdate)<$currentdat){
           $daylats=Carbon::parse($data->installmentdate)->diffInDays($currentdat);
           $monthlats=Carbon::parse($data->installmentdate)->diffInMonths($currentdat);
           $yearlats=Carbon::parse($data->installmentdate)->diffInYears($currentdat);
            $installment_value=$data->installment_value;
            $latfines=$data->latfines;
            $latfinestype=$data->latfinestype;
            if($latfinestype=='day'){
                $latfines_value= $installment_value*$latfines*$daylats/100;
                $daylats= $daylats;
            }

            if($latfinestype=='month'){
                $latfines_value= $installment_value*$latfines* $monthlats/100;
                $daylats= $monthlats;
            }

            if($latfinestype=='year'){
                $latfines_value= $installment_value*$latfines*$yearlats/100;
                $daylats= $yearlats;
            }
        }

        $business_id=auth()->user()->business_id;
        $accounts =Account::where('business_id', $business_id)->pluck('name','id');

    /*   $accounts->prepend(__('lang_v1.select_all'), '0');*/


        return view('installment::customer.payment',['data'=>$data,'daylats'=>$daylats,'latfines_value'=>$latfines_value,'accounts'=>$accounts,'contact'=>$contact]);
    }

    public function storepayment(Request $request){
       $business_id=auth()->user()->business_id;
    // get transaction_id
    try{
     DB::beginTransaction();
       $transaction_id=Installments::where('id',$request->installment_id)->first();
       $amount=$request->totallpaid;
       $total=$request->totallreq;
       if($amount>$total){
           $output = ['success' => 0,
               'msg' =>'عفوا لايمكن تجاوز قيمة القسط'
           ];
           return $output;
       }

       $total_paid=$request->totallpaid+$transaction_id->paid_value;
       $payment=TransactionPayment::updateorcreate(
           ['id'=>$transaction_id->payment_id],
           [
                'business_id'=>$business_id,
                'transaction_id'=>$transaction_id->transaction_id,
                'is_return'=>0,
                'is_advance'=>0,
                'payment_for'=>$request->contact_id,
                'amount'=>$total_paid,
                'paid_on'=>$request->installmentdate,
                'method'=>'cash',
                'account_id'=>$request->account_id,
                'created_by'=>auth()->user()->id,
                'note'=>'قيمة قسط بدون الفوائد'
            ]);



        // update installment with payment_id
        $insatllment=Installments::findorfail($request->installment_id);
        $insatllment->payment_id=$payment->id;
        $insatllment->paid_date=$request->installmentdate;
        $insatllment->latfines_value=$request->latfines;
        $insatllment->paid_value=$total_paid;
        $insatllment->save();
        /*Todo : change Transaction status after check payment 15-8-2022*/
       $this->updateinstallmentdb($transaction_id->installment_id);
       $this->updatepayment_status($transaction_id->transaction_id);
      // add
        $user_id = request()->user()->id;
        $data=[
            'amount' => $payment->amount,
            'account_id' => $request->account_id,
            'type' =>'credit',
            'sub_type' => null,
            'operation_date' =>$request->installmentdate,
            'created_by' =>$user_id,
            'transaction_id' =>$transaction_id->transaction_id,
            'transaction_payment_id' => $payment->id,
            'note'=>'قيمة قسط ',
            'transfer_transaction_id' => null,
        ];


        AccountTransaction::createAccountTransaction($data);




       DB::commit();
    } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
              $output = ['success' => 0,
                          'msg' => __("messages.something_went_wrong")
                     ];
               return $output;
             }
       $result=[
            'success'=>true,
            'msg'=>'تم تسجيل بنجاح'
        ];
        return $result;
    }
    public function installmentdelete(Request $request){

        if (!auth()->user()->can('installment.delete')) {
            $output = [ 'success' => false,
                'msg' =>'عفوالا تملك الصلاحية لحذف قسط'
            ];
            return $output;
        }

        try {
            DB::beginTransaction();
            $data = Installments::where('id', '=', $request->id)->first();
            $installment_id = $data->installment_id;
            TransactionPayment::where('id', '=', $data->payment_id)->delete();
            Installments::where('id', '=', $request->id)->delete();
            $data->delete();

            $transaction_id = $data->transaction_id;
            $this->updatepayment_status($transaction_id);

            $this->updateinstallmentdb($installment_id);
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('installment:lang.deleted_success')
            ];
            return $output;



    } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => 0,
                'msg' => __("messages.something_went_wrong")
                ];
             return $output;
            }




    }

    public function paymentdelete(Request $request){

        if (!auth()->user()->can('installment.delete_Collection')) {
            $output = [ 'success' => false,
                'msg' =>'عفوالا تملك الصلاحية لحذف قسط'
            ];
            return $output;
        }
        $data=Installments::where('id','=',$request->id)->first();
        TransactionPayment::where('id','=',$data->payment_id)->delete();
        AccountTransaction::where('transaction_payment_id',$data->payment_id)->delete();

        $data->payment_id=0;
        $data->paid_value=0;
        $data->paid_date='NULL';
        $data->save();


        $this->updateinstallmentdb($data->installment_id);

      $transaction_id=$data->transaction_id;
      $this->updatepayment_status($transaction_id);
        $result=[
            'success'=>true,
            'msg'=>__('installment:lang.deleted_success')
        ];
        return $result;



    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function system(){

       return view('installment::systems.index');
    }

    public function system_create(){
        return view('installment::systems.create');
    }


    public function updateinstallmentdb($id){
   $paidnumber=Installments::where('installment_id','=',$id)->where('payment_id','>',0)->count();
   $total_number=Installments::where('installment_id','=',$id)->count();

        // installment
        $installmentdb=installmentdb::findorfail($id);
        $installmentdb->paidnumber=$paidnumber;
        $installmentdb->number=$total_number;
        $installmentdb->save();

        }


   public function printinstallment($id){

        $receipt_details=Installments::findorfail($id);
        $client=Contact::find( $receipt_details->contact_id);

        $business_id=auth()->user()->business_id;
        $business=Business::find($business_id);
        $logo = InvoiceLayout::find($business_id);
       $receipt_details['logo'] = !empty( $logo->logo) && file_exists(public_path('uploads/invoice_logos/' .  $logo->logo)) ? asset('uploads/invoice_logos/' .  $logo->logo) : false;

       $receipt_details['total']= $receipt_details->installment_value + $receipt_details->benefit_value + $receipt_details->latfines_value;
       $receipt_details['business_name'] =$business->name;

        $installments=Installments::where('installment_id','=',$receipt_details->installment_id)->get();


        $html=view( 'installment::print.installment', compact(['receipt_details','client','installments']))->render();

        return $html;
   }

   public function business(){
        $data=Business::select('business.id','business.name',DB::raw('count(products.id) as total'))->join('products','business.id','=','products.business_id')->groupby('business.id')->get();
        return $data;
     }

public function updatepayment_status($transaction_id){
    $total_paid = TransactionPayment::where('transaction_id', $transaction_id)
        ->select(DB::raw('SUM(IF( is_return = 0, amount, amount*-1))as total_paid'))
        ->first()
        ->total_paid;
      $transaction = Transaction::where('id',$transaction_id)->first();
      $installment=Installments::where('transaction_id',$transaction_id)
                                 ->where('payment_id',0)->count();

      $final_amount = $transaction->final_total;
    $status = 'installmented';
//dd($final_amount,$total_paid,$installment);
    if ($final_amount > $total_paid && $installment == 0 )
              $status = 'partial';

    if ($final_amount <= $total_paid && $installment == 0 )
                 $status = 'paid';

        $transaction->payment_status=$status;
        $transaction->save();
    return ;

}

public function test($input){


}
}
