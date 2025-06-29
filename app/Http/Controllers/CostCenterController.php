<?php

namespace App\Http\Controllers;

use App\Account;
use App\Brands;
use App\Models\CostCenter;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        if($request->ajax()){
            $business_id = request()->session()->get('user.business_id');

            $html='';
            $main_center=CostCenter::where('business_id',$business_id)
                ->where('parent_id',0)->get();
         foreach ($main_center as $main) {
                $html .=$this->centertable($main,0);
                $main2=CostCenter::where('parent_id',$main->id)->get();
                foreach ($main2 as $main_2) {
                    $html .= $this->centertable($main_2,1);
                    $main3 = CostCenter::where('parent_id', $main_2->id)->get();
                    foreach ($main3 as $main_3) {
                        $html .= $this->centertable($main_3, 2);
                        $main4 = CostCenter::where('parent_id', $main_3->id)->get();
                        foreach ($main4 as $main_4) {
                            $html .= $this->centertable($main_4, 3);
                            $main5 = CostCenter::where('parent_id', $main_3->id)->get();
                            foreach ($main5 as $main_5) {
                                $html .= $this->centertable($main_5, 4);
                            }
                        }
                    }

                }
            }
            return $html;
     }


       return view('cost_center.index');
    }


    public function centertable($main,$level)
    {

        $colspan=9-$level;
        $html = "<tr>";
        for($i=0;$i<$level;$i++){
            $html =$html.'  <td></td>';
        }

        if($main->type==0){
            $html .='<td class="account_code"><span><i class="account-logo fa fa-folder-open"></i> </span>';
        }else{
            $html .='<td class="account_code"><span><i class="account-logo fa fa-minus"></i> </span>';
        }



             $html .='<td colspan="'.$colspan.'" > #'.$main->code.' '.$main->name.'</td>
                            <td>'.$main->description.'</td>
                            <td>';

        $html .=' <a data-href="' . action('CostCenterController@edit', [$main->id]) . '"     data-container=".brands_modal" class="btn btn-success btn-flat btn-sm cursor-pointer btn-modal">
                                    <i class="fas fa-edit"></i>
                                    '.__("messages.edit").'
                                </a>';
        $html .=' <a data-href="' . action('CostCenterController@show', [$main->id]) . '"   class="btn bg-navy btn-default   btn-flat btn-sm cursor-pointer">
                                    <i class="fas fa-eye"></i>
                                    '.__("messages.view").'
                                </a>';

        $html .=' <a  href="' . action('CostCenterController@delete', [$main->id]) . '"   class="btn btn-danger btn-sm cursor-pointer btn-modal-delete">
                                    <i class="fas fa-trash"></i>
                                    '.__("messages.delete").'
                                </a>';

        $html .='</td>
                       </tr>';


        return $html;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $center=new CostCenter();
        $parents=CostCenter::where('business_id',$business_id)
            ->where('type','0')
            ->pluck('name','id');
        return view('cost_center.create',compact(['parents','center']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('brand.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->except('__token');
            $business_id = $request->session()->get('user.business_id');
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');
            if(empty($request->parent_id))
                     $input['parent_id'] =0;
                 $id=$request->id?$request->id:0;



            $costcenter = CostCenter::updateorcreate(
                ['id'=>$id],
                $input);
            $output = ['success' => true,
                'data' => $costcenter,
                'msg' => 'تم إضافة مركز التكلفة بنجاح'
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
        $business_id = request()->session()->get('user.business_id');
        $center=CostCenter::where('id',$id)->first();

        $parents=CostCenter::where('business_id',$business_id)
            ->where('type','0')
            ->pluck('name','id');
        return view('cost_center.create',compact(['parents','center']));
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

    public function delete($id)
    {
        $output=['success'=>true,
            'msg'=>'تم حذف مركز التكلفة بنجاح'];
        $is_parent=CostCenter::where('parent_id',$id)->count();
        if($is_parent>0){
            $output=['success'=>false,
                'msg'=>'عفوا يجب حذف جميع الأفرع'];
        }else{
            CostCenter::where('id',$id)->delete();
        }


        return $output;
    }

}
