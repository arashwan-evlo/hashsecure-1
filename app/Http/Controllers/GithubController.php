<?php

namespace App\Http\Controllers;

use App\Models\GithubLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class GithubController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $data=GithubLog::get();

        //return response()->json($output);

        return view('brand.gitlogs',compact(['data']));
    }

public function gitpull()
{
    $branch = 'main'; // Specify your branch
    $output = '';

    try {
        // Step 1: Fetch the latest changes from the remote
        $fetchProcess = new Process(['git', 'fetch', 'origin']);
        $fetchProcess->setWorkingDirectory(base_path());
        $fetchProcess->mustRun();

        // Step 2: Reset the branch to match the remote
        $resetProcess = new Process(['git', 'reset', '--hard', "origin/$branch"]);
        $resetProcess->setWorkingDirectory(base_path());
        $resetProcess->mustRun();

        // Get the output
        $result = $resetProcess->getOutput();
        $output= [
            'success' => true,
            'message' => 'Git reset successful.',
            'output' => $result,
        ];
    } catch (ProcessFailedException $exception) {
        // Handle failure and capture error details
        $output= [
            'success' => false,
            'message' => 'Git reset failed.',
            'output' => $exception->getMessage(),
        ];
    }


    GithubLog::create($output);

    $data=GithubLog::get();

    //return response()->json($output);

    return view('brand.gitlogs',compact(['data']));

}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
}
