<?php

namespace Modules\Sitemanager\Http\Controllers;

use App\Models\Governorate;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Sitemanager\Entities\SlideMedia;
use function Termwind\render;

class SitemanagerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('sitemanager::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('sitemanager::create');
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
        return view('sitemanager::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('sitemanager::edit');
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


    public function media(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if($request->ajax()){

            $images=SlideMedia::where('business_id',$business_id)->orderby('order')->get();


            $output=view('sitemanager::media.slide_imgaes',compact(['images']))->render();
            return  $output;
        }


        return view('sitemanager::media.index');
    }

    public function media_edit($id=null)
    {

        if(!empty($id)){
            $data=SlideMedia::where('id',$id)->first();

        }else{
            $data=new SlideMedia();
        }

        $output=view('sitemanager::media.create',compact(['data']))->render();

        return $output;
    }

    public function media_sotre(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $input=[
                'business_id'=>$business_id,
                'order'=>$request->order,
                'title'=>$request->title,
                'status'=>$request->status,
            ];


        // Check if file

        try{
            DB::beginTransaction();
            if(!empty($request->id)){
                $data=SlideMedia::where('id',$request->id)->first();
            }
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if ($file->getSize() <= config('constants.document_size_limit')) {
                    $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                    if (!in_array($ext, ['jpg', 'png', 'web'])) {
                        $output=['success'=>0,
                            'msg'=>'الملف غير مسموح بة'
                        ];
                         return $output;
                    }
                    $file_name = time() . '_' . mt_rand() . '.' . $ext;
                    if ($file->storeAs('/media', $file_name)) {
                        $input['image_url']=$file_name;
                        if (!empty($data->image_url) && file_exists('uploads/media/' . $data->image_url)) {
                            unlink('uploads/media/'. $data->image_url);
                        }

                    }
                }
            }



            SlideMedia::updateorcreate(
                ['id'=>$request->id],
                $input);

            DB::commit();
            $output=['success'=>true,
                'msg'=>'تم حفظ الصورة بنجاح'
            ];



        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => 0,
                'msg' => __("messages.something_went_wrong")
           ];
        }





        return $output;
    }

    public function media_delete($id)
    {
       $data= SlideMedia::where('id',$id)->first();
        $data->delete();
        $output=['success'=>true,
            'msg'=>'تم حذف الصورة بنجاح'
        ];
        if (!empty($data->image_url) && file_exists('uploads/media/' . $data->image_url)) {
            unlink('uploads/media/'. $data->image_url);
        }
        return $output;
    }

    //shipping governorate

    public function shipping_governorate(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if($request->ajax()){

            $governorates=Governorate::where('business_id',$business_id)->get();


            $output=view('sitemanager::governorates.governorates',compact(['governorates']))->render();
            return  $output;
        }


        return view('sitemanager::governorates.index');
    }

    public function governorate_edit($id=null)
    {

        if(!empty($id)){
            $data=Governorate::where('id',$id)->first();

        }else{
            $data=new Governorate();
        }

        $output=view('sitemanager::governorates.create',compact(['data']))->render();

        return $output;
    }

    public function governorate_store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $price=$request->price;
        if (!is_numeric($price)) {
            $output = ['success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];

            return $output;
        }

          $input=[
            'business_id'=>$business_id,
            'name'=>$request->name,
            'price'=>$request->price,
            'status'=>$request->status,
        ];


        // Check if file

        try{
            DB::beginTransaction();
            if(!empty($request->id)){
                $data=Governorate::where('id',$request->id)->first();
            }




            Governorate::updateorcreate(
                ['id'=>$request->id],
                $input);

            DB::commit();
            $output=['success'=>true,
                'msg'=>'تم حفظ المحافظ بنجاح'
            ];



        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }





        return $output;
    }

    public function governorate_delete($id)
    {
        $data= Governorate::where('id',$id)->first();
        $data->delete();
        $output=['success'=>true,
            'msg'=>'تم حذف المحافظة بنجاح'
        ];

        return $output;
    }

    public function adddummygovernerat()
    {
        Governorate::create();
    }

}
