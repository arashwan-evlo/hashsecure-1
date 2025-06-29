<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="ERP Prosoft">
    <meta name="author" content="ERP Prosoft">
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="ERP Prosoft - برنامج المحاسبة لإدارة الأنشطة التجارية"/>
    <meta property="og:site_name" content="ERP Prosoft "/>
    <meta property="og:image" content="https://www.erp.prosofteg.com/img/Prosoft_Logo.svg"/>
    <meta property="og:description" content=" مرحبا بك في Prosoft  للبرمجيات نحن نعمل علي بناء و تطوير المواقع وبرامج سطح المكتب"/>




    <title>@yield('title') - {{ config('app.name', 'POS') }}</title> 

    @include('layouts.partials.css')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body style="background-color: #ffffff">
    @inject('request', 'Illuminate\Http\Request')
    @if (session('status'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
    @endif
    <div class="container-fluid">
        <div class="row eq-height-row">
            <div class="col-md-5 col-sm-5 hidden-xs left-col eq-height-col" >
                <img src="/img/Prosoft_Logo.svg"  class="img-responsive" alt="Logo">

                <div class="left-col-content login-header"> 
                    <div style="margin-top: 50%;display: none" >
                    <a href="/">
                    @if(file_exists(public_path('uploads/logo.png')))
                        <img src="/uploads/logo.png" class="img-rounded" alt="Logo" width="150">
                    @else
                       {{ config('app.name', 'أزها سوفت') }}
                    @endif
                    </a>
                    <br/>
                    @if(!empty(config('constants.app_title')))
                        <small style="color:#960a27">{{config('constants.app_title')}}</small>
                    @endif
                    </div>
                </div>
            </div>
            <div class="col-md-7 col-sm-7 col-xs-12 right-col eq-height-col">
                <div class="row">
                    <div class="col-md-5 col-xs-6" style="text-align: left;">
                        <select class="form-control " id="change_lang" style="margin: 10px;min-width: 140px">
                        @foreach(config('constants.langs') as $key => $val)
                            <option value="{{$key}}"
                                @if( (empty(request()->lang) && config('app.locale') == $key)
                                || request()->lang == $key)
                                    selected
                                @endif
                                 >
                                {{$val['full_name']}}
                            </option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-md-7 col-xs-12" style="text-align: right;padding-top: 10px;">
                        @if(!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                            <!-- Register Url -->
                            @if(config('constants.allow_registration'))
                                <a href="{{ route('business.getRegister') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif" class="btn  btn-flat" ><b>{{ __('business.not_yet_registered')}}</b> {{ __('business.register_now') }}</a>
                                <!-- pricing url -->
                                {{--@if(Route::has('pricing') && config('app.env') != 'demo' && $request->segment(1) != 'pricing')
                                    &nbsp; <a href="{{ action('\Modules\Superadmin\Http\Controllers\PricingController@index') }}">@lang('superadmin::lang.pricing')</a>
                                @endif--}}
                            @endif
                        @endif
                        @if($request->segment(1) != 'login')
                            &nbsp; &nbsp;<span class="text-white">{{ __('business.already_registered')}} </span><a href="{{ action('Auth\LoginController@login') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif">{{ __('business.sign_in') }}</a>
                        @endif
                    </div>

                    <div class="col-md-12 col-xs-12">
                        @yield('content')
                    </div>
                 </div>


            </div>
        </div>




    </div>




    
    @include('layouts.partials.javascripts')
    
    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    
    @yield('javascript')

    <script type="text/javascript">
        $(document).ready(function(){
            $('.select2_register').select2();

            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
</body>

</html>