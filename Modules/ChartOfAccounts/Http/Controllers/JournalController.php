<?php

namespace Modules\ChartOfAccounts\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\Contact;
use App\Media;
use App\Models\AccountRouting;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use Yajra\DataTables\Facades\DataTables;

class JournalController extends Controller
{

    protected $commonUtil;
    protected $productUtil;
    protected $transactionUtil;
    public function __construct(Util $commonUtil,ProductUtil $productUtil,TransactionUtil $transactionUtil)
    {
        $this->commonUtil=$commonUtil;
        $this->productUtil=$productUtil;
        $this->transactionUtil=$transactionUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {



        return view('chartofaccounts::journal.index');
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

    public function cash_receipt()
    {
        return view('chartofaccounts::journal.cash_receipt.index');
    }

    public function cash_receipt_add(Request $request)
    {

        $business_id = request()->session()->get('user.business_id');

        $account=new Account();

        // الحسابات الفرعية
        $accounts=Account::where('accounts.business_id',$business_id)
                    ->join('account_types','account_types.id','accounts.account_type_id')
                    ->whereIn('account_types.id',[3,4,5])-> pluck('accounts.name','accounts.id');

        $customers=Contact::where('contacts.business_id',$business_id)
            ->whereIn('type',['customer','both'])
             -> pluck('contacts.name','id');

        $suppliers=Contact::where('contacts.business_id',$business_id)
            ->whereIn('type',['supplier','both'])
            -> pluck('contacts.name','id');


        //حسابات الخزن
        $save_account=Account::where('business_id',$business_id)
                   ->where('account_type_id',6)->pluck('name','id');

        $currentdate=$this->commonUtil->format_date(now(),false);

        $account_type='sub_account';
        $account_id=0;
        if(!empty($request->id) && $request->type==='sell'){
            //this is a customer
            $account_type='customer';
            $account_id=$request->id;

        }
        if(!empty($request->id) && $request->type==='purchase'){
            //this is a supplier
            $account_type='supplier';
            $account_id=$request->id;
        }

        return view('chartofaccounts::journal.cash_receipt.create',compact(['accounts','account',
            'save_account','currentdate'
          ,'customers','suppliers','account_type','account_id'
        ]));
    }

    public function cash_receipt_edit($transaction_id)
    {

        $business_id = request()->session()->get('user.business_id');

        $transaction=Transaction::where('id',$transaction_id)->first();
        $account_code=$transaction->ref_no;

        $debit_account=AccountTransaction::where('type','debit')->where('transaction_id',$transaction_id)
            ->join('accounts','accounts.id','account_transactions.account_id')
            ->first();

        $contact_name='';
        if(!empty($debit_account->contact_id)){
            $contact=Contact::where('id',$debit_account->contact_id)->first();
            $contact_name=$contact->name;
        }



        $credit_account=AccountTransaction::where('type','credit')->where('transaction_id',$transaction_id)
            ->join('accounts','accounts.id','account_transactions.account_id')
            ->first();




        //حسابات الخزن
        $save_account=Account::where('business_id',$business_id)
            ->where('account_type_id',6)->pluck('name','id');

        $currentdate=$this->commonUtil->format_date($credit_account->operation_date,false);





        return view('chartofaccounts::journal.cash_receipt.edit',compact(['account_code',
            'debit_account','credit_account','contact_name','currentdate'
        ]));
    }
    public function get_cash_receipt(Request $request){

        $business_id = request()->session()->get('user.business_id');
        $transaction=Transaction::where('business_id', $business_id)
            ->where('transactions.type','journal_payment')
            ->where('transactions.sub_type','journal')
            ->join('transaction_types','transaction_types.type','transactions.type')
            ->select('transactions.id as id','transactions.additional_notes','transaction_date'
            ,'transaction_types.description'

            )
            ->orderBy('transaction_date', 'desc')->get();

        $html="";
        $row_no=1;
        foreach ($transaction as $row){
            $html .="<tr>";
            $html .="<td> ".$row->id ."</td>";
            $html .="<td>";
            $html .='<div class="btn-group" style="width:100%">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs"  style="width:100%"
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                         $html .= '<li><a href="' . action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@cash_receipt_add', [$row->id]) . '"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                         $html .= '<li><a href="' . action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@cash_receipt_edit', [$row->id]) . '" class=""><i class="glyphicon glyphicon-edit"></i>' .  __("messages.edit") . '</a></li>';
                         $html .= '<li><a href="' . action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@cash_receipt_delete', [$row->id]) . '" class="btn-modal-delete"><i class="glyphicon glyphicon-trash"></i>' . __("messages.delete") . '</a></li>';



                    $html .= '</ul></div>';



            $html .="</td>";
            $html .="<td> ".$this->commonUtil->format_date($row->transaction_date, false) ."</td>";
            $html .="<td>".$row->description."</td>";

           $account=AccountTransaction::where('transaction_id',$row->id)
                ->join('accounts','accounts.id','account_transactions.account_id')
                ->leftjoin('contacts','contacts.id','account_transactions.contact_id')
               ->select('accounts.name as account_name','account_transactions.type as type'
                        ,'amount','contacts.name as contact_name'


               )


               ->get();

           $loop=0;

         foreach ($account as $record){
               if($loop>0)
                   $html .="<tr>
                   <td></td>
                   <td class='account-link'> <i class='fa fa-angle-left' ></i>   </td>
                   <td colspan='2'>".$row->additional_notes."</td>";

               $conatact_name="";
               if(!empty($record->contact_name)){
                   $conatact_name=" ( ".$record->contact_name." ) ";
               }
               $html .="<td> ".$record->account_name.$conatact_name."</td>";
               if($record->type=='debit'){
                   $html .="<td> ".$this->commonUtil->num_f($record->amount, true) ."</td>";
                   $html .="<td> </td>";
               }else{
                   $html .="<td> </td>";
                   $html .="<td> ".$this->commonUtil->num_f($record->amount, true) ."</td>";

               }
             if($loop>0){
                   $html .="</tr>";
               }


               $loop +=1;
           }



            $row_no +=1;
        }

    return $html;


    }


    public function cash_receipt_save(Request $request)
    {
        $transaction_date= $this->commonUtil->uf_date($request->input('transaction_date').date("h:i:sa"), true);
        $business_id = request()->session()->get('user.business_id');
        $ref_count=$this->commonUtil->setAndGetReferenceCount('journal');
        $ref_no=$request->input('account_code');
        if (empty($ref_no)) {
            $ref_no = $this->productUtil->generateReferenceNumber('journal', $ref_count);
        }

        $transaction_data=[
            'business_id'=>$business_id,
            'type'=>'journal_payment',
            'sub_type'=>'journal',
            'status'=>'final',
            'transaction_date'=> $transaction_date,
            'created_by'=>auth()->user()->id,
            'additional_notes'=>$request->input('additional_notes'),
            'total_before_tax'=>$request->amount,
            'final_total'=>$request->amount,
            'ref_no'=>$ref_no,

        ];

        $contact_id=0;
        $account_id=0;
        $due_payment_type='';
        if($request->account_type==="sub_account"){
            $account_id=$request->sub_account;
            $contact_id=0;
        }

        if($request->account_type==="customer"){
            $contact_id=$request->customer;
            $due_payment_type='sell';
            $data=Contact::where('id',$contact_id)->first();
            if(!empty($data))
               $account_id=$data->account_id;
        }

        if($request->account_type==="supplier"){
            $contact_id=$request->supplier;
            $due_payment_type='purchase';
            $data=Contact::where('id',$contact_id)->first();
            if(!empty($data))
                $account_id=$data->account_id;
        }


        try {
            DB::beginTransaction();

            $transaction = Transaction::create($transaction_data);


            // from account
            $debit_data = [
                'business_id' => $business_id,
                'amount' => $request->amount,
                'account_id' => $account_id,
                'contact_id' => $contact_id,
                'type' => 'debit',
                'sub_type' => 'fund_transfer',
                'created_by' => session()->get('user.id'),
                'note' => '',
                'transaction_id' => $transaction->id,
                'operation_date' => $transaction_date,
            ];


            $debit = AccountTransaction::createAccountTransaction($debit_data);


            // add payment as account data


            // get payment_ref_no for payment
            $prefix_type = 'sell_payment';
            $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type, $business_id);
                    //Generate reference number
             $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count, $business_id);


              $payment_data=[
                  'transaction_id' => $transaction->id,
                  'business_id' => $business_id,
                  'amount' => $request->amount,
                  'account_id' => $account_id,
                  'payment_for' => $contact_id,
                  'payment_type' => 'credit',
                  'is_advance'=>1,
                  'created_by' => session()->get('user.id'),
                  'note' => 'journal',
                  'paid_on' => $transaction_date,
                  'type'=>1,
                  'is_return'=>0,
                  'card_type'=>'credit',
                  'payment_ref_no'=>$payment_ref_no,
                  'is_journal'=>$debit->id,
              ];

            $paymet= TransactionPayment::create($payment_data);

            $debit->transaction_payment_id=$paymet->id;
             $debit->save();
            // To account
            $credit_data = [
                'business_id' => $business_id,
                'amount' => $request->amount,
                'account_id' => $request->credit_account,
                'type' => 'credit',
                'sub_type' => 'fund_transfer',
                'created_by' => session()->get('user.id'),
                'note' => '',
                'transaction_id' => $transaction->id,
                'transfer_transaction_id' => $debit->id,
                'operation_date' => $transaction_date,
            ];

            $credit = AccountTransaction::createAccountTransaction($credit_data);
            $debit->transfer_transaction_id = $credit->id;
            $debit->save();


           /* //
            if(!empty($due_payment_type)){
                $data=[
                    'contact_id' => $contact_id,
                    'amount'=>$request->amount,
                    'method'=>'cash',
                    'note'=>''
                ];

                $request = new Request($data);
                $this->transactionUtil->payContact($request);
            }*/





            Media::uploadMedia($business_id, $transaction, $request, 'document');

            DB::commit();
            $output = ['success' => true,
                'msg' => 'تم حفظ السند بنجاح'];
        }catch (Exception $e){

            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false,
                'msg'=>'عفوا لقد حدث شئ خاطئ'
            ];
        }
        return $output;
    }
    public function cash_receipt_update(Request $request)
    {
      $transaction_date= $this->commonUtil->uf_date($request->input('transaction_date').date("h:i:sa"), true);
        $business_id = request()->session()->get('user.business_id');
        $transaction_id=$request->transaction_id;


        $amount_new= $this->commonUtil->number_un_format($request->amount);

        $transaction_data=[
            'business_id'=>$business_id,
            'type'=>'journal_payment',
            'status'=>'final',
            'transaction_date'=> $transaction_date,
            'created_by'=>auth()->user()->id,
            'additional_notes'=>$request->input('additional_notes'),
            'total_before_tax'=>$amount_new,
            'final_total'=>$amount_new,
            'ref_no'=>$request->input('account_code'),

        ];
        $contact_id=0;
        $account_id=0;

        try {
            DB::beginTransaction();

            $transaction = Transaction::updateOrCreate(
                ['id'=>$transaction_id],
                $transaction_data);
             // from account
            $journal=AccountTransaction::where('transaction_id',$transaction_id)
                       ->where('business_id',$business_id)->get();

            foreach ($journal as $row){
                $amount_old=$row->amount;
                $row->amount=$amount_new;
                $row->operation_date=$transaction_date;
                $row->save();
                AccountTransaction::updateparentpalance($row);

            }


            Media::uploadMedia($business_id, $transaction, $request, 'document');

            DB::commit();
            $output = ['success' => true,
                'msg' => 'تم حفظ السند بنجاح'];
        }catch (Exception $e){

            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false,
                'msg'=>'عفوا لقد حدث شئ خاطئ'
            ];
        }
        return $output;
    }
    public function cash_receipt_delete($id)
    {


        $transaction= Transaction::where('id',$id)->first();
        DB::beginTransaction();
        $transaction->delete();
        AccountTransaction::deleteAccountTransaction($id);
        TransactionPayment::where('transaction_id',$id)->delete();
        DB::commit();
        $output=['success'=>true,
            'msg'=>'تم حذف القيد بنجاح'];
        return $output;
    }




    public function payment_receipt()
    {
        return view('chartofaccounts::journal.payment_receipt.index');
    }
    public function get_payment_receipt(Request $request){

        $business_id = request()->session()->get('user.business_id');
        $transaction=Transaction::where('business_id', $business_id)
            ->where('transactions.type','purchase_payment')
            ->join('transaction_types','transaction_types.type','transactions.type')
            ->select('transactions.id as id','transactions.additional_notes','transaction_date'
                ,'transaction_types.description'

            )
            ->orderBy('transaction_date', 'desc')->get();

        $html="";
        $row_no=1;
        foreach ($transaction as $row){
            $html .="<tr>";
            $html .="<td> ".$row->id ."</td>";
            $html .="<td>";
            $html .='<div class="btn-group" style="width:100%">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs"  style="width:100%"
                        data-toggle="dropdown" aria-expanded="false">' .
                __("messages.actions") .
                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
            $html .= '<li><a href="' . action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@payment_receipt_edit', [$row->id]) . '"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
            $html .= '<li><a href="' . action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@payment_receipt_edit', [$row->id]) . '" class=""><i class="glyphicon glyphicon-edit"></i>' .  __("messages.edit") . '</a></li>';
            $html .= '<li><a href="' . action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@cash_receipt_delete', [$row->id]) . '" class="btn-modal-delete"><i class="glyphicon glyphicon-trash"></i>' . __("messages.delete") . '</a></li>';



            $html .= '</ul></div>';



            $html .="</td>";
            $html .="<td> ".$this->commonUtil->format_date($row->transaction_date, false) ."</td>";
            $html .="<td>".$row->description."</td>";

            $account=AccountTransaction::where('transaction_id',$row->id)
                ->join('accounts','accounts.id','account_transactions.account_id')
                ->leftjoin('contacts','contacts.id','account_transactions.contact_id')
                ->select('accounts.name as account_name','account_transactions.type as type'
                    ,'amount','contacts.name as contact_name'


                )


                ->get();

            $loop=0;

            foreach ($account as $record){
                if($loop>0)
                    $html .="<tr>
                   <td></td>
                   <td class='account-link'> <i class='fa fa-angle-left' ></i>   </td>
                   <td colspan='2'>".$row->additional_notes."</td>";

                $conatact_name="";
                if(!empty($record->contact_name)){
                    $conatact_name=" ( ".$record->contact_name." ) ";
                }
                $html .="<td> ".$record->account_name.$conatact_name."</td>";
                if($record->type=='debit'){
                    $html .="<td> ".$this->commonUtil->num_f($record->amount, true) ."</td>";
                    $html .="<td> </td>";
                }else{
                    $html .="<td> </td>";
                    $html .="<td> ".$this->commonUtil->num_f($record->amount, true) ."</td>";

                }
                if($loop>0){
                    $html .="</tr>";
                }


                $loop +=1;
            }



            $row_no +=1;
        }

        return $html;


    }
    public function payment_receipt_add(Request $request)
    {


        $business_id = request()->session()->get('user.business_id');
        $account=new Account();
        // الحسابات الفرعية
        $accounts=Account::where('accounts.business_id',$business_id)
            ->join('account_types','account_types.id','accounts.account_type_id')
            ->whereIn('account_types.id',[3,4,5])-> pluck('accounts.name','accounts.id');

        $customers=Contact::where('contacts.business_id',$business_id)
            ->whereIn('type',['customer','both'])
            -> pluck('contacts.name','id');

        $suppliers=Contact::where('contacts.business_id',$business_id)
            ->whereIn('type',['supplier','both'])
            -> pluck('contacts.name','id');


        //حسابات الخزن
        $save_account=Account::where('business_id',$business_id)
            ->where('account_type_id',6)->pluck('name','id');

        $currentdate=$this->commonUtil->format_date(now(),false);

        $account_type='sub_account';
        $account_id=0;
        if(!empty($request->id) && $request->type==='sell'){
            //this is a customer
            $account_type='customer';
            $account_id=$request->id;

        }
        if(!empty($request->id) && $request->type==='purchase'){
            //this is a supplier
            $account_type='supplier';
            $account_id=$request->id;
        }

        return view('chartofaccounts::journal.payment_receipt.create',compact(['accounts','account',
            'save_account','currentdate'
            ,'customers','suppliers','account_type','account_id'
        ]));
    }
    public function payment_receipt_save(Request $request)
    {

        $transaction_date= $this->commonUtil->uf_date($request->input('transaction_date').date("h:i:sa"), true);
        $business_id = request()->session()->get('user.business_id');

        $ref_count=$this->commonUtil->setAndGetReferenceCount('journal');
        $ref_no=$request->input('account_code');
        if (empty($ref_no)) {
            $ref_no = $this->productUtil->generateReferenceNumber('journal', $ref_count);
        }


        $transaction_data=[
            'business_id'=>$business_id,
            'type'=>'purchase_payment',
            'sub_type'=>'journal',
            'status'=>'final',
            'total_before_tax'=>$request->amount,
            'final_total'=>$request->amount,
            'transaction_date'=> $transaction_date,
            'created_by'=>auth()->user()->id,
            'additional_notes'=>$request->input('additional_notes'),
            'ref_no'=>$ref_no,
        ];

        $contact_id=0;
        $account_id=0;
        $due_payment_type='';


        if($request->account_type==="sub_account"){
            $account_id=$request->sub_account;
            $contact_id=0;
        }

        if($request->account_type==="customer"){
            $contact_id=$request->customer;
            $due_payment_type='sell';
            $data=Contact::where('id',$contact_id)->first();
            if(!empty($data))
                $account_id=$data->account_id;
        }

        if($request->account_type==="supplier"){
            $contact_id=$request->supplier;
            $due_payment_type='purchase';
            $data=Contact::where('id',$contact_id)->first();
            if(!empty($data))
                $account_id=$data->account_id;
        }


        try {
            DB::beginTransaction();

            $transaction = Transaction::create($transaction_data);


            // from account
            $debit_data = [
                'business_id' => $business_id,
                'amount' => $request->amount,
                'account_id' => $request->debit_account,
                 'type' => 'debit',
                 'sub_type' => 'fund_transfer',
                 'created_by' => session()->get('user.id'),
                 'note' => '',
                 'transaction_id' => $transaction->id,
                 'operation_date' => $transaction_date,
            ];


            $debit = AccountTransaction::createAccountTransaction($debit_data);


            // add payment as account data


            // get payment_ref_no for payment
            $prefix_type = 'sell_payment';
            $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type, $business_id);
            //Generate reference number
            $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count, $business_id);


            $payment_data=[
                'transaction_id' => $transaction->id,
                'business_id' => $business_id,
                'amount' => $request->amount,
                'account_id' => $account_id,
                'payment_for' => $contact_id,
                'payment_type' => 'debit',
                'is_advance'=>1,
                'created_by' => session()->get('user.id'),
                'note' => 'journal',
                'paid_on' => $transaction_date,
                'type'=>1,
                'is_return'=>0,
                'card_type'=>'credit',
                'payment_ref_no'=>$payment_ref_no,
                'is_journal'=>$debit->id,
            ];

            $paymet= TransactionPayment::create($payment_data);

            $debit->transaction_payment_id=$paymet->id;
            $debit->save();
            // To account
            $credit_data = [
                'business_id' => $business_id,
                'amount' => $request->amount,
                'account_id' =>$account_id,
                'type' => 'credit',
                'contact_id'=>$contact_id,
                'sub_type' => 'fund_transfer',
                'created_by' => session()->get('user.id'),
                'note' => '',
                'transaction_id' => $transaction->id,
                'transfer_transaction_id' => $debit->id,
                'operation_date' => $transaction_date,
            ];

            $credit = AccountTransaction::createAccountTransaction($credit_data);
            $debit->transfer_transaction_id = $credit->id;
            $debit->save();


            /* //
             if(!empty($due_payment_type)){
                 $data=[
                     'contact_id' => $contact_id,
                     'amount'=>$request->amount,
                     'method'=>'cash',
                     'note'=>''
                 ];

                 $request = new Request($data);
                 $this->transactionUtil->payContact($request);
             }*/





            Media::uploadMedia($business_id, $transaction, $request, 'document');

            DB::commit();
            $output = ['success' => true,
                'msg' => 'تم حفظ السند بنجاح'];
        }catch (Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false,
                'msg'=>'عفوا لقد حدث شئ خاطئ'
            ];
        }
        return $output;
    }
    public function payment_receipt_delete($id)
    {
        $transaction= Transaction::where('id',$id)->first();
        DB::beginTransaction();
        $transaction->delete();
        AccountTransaction::deleteAccountTransaction($id);
        TransactionPayment::where('transaction_id',$id)->delete();
        DB::commit();
        $output=['success'=>true,
            'msg'=>'تم حذف القيد بنجاح'];
        return $output;
    }


    public function payment_receipt_update(Request $request)
    {
        $transaction_date= $this->commonUtil->uf_date($request->input('transaction_date').date("h:i:sa"), true);
        $business_id = request()->session()->get('user.business_id');
        $transaction_id=$request->transaction_id;


        $amount_new= $this->commonUtil->number_un_format($request->amount);

        $transaction_data=[
            'transaction_date'=> $transaction_date,
            'created_by'=>auth()->user()->id,
            'additional_notes'=>$request->input('additional_notes'),
            'total_before_tax'=>$amount_new,
            'final_total'=>$amount_new,
            'ref_no'=>$request->input('account_code'),

        ];
        $contact_id=0;
        $account_id=0;

        try {
            DB::beginTransaction();

            $transaction = Transaction::updateOrCreate(
                ['id'=>$transaction_id],
                $transaction_data);
            // from account
            $journal=AccountTransaction::where('transaction_id',$transaction_id)
                ->where('business_id',$business_id)->get();

            foreach ($journal as $row){
                $amount_old=$row->amount;
                $row->amount=$amount_new;
                $row->operation_date=$transaction_date;
                $row->save();
                AccountTransaction::updateparentpalance($row);

            }


            Media::uploadMedia($business_id, $transaction, $request, 'document');

            DB::commit();
            $output = ['success' => true,
                'msg' => 'تم حفظ السند بنجاح'];
        }catch (Exception $e){

            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false,
                'msg'=>'عفوا لقد حدث شئ خاطئ'
            ];
        }
        return $output;
    }
    public function payment_receipt_edit($transaction_id)
    {

        $business_id = request()->session()->get('user.business_id');

        $transaction=Transaction::where('id',$transaction_id)->first();
        $account_code=$transaction->ref_no;

        $debit_account=AccountTransaction::where('type','debit')->where('transaction_id',$transaction_id)
            ->join('accounts','accounts.id','account_transactions.account_id')
            ->first();

        $contact_name='';
        if(!empty($debit_account->contact_id)){
            $contact=Contact::where('id',$debit_account->contact_id)->first();
            $contact_name=$contact->name;
        }



        $credit_account=AccountTransaction::where('type','credit')->where('transaction_id',$transaction_id)
            ->join('accounts','accounts.id','account_transactions.account_id')
            ->first();




        //حسابات الخزن
        $save_account=Account::where('business_id',$business_id)
            ->where('account_type_id',6)->pluck('name','id');

        $currentdate=$this->commonUtil->format_date($credit_account->operation_date,false);
        return view('chartofaccounts::journal.payment_receipt.edit',compact(['account_code',
            'debit_account','credit_account','contact_name','currentdate'
        ]));


    }


    public function __getPaymentDetails($row)
    {
        $details = '';
        if (!empty($row->sub_type)) {
            $details = __('account.' . $row->sub_type);

            if (in_array($row->sub_type, ['fund_transfer', 'deposit']) && !empty($row->transfer_transaction)) {

                $details .= ' ( ' . __('account.from') .': ' . $row->transfer_transaction->account->name . ')';

                $details .= ' ( ' . __('account.to') .': ' . $row->transfer_transaction->account->name . ')';

            }
        } else {
            if (!empty($row->transaction->type)) {
                if ($row->transaction->type == 'purchase') {
                    $details = __('lang_v1.purchase') . '<br><b>' . __('purchase.supplier') . ':</b> ' . $row->transaction->contact->full_name_with_business . '<br><b>'.
                        __('purchase.ref_no') . ':</b> <a href="#" data-href="' . action("PurchaseController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->ref_no . '</a>';
                }elseif ($row->transaction->type == 'expense') {
                    $details = __('lang_v1.expense') . '<br><b>' . __('purchase.ref_no') . ':</b>' . $row->transaction->ref_no;
                } elseif ($row->transaction->type == 'sell') {
                    $is_return = $row->is_return == 1 ? ' (' . __('lang_v1.change_return') . ')' : '';
                    $details = __('sale.sale') . $is_return . '<br><b>' . __('contact.customer') . ':</b> ' . $row->transaction->contact->full_name_with_business . '<br><b>'.
                        __('sale.invoice_no') . ':</b> <a href="#" data-href="' . action("SellController@show", [$row->transaction->id]) . '" class="btn-modal" data-container=".view_modal">' . $row->transaction->invoice_no . '</a>';
                }
            } else {
                //for contact payment which is not advance
                if ($row->is_advance != 1) {
                    if ($row->payment_for_type == 'supplier') {
                        $details .= '<b>' . __('purchase.supplier') . ':</b> ';
                    } elseif ($row->payment_for_type == 'customer') {
                        $details .= '<b>' . __('contact.customer') . ':</b> ';
                    } else {
                        $details .= '<b>' . __('account.payment_for') . ':</b> ';
                    }

                    if (!empty($row->payment_for_business_name)) {
                        $details .= $row->payment_for_business_name . ', ';
                    }
                    if (!empty($row->payment_for_contact)) {
                        $details .= $row->payment_for_contact;
                    }
                }
            }
        }

        if (!empty($row->payment_ref_no)) {
            if (!empty($details)) {
                $details .= '<br/>';
            }

            $details .= '<b>' . __('lang_v1.pay_reference_no') . ':</b> ' . $row->payment_ref_no;
        }
        if (!empty($row->transaction->contact) && $row->transaction->type == 'expense') {
            if (!empty($details)) {
                $details .= '<br/>';
            }

            $details .= '<b>';
            $details .= __('lang_v1.expense_for_contact');
            $details .= ':</b> ' . $row->transaction->contact->full_name_with_business;
        }

        if (!empty($row->transaction->transaction_for)) {
            if (!empty($details)) {
                $details .= '<br/>';
            }

            $details .= '<b>' . __('expense.expense_for') . ':</b> ' . $row->transaction->transaction_for->user_full_name;
        }

        if ($row->is_advance == 1) {
            $total_advance = $row->amount - $row->total_recovered;
            $details .= '<br>';

            if ($total_advance > 0) {
                $details .= '<b>' . __('lang_v1.advance_payment') . '</b>: ' . $this->commonUtil->num_f($total_advance, true) . '<br>';
            }

            if (!empty($row->child_sells)) {
                $details .= '<b>' . __('lang_v1.payments_recovered_for') . '</b>: ' . $row->child_sells . '<br>';
            }

            if ($row->payment_for_type == 'supplier') {
                $details .= '<b>' . __('purchase.supplier') . ':</b> ';
            } elseif ($row->payment_for_type == 'customer') {
                $details .= '<b>' . __('contact.customer') . ':</b> ';
            } else {
                $details .= '<b>' . __('account.payment_for') . ':</b> ';
            }

            if (!empty($row->payment_for_business_name)) {
                $details .= $row->payment_for_business_name . ', ';
            }
            if (!empty($row->payment_for_contact)) {
                $details .= $row->payment_for_contact;
            }
        }

        if (!empty($row->added_by)) {
            $details .= '<br><b>' . __('lang_v1.added_by') . ':</b> ' . $row->added_by;
        }

        return $details;
    }

    public function store_journal(Request $request)
    {

        $transaction_date= $this->commonUtil->uf_date($request->input('transaction_date').date("h:i:sa"), true);
        $business_id = request()->session()->get('user.business_id');

        $input=$request->except('_token');
        $total_debit= $request->debit_total;
        $total_credit= $request->credit_total;
         $def_val=abs($total_debit-$total_credit);

        if($total_debit==0 || $def_val>0.0001 ){
           $output = ['success' => false,
                'msg'=>'إجمالي المدين يجب أن يكون مساوياَ لإجمالي الدائن'
            ];

            return $output;
        }
        try {
        //Generate reference number
        $prefix_type = 'journal';
      if(empty($input['transaction_id'])){
          $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type, $business_id);
          $ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count, $business_id);
      }else{
          $transaction=Transaction::where('id',$input['transaction_id'])->first();
          $ref_no=$transaction->ref_no;
      }


        $transaction_data=[
            'business_id'=>$business_id,
            'journal_source_id'=>1,
            'type'=>'journal',
            'status'=>'final',
            'total_before_tax'=>$total_debit,
            'final_total'=>$total_debit,
            'transaction_date'=> $transaction_date,
            'created_by'=>auth()->user()->id,
            'additional_notes'=>$request->input('additional_notes'),
            'ref_no'=>$ref_no,
            'invoice_no'=>$ref_no,
        ];
        $contact_id=0;
        $account_id=0;

            DB::beginTransaction();

            $transaction = Transaction::updateorcreate(
                ['id'=>$input['transaction_id']],
                $transaction_data);
            Media::uploadMedia($business_id, $transaction, $request, 'document');

         // Delete old transaction account
         AccountTransaction::deleteAccountTransaction($input['transaction_id']);



            $journals=$input['journal'];

        foreach ($journals as $journal){
            $contact=$this->getaccount_id($journal);
            $data=[
                'business_id' => $business_id,
                'account_id' => $contact['account_id'],
                'contact_id'=>$contact['contact_id'],
                'created_by' => session()->get('user.id'),
                'cost_center'=>$journal['cost_center'],
                'percent'=>$journal['percent'],
                'note' => '',
                'transaction_id' => $transaction->id,
                'operation_date' => $transaction_date,
                ];


           if($journal['amount_debit']>0){
               $data['amount']=$journal['amount_debit'];
               $data['type'] ='debit';
               $data['cost_center_amount'] =$journal['amount_debit']*$journal['percent']/100;
               $debit = AccountTransaction::createAccountTransaction($data);
           }else{
               $data['amount']=$journal['amount_credit'];
               $data['type'] ='credit';
               $data['cost_center_amount'] =$journal['amount_credit']*$journal['percent']/100;
               $credit = AccountTransaction::createAccountTransaction($data);
           }
            



        }
         DB::commit();

            $output = ['success' => true,
                'msg' => 'تم حفظ السند بنجاح'];
        }catch (Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false,
                'msg'=>'عفوا لقد حدث شئ خاطئ'
            ];
        }

        return $output;

    }


    public function getaccount_id($journal){
        $account_id=0;
        $contact_id=0;

        if($journal['account_type']==1){
            $account_id=$journal['sub_account'];
        }


        if($journal['account_type']==2){

             $contact_id=$journal['customer'];
             $contact=Contact::where('id',$contact_id)->first();
             $account_id=$contact->account_id;
        }


        if($journal['account_type']==3){
            $contact_id=$journal['supplier'];
            $contact=Contact::where('id',$contact_id)->first();
            $account_id=$contact->account_id;
        }


        if($journal['account_type']==4)
            $account_id=$journal['save_account'];


        $data=[ 'account_id'=>$account_id,
                'contact_id'=>$contact_id
               ];

        return $data;
    }

    /* Journal accounts routing */
    public function routing(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if($request->ajax()){

            $query=AccountRouting::where('account_routings.business_id',$business_id)
                ->leftjoin('accounts','accounts.id','account_routings.account_id')
                ->select('account_routings.id','account_routings.operation','account_routings.status','account_routings.description',
                   'accounts.name')
                ->get();
           $html=view('chartofaccounts::journal.journal.routing_table',compact(['query']))->render();


         return $html;


        }



     return view('chartofaccounts::journal.journal.account_routing');
    }


    public function routing_edit($id)
    {

        $business_id = request()->session()->get('user.business_id');

        if($id>0){
            $data=AccountRouting::where('account_routings.business_id',$business_id)->where('account_routings.id',$id)
                ->leftjoin('accounts','accounts.id','account_routings.account_id')
                ->select('account_routings.id','account_routings.operation','account_routings.status','account_routings.description',
                    'accounts.name','accounts.id as account_id')
                ->first();
        }else{
            $data=new AccountRouting();
        }


           $accounts=Account::forDropdownmainaccount($business_id);

           $html=view('chartofaccounts::journal.journal.edit_route',compact(['data','accounts']))->render();
           return $html;


    }


    public function routing_update(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $id=$request->id;
        $account_id=$request->account_id;
       /* $data=AccountRouting::where('id',$id)->where('business_id',$business_id)->first();
        $data->account_id=$account_id;
        $data->description=$request->description;
        $data->operation=$request->operation;
        $data->save();*/

        $data=AccountRouting::updateorcreate(['id'=>$id],
        [
            'business_id'=>$business_id,
            'account_id'=>$account_id,
            'description'=>$request->description,
            'operation'=>$request->operation,
        ]


        );


        $output=['success'=>true,
            'msg'=>'تم حفظ التوجية بنجاح'];

        return $output;
    }


}
