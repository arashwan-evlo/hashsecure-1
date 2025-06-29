@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | Business')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang( 'superadmin::lang.all_business' )
            <small>@lang( 'superadmin::lang.manage_business' )</small>
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
         <div class="box box-solid">
            <div class="box-header">
             </div>

            <div class="box-body">
                @can('superadmin')
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" >
                            <thead>
                            <tr>
                            <td>#</td>
                           <td>Type</td>
                           <td>description</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $row)
                                <tr>
                                    <td>{{$row->id}}</td>
                                    <td>{{$row->type}}</td>
                                    <td>{{$row->description}}</td>

                                </tr>

                            @endforeach

                            </tbody>
                        </table>
                    </div>
                @endcan
            </div>
        </div>

    </section>
    <!-- /.content -->

@endsection

@section('javascript')

    <script type="text/javascript">

    </script>

@endsection