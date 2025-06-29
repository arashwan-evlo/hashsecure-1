<?php

namespace Modules\Installment\Http\Controllers;

use App\Contact;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\ContactUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Modules\Installment\Entities\installmentdb;
use Modules\Installment\Entities\Installments;
use Modules\Installment\Entities\installmentsystem;
use Yajra\DataTables\DataTables;
use DB;


class CustomerController extends Controller
{



    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('installment.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = auth()->user()->business_id;
        /* $con=new TransactionUtil();
        $contactinfo= $con->getLedgerDetails(19,'2000-01-01','3000-01-01');
        dd($contactinfo);*/

        $systems = installmentsystem::where('business_id', '=', $business_id)->pluck('name', 'id');
        $systems->prepend(__('messages.please_select'), '');
        $customers = Contact::where('business_id', '=', $business_id)->where('type', '!=', 'supplier')->pluck('name', 'id');
        $customers->prepend(__('messages.all'), '');
        return view('installment::customer.index', ['customers' => $customers, 'systems' => $systems]);
    }

    public function getLedger()
    {
        if (!auth()->user()->can('installment.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $contact_id = request()->input('contact_id');
    }

    public function createinstallment(Request $request)
    {
        if (!auth()->user()->can('installment.view')) {
            $output = [
                'success' => false,
                'msg' => __("عفوا لا تملك الصلاحية")
            ];
        }

        $this->saveinstallmen($request);

        $output = [
            'success' => true,
            'msg' => __("installment::lang.added_success")
        ];
        return $output;
    }

    public function saveinstallmen($request)
    {
        $check = installmentdb::where('transaction_id', $request['transaction_id'])->get();
        foreach ($check as $row) {
            $installment_id = $row->id;
            installmentdb::where('id', $installment_id)->delete();
            Installments::where('installment_id', $installment_id)->delete();
        }



        $business_id = auth()->user()->business_id;
        $user_id = auth()->user()->id;
        $installment = installmentdb::create([
            'business_id' => $business_id,
            'contact_id' => $request['contact_id'],
            'transaction_id' => $request['transaction_id'],
            'system_id' => $request['system_id'] ? $request['system_id'] : 0,
            'installment_value' => $request['installment_value'],
            'total' => $request['total'],
            'number' => $request['number'],
            'paidnumber' => 0,
            'period' => $request['period'],
            'type' => $request['type'],
            'benefit' => $request['benefit'],
            'benefit_type' => $request['benefit_type'],
            'benefit_value' => $request['benefit_value'],
            'latfines' => $request['latfines'],
            'latfinestype' => $request['latfinestype'],
            'installmentdate' => $request['installmentdate'],
            'notes' => $request['notes'],
            'user_id' => $user_id
        ]);
        $installmentdate = Carbon::parse($request['installmentdate']);
        $ninstallmentdate = $installmentdate;
        $period = $request['period']; //2

        for ($n = 0; $n < $request['number']; $n++) {
            $installmentdate = Carbon::parse($request['installmentdate']);
            if ($request['type'] == 'day')
                $installmentdate = $installmentdate->addDays($period * $n);

            if ($request['type'] == 'month') {
                $installmentdate = $installmentdate->addMonths($period * $n);
            }
            if ($request['type'] == 'year')
                $installmentdate = $installmentdate->addYears($period * $n);
            $data = Installments::create([
                'business_id' => $business_id,
                'installment_id' => $installment->id,
                'contact_id' => $request['contact_id'],
                'transaction_id' => $request['transaction_id'],
                'payment_id' => 0,
                'system_id' => $request['system_id'] ? $request['system_id'] : 0,
                'installment_number' => $n + 1,
                'installment_value' => $request['installment_value'] / $request['number'],
                'number' => $request['number'],
                'period' => $request['period'],
                'type' => $request['type'],
                'benefit' => $request['benefit'],
                'benefit_type' => $request['benefit_type'],
                'benefit_value' => $request['benefit_value'] / $request['number'],
                'latfines' => $request['latfines'],
                'latfinestype' => $request['latfinestype'],
                'latfines_value' => '0',
                'installmentdate' => $installmentdate,


            ]);
        }


        // add advanced payments
        if ($request['advanced'] > 0) {
            $payment = TransactionPayment::create([
                'business_id' => $business_id,
                'transaction_id' => $request['transaction_id'],
                'is_return' => 0,
                'is_advance' => 0,
                'payment_for' => $request['contact_id'],
                'amount' => $request['advanced'],
                'paid_on' => $request['installmentdate'],
                'method' => 'cash',
                'account_id' => $request['account_id'],
                'created_by' => auth()->user()->id,
                'note' => 'مقدم تقسيط'
            ]);
        }


        //set transaction as installment :
        $transaction = Transaction::findorfail($request['transaction_id']);
        $transaction->payment_status = 'installmented';
        $transaction->save();
    }
    public function getcustomerdata(Request $request)
    {

        $business_id = auth()->user()->business_id;
        $con = new TransactionUtil();
        $contactinfo = $con->getLedgerDetails($request->id, '2000-01-01', '3000-01-01');

        return $contactinfo;
    }

    public function getinstallment(Request $request)
    {

        $business_id = request()->session()->get('user.business_id');

        $installments = installmentdb::where('installment_db.business_id', $business_id)
            ->join('contacts', 'contacts.id', 'installment_db.contact_id')
            ->leftjoin('installments', 'installments.installment_id', 'installment_db.id')
            ->select(
                'contacts.name',
                'installment_db.installmentdate',
                'total',
                'installments.installment_value as dd',
                'installment_db.number',
                'paidnumber',
                'installment_db.id',
                'installments.benefit_value'

            )
            ->groupby('installment_db.id')
            ->orderby('installment_db.id');

        if (!empty($request->id)) {
            $installments->where('installment_db.contact_id', '=', $request->id);
        }


        return DataTables::of($installments)
            ->addColumn(
                'action',

                ' <a href="{{action(\'\Modules\Installment\Http\Controllers\InstallmentController@index\',[$id])}}" class="btn btn-xs btn-primary edit_editinstallment_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.view")</a>
                        &nbsp;
                @can("installment.delete")
                   <button data-href="{{action(\'\Modules\Installment\Http\Controllers\CustomerController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_installment_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                @endcan'
            )
            ->editcolumn('dd', function ($row) {
                return number_format($row->dd + $row->benefit_value, '2');
            })


            ->removeColumn('id')
            ->removeColumn('benefit_value')
            ->rawColumns([5, 6])
            ->make(false);
    }


    /*function to show create installment from transaction
     *
     * */
    public function  createinstallment2(Request $request)
    {

        $total = $request->total;
        $total_paid = $request->paid ? $request->paid : 0;
        $transaction = Transaction::select('transactions.*', 'contacts.name')
            ->where('transactions.id', '=', $request->id)
            ->join('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->first();



        $business_id = auth()->user()->business_id;

        $systems = installmentsystem::where('business_id', '=', $business_id)->pluck('name', 'id');
        $systems->prepend(__('messages.please_select'), '');



        return view('installment::customer.create', ['systems' => $systems, 'contact_id' => $request->id, 'transaction' => $transaction, 'total' => $total, 'total_paid' => $total_paid]);
    }



    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('installment::create');
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
        return view('installment::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('installment::edit');
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
        if (!auth()->user()->can('installment.delete')) {
            $output = [
                'success' => false,
                'msg' => 'عفوالا تملك الصلاحية لحذف قسط'
            ];
            return $output;
        }

        $data = installmentdb::where('id', '=', $id)->first();
        $paidnumber = Installments::where('installment_id', '=', $id)->where('payment_id', '>', 0)->count();
        if ($paidnumber > 0) {
            $output = [
                'success' => false,
                'data' => $data,
                'msg' => 'عفوا لايمكن الحذف تم سداد بعض الأقساط'
            ];
            return $output;
        }

        $data->delete();
        $installment = Installments::where('installment_id', '=', $id)->where('payment_id', '=', 0)->delete();
        $transaction = Transaction::findorfail($data->transaction_id);
        $transaction->payment_status = 'due';
        $transaction->save();



        $output = [
            'success' => true,
            'data' => $data,
            'msg' => __("installment::lang.deleted_success")
        ];
        return $output;
    }

    public function contacts()
    {

        $business_id = auth()->user()->business_id;
        $customers = Contact::where('contacts.business_id', '=', $business_id)->where('contacts.type', '!=', 'supplier')
            ->join('installment_db', 'contacts.id', '=', 'installment_db.contact_id')
            ->pluck('name', 'contacts.id');

        $customers->prepend(__('messages.all'), '');
        return view('installment::customer.contacts', ['customers' => $customers]);
    }

    public function contactwithinstallment(Request $request)
    {
        $business_id = auth()->user()->business_id;


        $installments = Contact::select('contacts.name', 'contacts.id as id', 'installments.payment_id as paid_date', DB::raw('count(*) as count'))
            ->where('contacts.business_id', '=', $business_id)
            ->where('contacts.type', '!=', 'supplier')
            ->where(function ($query) use ($request) {
                if ($request->id > 0)
                    $query->where('installments.contact_id', '=', $request->id);

                if ($request->installment_status == 1)
                    $query->where('payment_id', '>', 0);

                if ($request->installment_status == 2) {
                    $query->where('installmentdate', '>=', Carbon::now()->addDays(-1));
                    $query->where('payment_id', '=', 0);
                }



                if ($request->installment_status == 3) {
                    $query->where('installmentdate', '<', Carbon::now()->addDays(-1));
                    $query->where('payment_id', '=', 0);
                }




                if (!$request->installment_id) {
                    if ($request->datefrom && $request->dateto)
                        $query->whereBetween('installmentdate', [$request->datefrom, $request->dateto]);
                }
            })
            ->join('installments', 'contacts.id', '=', 'installments.contact_id')
            ->groupBy('contacts.id');

        return DataTables::of($installments)


            ->addColumn(
                'paid_date',
                function ($row) use ($request) {
                    if ($request->installment_status == 0)
                        return $st = 'الكل';
                    if ($request->installment_status == 1)
                        return $st = 'مدفوع';
                    if ($request->installment_status == 2)
                        return $st = 'مستحق';

                    if ($request->installment_status == 3)
                        return $st = 'متأخر';
                }
            )
            ->addColumn(
                'action',
                function ($row) {

                    $html = ' <a href="' . action('\Modules\Installment\Http\Controllers\InstallmentController@index', [$row->id]) . '" class="btn btn-xs btn-primary edit_editinstallment_button"><i class="glyphicon glyphicon-edit"></i>' . __("messages.view") . '</a>';
                    return $html;
                }


            )



            ->removeColumn('id')

            ->rawColumns([3])
            ->make(false);
    }

    public function installments(Request $request)
    {
        $business_id = auth()->user()->business_id;
        $installmentdate = Carbon::now()->addDay(35);
        $query = Installments::where('installments.business_id', $business_id)
            ->join('contacts', 'contacts.id', 'installments.contact_id')
            ->whereNull('paid_date')
            ->whereDate('installments.installmentdate', '<', $installmentdate)
            ->select(
                'contacts.name',
                'installments.installmentdate',
                'installments.benefit_value',
                'installments.id',
                'installments.installment_id as contact_id'
            );
        return DataTables::of($query)
            ->editColumn(
                'id',
                function ($row) {
                    $html = ' 
                       <button data-href="' . action('\Modules\Installment\Http\Controllers\InstallmentController@addpayment', [$row->id]) . '" class="btn btn-xs btn-primary add_payment"><i class="glyphicon glyphicon-edit"></i> تحصيل</button>
                         <button data-href="' . action('\Modules\Installment\Http\Controllers\InstallmentController@installmentdelete', [$row->id]) . '" class="btn btn-xs  btn-danger paymentdelete"><i class="glyphicon glyphicon-edit"></i> حذف </button>
                         ';
                    $html = '<a href="' . action('\Modules\Installment\Http\Controllers\InstallmentController@index', ['id' => $row->contact_id]) . '" class="btn btn-xs  btn-danger" >عرض</a>';
                    return $html;
                }
            )
            ->removeColumn('contact_id')
            ->rawColumns([0, 1, 2, 3])
            ->make(false);
    }
}
