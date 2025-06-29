<?php

namespace Modules\Essentials\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Essentials\Entities\EssentialsLeave;
use Modules\Essentials\Entities\EssentialsHoliday;
use Modules\Essentials\Entities\EssentialsAttendance;
use App\User;
use App\Category;
use App\Utils\ModuleUtil;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ModuleUtil $moduleUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function hrmDashboard()
    {
        $business_id = request()->session()->get('user.business_id');

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        $user_id = auth()->user()->id;

        $users = User::where('business_id', $business_id)
                    ->user()
                    ->get();

        $departments = Category::where('business_id', $business_id)
                            ->where('category_type', 'hrm_department')
                            ->get();
        $users_by_dept = $users->groupBy('essentials_department_id');

        $today = new \Carbon('today');

        $one_month_from_today = \Carbon::now()->addMonth();
        $leaves = EssentialsLeave::where('business_id', $business_id)
                            ->where('status', 'approved')
                            ->whereDate('end_date', '>=', $today->format('Y-m-d'))
                            ->whereDate('start_date', '<=', $one_month_from_today->format('Y-m-d'))
                            ->with(['user', 'leave_type'])
                            ->orderBy('start_date', 'asc')
                            ->get();

        $todays_leaves = [];
        $upcoming_leaves = [];

        $users_leaves = [];
        foreach ($leaves as $leave) {
            $leave_start = \Carbon::parse($leave->start_date);
            $leave_end = \Carbon::parse($leave->end_date);

            if ($today->gte($leave_start) && $today->lte($leave_end)) {
                $todays_leaves[] = $leave;

                if ($leave->user_id == $user_id) {
                    $users_leaves[] = $leave;
                }
            } else if ($today->lt($leave_start) && $leave_start->lte($one_month_from_today)) {
                $upcoming_leaves[] = $leave;
                
                if ($leave->user_id == $user_id) {
                    $users_leaves[] = $leave;
                }
            }
        }

        $holidays_query = EssentialsHoliday::where('essentials_holidays.business_id', 
                                $business_id)
                                ->whereDate('end_date', '>=', $today->format('Y-m-d'))
                                ->whereDate('start_date', '<=', $one_month_from_today->format('Y-m-d'))
                                ->orderBy('start_date', 'asc')
                                ->with(['location']);

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $holidays_query->where(function ($query) use ($permitted_locations) {
                $query->whereIn('essentials_holidays.location_id', $permitted_locations)
                    ->orWhereNull('essentials_holidays.location_id');
            });
        }
        $holidays = $holidays_query->get();

        $todays_holidays = [];
        $upcoming_holidays = [];

        foreach ($holidays as $holiday) {
            $holiday_start = \Carbon::parse($holiday->start_date);
            $holiday_end = \Carbon::parse($holiday->end_date);

            if ($today->gte($holiday_start) && $today->lte($holiday_end)) {
                $todays_holidays[] = $holiday;
            } else if ($today->lt($holiday_start) && $holiday_start->lte($one_month_from_today)) {
                $upcoming_holidays[] = $holiday;
            }
        }

        $todays_attendances = [];
        if ($is_admin) {
            $todays_attendances = EssentialsAttendance::where('business_id', $business_id)
                                ->whereDate('clock_in_time', \Carbon::now()->format('Y-m-d'))
                                ->with(['employee'])
                                ->orderBy('clock_in_time', 'asc')
                                ->get();
        }

        return view('essentials::dashboard.hrm_dashboard')
                ->with(compact('users', 'departments', 'users_by_dept', 'todays_holidays', 'todays_leaves', 'upcoming_leaves', 'is_admin', 'users_leaves', 'upcoming_holidays', 'todays_attendances'));
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function essentialsDashboard()
    {
        return view('essentials::dashboard.essentials_dashboard');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('essentials::create');
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
        return view('essentials::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('essentials::edit');
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

    public function employee()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $employee_status=[
            ''=>'الكل',
            'active'=>'في الخدمة',
            'inactive'=>'غير نشط',
            'terminated'=>'مفصول'
        ];


        if (request()->ajax()) {
            if ($is_admin) {
                // Do some thing
            }


            $employees = User::where('users.business_id', $business_id)
                ->leftJoin('categories as dept', 'users.essentials_department_id', '=', 'dept.id')
                ->leftJoin('categories as dsgn', 'users.essentials_designation_id', '=', 'dsgn.id')
                ->select([
                    'users.*',

                    DB::raw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as user"),
                   'dept.name as department',
                   'dsgn.name as designation'
                ]);

               if (!empty(request()->input('designation_id'))) {
                    $employees->where('dsgn.id', request()->input('designation_id'));
                }

                if (!empty(request()->input('department_id'))) {
                    $employees->where('dept.id', request()->input('department_id'));
                }
                 if (!empty(request()->input('status'))) {
                     $employees->where('users.status', request()->input('status'));
                          }


               return Datatables::of($employees)
                ->addColumn(
                    'action',
                    function ($row) use ($is_admin) {
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Essentials\Http\Controllers\PayrollController@show', [$row->id]) . '" data-container=".view_modal" class="btn-modal"><i class="fa fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';

                        if ($is_admin) {
                            $html .= '<li><a href="#"  class="employee_edit "   data-href="' . action('\Modules\Essentials\Http\Controllers\DashboardController@employee_edit', [$row->id]) . '"><i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>';
                            $html .= '<li><a href="' . action('\Modules\Essentials\Http\Controllers\PayrollController@destroy', [$row->id]) . '" class="delete-payroll"><i class="fa fa-trash" aria-hidden="true"></i> ' . __("messages.delete") . '</a></li>';
                        }

                        $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) . '" class="view_payment_modal"><i class="fa fa-money"></i> ' . __("purchase.view_payments") . '</a></li>';

                        if ($row->payment_status != "paid" && $is_admin) {
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->id]) . '" class="add_payment_modal"><i class="fa fa-money"></i> ' . __("purchase.add_payment") . '</a></li>';
                        }


                        $html .= '</ul></div>';
                        return $html;
                    }
                )

             ->filterColumn('user', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) like ?", ["%{$keyword}%"]);
                })
             ->editColumn('status',function ($row) use ($employee_status){

                 $html=$employee_status[$row->status];
                 return $html;

             })

                ->removeColumn('id')
                ->rawColumns(['action','user'])
                ->make(true);
        }

        $employees = [];
        if ($is_admin) {
            $employees = User::forDropdown($business_id, false);
        }
        $departments = Category::forDropdown($business_id, 'hrm_department');
        $designations = Category::forDropdown($business_id, 'hrm_designation');

        return view('essentials::employees.index')->with(compact('employees', 'is_admin', 'departments', 'designations','employee_status'));
    }



    public function employee_edit($id=null)
    {
        $business_id = request()->session()->get('user.business_id');

        if(!empty($id)) {
            $employees = User::where('users.id', $id)
                ->leftJoin('categories as dept', 'users.essentials_department_id', '=', 'dept.id')
                ->leftJoin('categories as dsgn', 'users.essentials_designation_id', '=', 'dsgn.id')
                ->select([
                    'users.*',
                    'essentials_department_id',
                    'essentials_designation_id',
                    DB::raw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as user"),
                    'dept.name as department',
                    'dsgn.name as designation'
                ])->first();
        }else{
            $employees =new User();
        }

        $departments = Category::forDropdown($business_id, 'hrm_department');
        $designations = Category::forDropdown($business_id, 'hrm_designation');
        $employee_status=['active'=>'في الخدمة',
            'inactive'=>'غير نشط',
            'terminated'=>'مفصول'];
        $html= view('essentials::employees.create',compact(['employees',  'departments', 'designations','employee_status']))->render();
        return $html;

    }


   public function employee_update(Request $request)
    {
        $output=['success'=>true,
            'msg'=>'تم حفظ البيانات بنجاح'];
        $input=$request->except('_token');
        $business_id = request()->session()->get('user.business_id');
        $input['business_id']=$business_id;

        if(empty($request->id))
        {
            if (empty($request->input('allow_login'))) {
                unset($input['username']);
                unset($input['password']);
                $input['allow_login'] = 0;
            } else {
                $input['allow_login'] = 1;

                $user_name_exit=User::where('username',$input['username'])->count();

                if($user_name_exit>0){
                    $output=['success'=>false,
                        'msg'=>'عفوا إسم المستخدم موجود'];
                    return $output;
                }

                if(empty($input['password']) || empty($input['confirm_password']) || $input['confirm_password']<>$input['password'] ){
                    $output=['success'=>false,
                        'msg'=>'عفوا يجب إدخال كلمة مرور صحيحة'];
                    return $output;
                }

                $input['password'] = $input['allow_login'] ? Hash::make($input['password']) : null;
            }

        }else {
            // Update
            if (empty($request->input('allow_login'))) {
                unset($input['username']);
                unset($input['password']);
                $input['allow_login'] = 0;
            } else {
                $input['allow_login'] = 1;

                $user_name_exit = User::where('username', $input['username'])
                    ->where('id','<>',$input['id'])->count();

                if ($user_name_exit > 0) {
                    $output = ['success' => false,
                        'msg' => 'عفوا إسم المستخدم موجود'];
                    return $output;
                }
               if(!empty($input['password'])){
                   if (empty($input['confirm_password']) || $input['confirm_password'] <> $input['password']) {
                       $output = ['success' => false,
                           'msg' => 'عفوا يجب إدخال كلمة مرور صحيحة'];
                       return $output;
                   }

                   $input['password'] = $input['allow_login'] ? Hash::make($input['password']) : null;
               }else{
                   unset($input['username']);
                   unset($input['password']);
               }
            }
        }
        $employees = User::updateorcreate(
            ['id'=>$request->id],
            $input
        );


        return $output;
    }
}
