<?php

namespace Modules\ChartOfAccounts\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\ChartOfAccounts\Entities\JournalDocument;

class JournalDocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('chartofaccounts::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return string
     */
    public function create(Request $request)
    {
       $journal_id=$request->id;
        $html=view('chartofaccounts::journal.documents.create',compact(['journal_id']))->render();
        return $html;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

       $journal_id= $request->journal_id;
        if ($request->hasFile('document')) {
            $file = $request->file('document');

            $file_name = null;
            if ($file->getSize() <= config('constants.document_size_limit')) {
                $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $new_file_name = time() . '_' . mt_rand() . '.' . $ext;
                if ($file->storeAs('/media', $new_file_name)) {
                    $file_name = $new_file_name;
                }
            }

            $data=JournalDocument::create([
                'journal_id'=>$journal_id,
                'file_name'=>$file_name,
                'created_by'=>Auth::user()->id,
                'business_id'=>$business_id = request()->session()->get('user.business_id'),
                'name'=>$request->name,

            ]);



            $output = ['success' => true,
                'msg' => 'تم حفظ المستند بنجاح'];
        }else{
            $output = ['success' => false,
                'msg' => 'عفوا برجاء إختيار مستند'];
        }


        return $output;

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
}
