<?php

namespace Modules\ChartOfAccounts\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\Contact;
use App\Models\CostCenter;
use App\Models\SysAccount;
use App\Transaction;
use App\User;
use App\Utils\ChartofAccountUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\ChartOfAccounts\Entities\chartofaccount;
use Modules\ChartOfAccounts\Entities\JournalDocument;
use Yajra\DataTables\Facades\DataTables;

class ChartOfAccountsController extends Controller
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
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if($request->ajax()){

           $transactions=Transaction::where('transactions.business_id',$business_id)
                 ->join('users as u','u.id','transactions.created_by')
                 ->join('account_transactions','account_transactions.transaction_id','transactions.id')
                 ->select('transactions.id as id','transactions.transaction_date','transactions.additional_notes',
                       'transactions.type','transactions.invoice_no','transactions.ref_no',
                       'transactions.final_total',
                       'u.surname',
                       'u.first_name',
                       'u.last_name',
                          DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as added_by")

               )->groupby('transactions.id')->orderby('transactions.id','desc');

           if(!empty($request->account_id)){
               $transactions->where('account_transactions.account_id',$request->account_id);
           }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $transactions->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            $datatable = Datatables::of($transactions)
                ->addColumn(
                    'action',function ($row) {
                    $html =
                        '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    $html .= '<li><a href="#" data-href="' . action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@show', [$row->id]) . '" class="  btn-modal" data-container=".view_modal"> <i class="fas fa-eye" aria-hidden="true"></i> ' . __("messages.view") . ' </a></li> ';
                    if ($row->type <> 'sell' || $row->type <> 'purchase'){
                        $html .= '<li><a href="' . action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@create', [$row->id]) . '" class=" btn-modal-edit">  <i class="fas fa-edit">  </i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\ChartOfAccounts\Http\Controllers\JournalController@cash_receipt_delete', [$row->id]) . '" class="  btn-modal-delete">  <i class="fas fa-trash">  </i> ' . __("messages.delete") . '</a></li>';
                        }
                    $html .= '<li><a href="' . action('\Modules\ChartOfAccounts\Http\Controllers\JournalDocumentsController@create', [$row->id]) . '" class=" add_document" data-container=".view_modal"> <i class="fas fa-plus" aria-hidden="true"></i> ' . __("chartofaccounts::lang.add_document") . ' </a></li> ';



                    $html .= '</ul></div>';
                       return $html;

                     })


                ->editColumn(
                    'final_total',
                    '<span class="final-total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>'
                )
                ->editColumn('additional_notes',function ($row){
                    $html=$row->additional_notes;
                    if($row->type=='sell'){
                        $html=' قيد يومية مبيعات  ';
                    }

                    if($row->type=='purchase'){
                        $html=' قيد يومية مشتريات  ';
                    }
                    return $html;
                })
                ->editColumn('invoice_no', function ($row) {
                    $invoice_no = $row->invoice_no;
                    if(empty($invoice_no)){
                        $invoice_no=$row->ref_no;
                    }
                      return $invoice_no;
                })
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->filterColumn('added_by', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view")) {
                            return  action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@show', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }])

            ;

            $rawColumns = ['action','final_total',  'invoice_no', 'conatct_name','additional_notes'];

            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }

        $customers = Contact::customersDropdown($business_id, false);
        $sales_representative = User::forDropdown($business_id, false, false, true);
        $accounts=Account::where('business_id',$business_id)->whereIN('account_type_id',[3,4,5,6])->pluck('name','id');

        return view('chartofaccounts::journal.journal.index',compact(['accounts','sales_representative']));
    }

    public function create($journal_id=0){
        $business_id = request()->session()->get('user.business_id');
        $currentdate=$this->commonUtil->format_date(now(),false);
        if($journal_id>0){
            $journal= Transaction::where('business_id',$business_id)->where('id',$journal_id)->first();
            $account_transactions=AccountTransaction::where('transaction_id',$journal_id)
                ->join('accounts','accounts.id','account_transactions.account_id')
                ->leftjoin('contacts','contacts.id','account_transactions.contact_id')
                ->select('account_transactions.*','accounts.account_type_id',
                    'contacts.type as contact_type','contacts.id as contact_id')
                ->get();
            $currentdate=$this->commonUtil->format_date($journal->transaction_date,false);
        }else{
            $journal=new Transaction();
            $account_transactions=new AccountTransaction();
        }
        $account=new Account();
        // الحسابات الفرعية
        $accounts=Account::where('accounts.business_id',$business_id)
            ->join('account_types','account_types.id','accounts.account_type_id')
            ->whereIn('account_types.id',[3])
            ->select('accounts.id',
                DB::raw('CONCAT(accounts.account_code,"- ",accounts.name ) as name'))
            -> pluck('name','id');

        //
        $customers=Contact::where('contacts.business_id',$business_id)
            ->whereIn('type',['customer','both'])
            ->join('accounts','accounts.id','contacts.account_id')
            ->select('contacts.id',
                DB::raw('CONCAT(accounts.account_code,"- ",contacts.name ) as name'))
            -> pluck('contacts.name','id');

        $suppliers=Contact::where('contacts.business_id',$business_id)
            ->whereIn('type',['supplier','both'])
            ->join('accounts','accounts.id','contacts.account_id')
            ->select('contacts.id',
                DB::raw('CONCAT(accounts.account_code,"- ",contacts.name ) as name'))
            -> pluck('contacts.name','id');


        //حسابات الخزن
        $save_account=Account::where('business_id',$business_id)
            ->where('account_type_id',6)
            ->select('accounts.id',
                DB::raw('CONCAT(accounts.account_code,"- ",accounts.name ) as name'))
            ->pluck('name','id');


        //


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

        //مركز التكلفة
        $cost_center=CostCenter::where('business_id',$business_id)->where('type',1)
            ->pluck('name','id');

        $cost_center->prepend(__('lang_v1.none'), '');

        return view('chartofaccounts::journal.journal.create',compact(['accounts','account',
            'save_account','currentdate'
            ,'customers','suppliers','account_type','account_id','cost_center','account_transactions','journal'
        ]));
    }

    public function journal_row(Request $request)
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

        //مركز التكلفة
        $cost_center=CostCenter::where('business_id',$business_id)->where('type',1)
            ->pluck('name','id');

        $cost_center->prepend(__('lang_v1.none'), '');

        $row_count=$request->row_count;
        $html=view('chartofaccounts::journal.journal.journal_row',compact(['accounts','account',
            'save_account','currentdate'
            ,'customers','suppliers','account_type','account_id','cost_center','row_count']));
        return $html;
    }
    public function chart_view_old (){
        $business_id = request()->session()->get('user.business_id');
        $accounts=chartofaccount::where('parent_id','=',0)
                      ->where(function ($query) use ($business_id) {
                        $query->where('business_id',$business_id)
                              ->orWhere('business_id',0);
                      })->pluck('name','id');

        $accounts_all=chartofaccount::where('type','=',0)
                     ->where(function ($query) use ($business_id) {
                      $query->where('business_id',$business_id)
                    ->orWhere('business_id',0);
            })->pluck('name','id');
        $data=[];

            $data=$this->get_node_data(0);

        if (request()->ajax()) {
            $data=$this->get_node_data(0);
            return $data;
        }



        $data=json_encode((array_values($data)));


       return view('chartofaccounts::index',compact('accounts','accounts_all'));
    }


    public function chart_view(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if(!auth()->user()->can('chart_of_accounts.view')){
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $html=$request->view_type;

            if($request->view_type=='table'){
                $html=$this->account_table_view();
            }else{
                $html=$this->account_tree_view();
            }


            $output=[
                'success'=>true,
                'html'=>$html
            ];
            return $output;
        }

        return view('chartofaccounts::accounts.index');
    }



    public function account_table_view(){
        $business_id = request()->session()->get('user.business_id');
        $html="<table class='table table-bordered table-bordered-dark account-table table-hover'>
                    <th>
                    <td colspan='10'></td>
                     </th>
                     ";

        $main_accounts=Account::where('business_id',$business_id)->where('chart_order',1)
            ->where('parent_id',0)->get();
        foreach ($main_accounts as $main_account){
            $html=$html.$this->get_account_table($main_account);
            $live2_accounts=Account::where('business_id',$business_id)
                ->where('parent_id',$main_account->id)->orderby('chart_order') ->get();

            foreach ($live2_accounts as $live2_account) {
                $html=$html.$this->get_account_table($live2_account);
                $live3_accounts=Account::where('business_id',$business_id)
                    ->where('parent_id',$live2_account->id)->get();

                foreach ($live3_accounts as $live3_account) {
                    $html=$html.$this->get_account_table($live3_account);


                    $live4_accounts=Account::where('business_id',$business_id)
                        ->where('parent_id',$live3_account->id)->get();
                    foreach ($live4_accounts as $live4_account) {
                        $html=$html.$this->get_account_table($live4_account);
                    }
                }

            }
        }
        $html =$html."<tr>
                      <td colspan='10'></td>  
                     </tr>";

        return $html;
    }
    public function get_account_table($account)
{
    $level=$account->chart_order-1;
    $colspan=10-$level;
        $html = "<tr>";
        for($i=0;$i<$level;$i++){
            $html =$html.'  <td></td>';
         }



        if($account->account_type_id==3 || $account->account_type_id==6 ){
            $html = $html ." <td class='account_code'><span><i class='account-logo fa fa-dollar-sign'></i> </span></td>  ";
           }else{
            $html = $html ." <td class='account_code'><span><i class='account-logo fa fa-folder-open'></i> </span></td>  ";

        }


        $html = $html ." <td colspan='".$colspan."'><span class='account_name account' account_id='".$account->id."'>   #" .$account->account_code." " . $account->name . "</span></td>";


        $action_buttons=' <a data-href="' . action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@addacount', [$account->id]) . '"     data-container=".brands_modal" class="btn btn-success btn-flat btn-sm cursor-pointer btn-modal-edit">
                                    <i class="fas fa-edit"></i>
                                    '.__("messages.edit").'
                                </a>';
        $action_buttons=$action_buttons.' <a data-href="' . action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@addacount', [$account->id]) . '"   class="btn bg-navy btn-default   btn-flat btn-sm cursor-pointer">
                                    <i class="fas fa-eye"></i>
                                    '.__("messages.view").'
                                </a>';

    $action_buttons=$action_buttons.' <a data-href="' . action('\Modules\ChartOfAccounts\Http\Controllers\ChartOfAccountsController@deleteaccount', [$account->id]) . '"   class="btn btn-danger btn-sm cursor-pointer btn-modal-delete">
                                    <i class="fas fa-trash"></i>
                                    '.__("messages.delete").'
                                </a>';

    $account_nature=[
        '-1'=>'مدين',
        '1'=>'دائن'
    ];

          $html=$html."<td>".$account_nature[$account->account_nature]."</td>
                      <td style='width: 220px'>".$action_buttons."</td>
                      </tr>";
        return $html;
    }

    public function account_tree_view()
    {
        $business_id = request()->session()->get('user.business_id');
        $html='<div class="tree">
                <ul class="node-treeview">';


        $main_accounts=Account::where('business_id',$business_id)->where('chart_order',1)
            ->where('parent_id',0)->get();

        foreach ($main_accounts as $main_account) {
             $html = $html . '<li>
                        <details>
                            <summary class="main-chart">
                                <span class="account" account_id="' . $main_account->id . '">#'. $main_account->account_code .' '. $main_account->name . '</span>
                             </summary>
                       <ul>';

             $html=$html.$this->account_tree_view2($main_account->id);

            $html = $html . '</ul></details>
                          </li>
                          ';


        }

          $html=$html.'</ul>
                          </div>';



        return $html;
    }

    public function account_tree_view2($account_id){
        $business_id = request()->session()->get('user.business_id');
        $html ='';
        $live2_accounts=Account::where('business_id',$business_id)
            ->where('parent_id',$account_id)->orderby('chart_order') ->get();
        foreach ($live2_accounts as $live2_account) {
            if( $live2_account->account_type_id==2){
                $html=$html.'<li>
                                    <details>
                                        <summary>
                                            <span class="account" account_id="' .  $live2_account->id . '">
                                              #'. $live2_account->account_code .' ' .  $live2_account->name . '
                                            </span>
                                        </summary>
                                       <ul> ';
                  $html=$html.$this->account_tree_view2($live2_account->id);
                  $html=$html.'</details>
                                        </li>
                                   ';
            }else{
                $html=$html.'<li>
                        <span class="account fa fa-file" account_id="' .  $live2_account->id . '">  -#'. $live2_account->account_code .' ' .  $live2_account->name . ' </span></li>';

            }
        }
        return $html;

    }


    public function addacount(Request $request,$account_id=0){
        $business_id = request()->session()->get('user.business_id');
        $input = $request->only('id', 'account_id');
        $parent_id=$request->parent_id;
        $account_type=[
            '-1'=>__('messages.please_select'),
            '2'=>__('chartofaccounts::lang.main_account'),
            '3'=>__('chartofaccounts::lang.account_chiled'),
            '6'=>__('chartofaccounts::lang.account_save'),

        ];
        $main_accounts=Account::where('business_id',$business_id)
                       ->where('chart_order',0)->where('parent_id',0)
                       ->select('id',
               DB::raw('CONCAT(COALESCE(name, ""), " ", COALESCE(account_code, "")) as full_name'))
            ->get()
            ->pluck('full_name','id');

        $main_accounts->prepend(__('lang_v1.none'), '0');


        $accounts=Account::where('account_type',0)->where('business_id',$business_id)
                  ->whereIN('account_type_id',[1,2])
            ->select('id',
                DB::raw('CONCAT(COALESCE(account_code, ""), "- ", COALESCE(name, "")) as full_name'))
            ->get()
            ->pluck('full_name','id');

        $accounts->prepend(__('lang_v1.none'), '0');


        $account_id=$account_id?$account_id:$input['account_id'];

        $account_code="";
        if(!empty($account_id)){
            $account=Account::where('id',$account_id)
                           ->first();

        }else{
            $account=new Account();
            $account->parent_id=$parent_id;
            $account->account_code=$this->getnextaccountcode($parent_id);
        }


        return view('chartofaccounts::accounts.create',compact( ['main_accounts','accounts','account','account_type','account_code']));
    }


    public function getnextaccountcode($parent_id,$account_id='')
    {

        if(!empty($account_id)){
            $account=Account::where('id',$account_id)->first();
            return $account->account_code;
        }else{
        $parent_account=Account::where('id',$parent_id)->first();
        $parent_account_code="";
        if(!empty($parent_account)){
            $parent_account_code=$parent_account->account_code;
        }

        $last_account=Account::where('parent_id',$parent_id)->orderBy('id', 'desc')->first();
        $last_code=0;
        if(!empty($last_account)){
            $last_code=$last_account->account_code;
        }

        $account_code=str_replace($parent_account_code,'',$last_code+1);

        $count =$parent_account_code. str_pad($account_code, 2, '0', STR_PAD_LEFT);
        return $count;
        }
    }

    public function saveacount(Request $request){
        $business_id = request()->session()->get('user.business_id');

        $code_exit=Account::where('business_id',$business_id)->where('account_code', $request->account_code)
        ->where('id','<>',$request->account_id)->count();


              if($code_exit>0){
                  $output = ['success' => false,
                      'msg' => __("chartofaccounts::lang.code_found")
                  ];

                  return $output;
              }

          if($request->account_type<0){
              $output = ['success' => false,
                  'msg' => __("chartofaccounts::lang.select_account_type")
              ];

              return $output;
          }

        if(!empty($request->parent_id)){
            $parint_account=Account::where('business_id',$business_id)->where('id', $request->parent_id)->first();
        }



     try {
         DB::beginTransaction();

         $data=[
             'business_id'=>$business_id,
             'account_code' => $request->account_code,
             'parent_id' => $request->parent_id ? $request->parent_id : 0,
             'name' => $request->name,
             'account_nature'=>$request->account_nature,
             'account_type'=>$request->account_type?$request->account_type:0,
             'note'=>$request->notes,
             'chart_order'=>$request->parent_id?$parint_account->chart_order+1:1,
             'created_by'=>auth()->user()->id,
             'account_type_id'=>$request->account_type_id?$request->account_type_id:0,
         ];
         $chartofaccount=Account::updateOrCreate(
             [
             'id'=>$request->account_id,
             'business_id'=>$business_id,
              ],
              $data);

         // update main account

         $output = ['success' => 1,
             'msg' => __('chartofaccounts::lang.added_success')
         ];
         DB::commit();
     }catch (\Exception $e) {
         DB::rollBack();
         \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
         $output = ['success' => 0,
             'msg' => __("messages.something_went_wrong")
         ];
     }

        return $output;

    }

    public function getaccount(Request $request){
    $business_id = request()->session()->get('user.business_id');
    $accounts=chartofaccount::where('type',0)->where('parent_id','=',$request->id)
                       ->where(function ($query) use ($business_id) {
                           $query->where('business_id',$business_id)
                               ->orWhere('business_id',0);
                            })->get();
    $html='';
    foreach ($accounts as $account){
        $html .='<div class="parent" onclick="getaccount('.$account->id.')"  id="'.$account->id.'">
                        '.$account->name.'
                           </div>';

    }
    $output=[
        'data'=>$accounts,
        'html'=>$html,
        'parent'=>$request->id,
    ];
    return $output;
}

 public  function get_node_data($parent_category_id)
    {
       $output = array();
        $result=chartofaccount::where('parent_id','=',$parent_category_id)->get();

        foreach($result as $row)
        {
            $sub_array = array();
            $sub_array['text'] = $row->name;
            $sub_array['nodes'] = array_values($this->get_node_data($row->id));
            $output[] = $sub_array;
       }

        return $output;
    }




    public function getaccount2(Request $request){
        $business_id = request()->session()->get('user.business_id');
        $accounts=chartofaccount::where('business_id',$business_id)
            ->where('parent_id','=',$request->id)->get();
        $html='';
        foreach ($accounts as $account){
            $html .='<div class="parent" onclick="getaccount('.$account->id.')"  id="'.$account->id.'">
                       
                        '.$account->name.'
                           </div>';
        }
        $output=[
            'html'=>$html,
            'parent'=>$request->id,
        ];
        return $output;
    }



    public function deleteaccount(Request $request,$account_id=0)
    {
        $account_id=$account_id?$account_id:$request->account_id;

        $account=Account::where('id',$account_id)->first();
        $account_name=$account->account_code.' '.$account->name;

        $has_chiled=Account::where('parent_id',$account->id)->count();
        if($has_chiled>0){
            $output = ['success' => false,
                'msg' =>'عفوا يجب حذف جميع الحسابات التابعة أولا !!',
            ];
              return $output;
        }


        $account->delete();
        /*DB::beginTransaction();
        $account=SysAccount::where('id',8)->first();
        $parent_id=$account->parent_id;
        $amount=10;
        $account->balance=$account->balance+$amount;
        $account->save();
        while ($parent_id>0){
            $account=SysAccount::where('id',$parent_id)->first();
            $parent_id=$account->parent_id;
            $account->balance=$account->balance+$amount;
            $account->save();
        }


      DB::commit();*/



        $output = ['success' => true,
            'msg' => __('chartofaccounts::lang.deleted_success'),
        ];

        return $output;
    }



    public function linkaccounts()
    {
        $data=SysAccount::get();
        foreach ($data as $account){
            $parent=SysAccount::where('account_code',$account->parent_code)->first();
            if(!empty($parent)){
                $account->parent_id=$parent->id;
                $account->save();
            }else{
                $account->parent_id=0;
                $account->save();
            }



        }
    }


    public function show ($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $transaction=Transaction::where('business_id',$business_id)->where('id',$id)->first();
        $account_transactions=AccountTransaction::where('transaction_id',$transaction->id)
            ->join('accounts','accounts.id','account_transactions.account_id')
            ->leftjoin('contacts','contacts.id','account_transactions.contact_id')
            ->select('account_transactions.amount as amount','account_transactions.type as type'
                ,'contacts.name as contact_name','accounts.name as account_name'
               ,'accounts.account_code'
            )
            ->get();

        $documents=JournalDocument::where('journal_id',$id)->get();


        return view('chartofaccounts::journal.journal_show',compact(['account_transactions','transaction','documents']));
    }
}
