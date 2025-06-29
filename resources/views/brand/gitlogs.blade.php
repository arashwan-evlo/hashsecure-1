@extends('layouts.app')
@section('title', 'Git Hub Logs')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Git Hub logs

        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">


                <div class="table-responsive">
                    <table class="table table-bordered table-striped" >
                        <thead>
                        <tr>
                            <th>status</th>
                            <th>Result</th>
                            <th>date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $row)
                            <tr>
                                <td>{{$row->success}}</td>
                                <td>{{$row->output}}</td>
                                <td>{{$row->created_at}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>



    </section>
    <!-- /.content -->

@endsection
