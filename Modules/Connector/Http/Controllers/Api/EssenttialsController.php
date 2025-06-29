<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\User;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;
use Modules\Essentials\Entities\EssentialsAttendance;

class EssenttialsController extends ApiController
{

    protected $commonUtil;

    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('connector::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('connector::create');
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

    public function get_attendce ()
    {
        try {
            $user = Auth::user();
            $users = User::where('users.id',6)
                ->join('essentials_attendances','essentials_attendances.user_id','users.id')
                ->get();
            return CommonResource::collection($users);
        }catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
            return $this->respond($output);
        }


    }

public function set_attendance(Request $request)
    {
        try {
            $user = Auth::user();
            $today=$request->today;
            $clock_in_time=$request->clock_in_time;
            if(!empty($request->user)){
                $user_id=$request->user;
            }else{
                $user_id=$user->id;
            }
            $shift=EssentialsAttendance::where('user_id',$user_id)
                  ->where('shift_date',$today)
                  ->where('status',0)->first();
            if(!empty($shift)){
             $shift->clock_in_time=$clock_in_time;
                $shift->status=1;
             $shift->save();
                $output = ['success' => 1,
                    'msg' =>'you attendance has ben set Successfully at: '.$today.' '.$clock_in_time.' For user: '.$user->first_name,
                ];
                return $this->respond($output);

            }else{
                $output = ['success' => 0,
                    'msg' =>'No Shift Found please check your time line in: '.' '.$clock_in_time.' For user: '.$user->first_name,
                ];
                return $this->respond($output);

            }
            $users = User::where('users.id',6)
                ->join('essentials_attendances','essentials_attendances.user_id','users.id')
                ->get();
            return CommonResource::collection($users);
        }catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
            return $this->respond($output);
        }

    }
}
