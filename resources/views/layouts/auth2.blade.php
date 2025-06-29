<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Prosoft">
    <meta name="author" content="Prosoft">
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="Prosoft - برنامج المحاسبة لإدارة الأنشطة التجارية"/>
    <meta property="og:site_name" content="Prosoft "/>
    <meta property="og:image" content="http://erp.prosoft/img/logo3.png"/>
    <meta property="og:description" content=" مرحبا بك في أزها سوفت للبرمجيات نحن نعمل علي بناء و تطوير المواقع وبرامج سطح المكتب"/>



    <title>@yield('title') - {{ config('app.name', 'POS') }}</title>

    @include('layouts.partials.css')
    <link rel="stylesheet" href="{{asset('assets/css/all.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" />
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body style="background-color: #317db7">
@inject('request', 'Illuminate\Http\Request')
@if (session('status'))
    <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
@endif

@yield('content')




<footer class="login_footer">كل الحقوق محفوظة 2022 &copy;</footer>
@include('layouts.partials.javascripts')

<!-- Scripts -->
<script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
<script src="{{asset('assets/js/script.js')}}"></script>
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