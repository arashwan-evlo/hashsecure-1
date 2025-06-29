<?php

namespace App\Http\Controllers;

use App\Media;
use App\Models\tempInput;
use Illuminate\Http\Request;

class TempworkController extends Controller
{
    //

    public function temp_input(){
        return view('temp_work.temp_input');
    }

   public function post_temp_input(Request $request){

       $input = $request->except('_token');
       $data=tempInput::create($input);
       if ($request->hasFile('image')){
           $filename=Media::uploadFile($request->file('image'));
           $data->image=$filename;
           $data->save();
       }
       return back()->with('status', [
           'success' => 0,
           'msg' => __('lang_v1.return_exist')
       ]);


   }
}
