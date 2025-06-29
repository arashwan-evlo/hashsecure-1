<!DOCTYPE html>
<html lang="ar"  dir='rtl'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.70">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="keywords" content="برنامج حسابات, برنامج محاسبة عربي, برنامج للحسابات, برتامج حسابات عربى " />
    <meta name="description" content="برنامج محاسبة عربي أون لاين يتيح التجربة المجانية يمكنه متابعة الإيرادات والمصروفات والأرباح والخسائر من خلال حركة المبيعات والمشتريات، سجل حسابك الاًن! " />
    <meta name="google-site-verification" content="BVzYHxYJGQUVwRG6azf6Cxc4tDqskk2y2d65bvjwsP8" />
    <title>AZHA ERP</title>


    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    <link rel="icon" href="/uploads/style/azha.ico">

<style>
    html {
        scroll-behavior: smooth;
    }
</style>
</head>
<!-- onload="myFun2()" -->
<body >
<!--pass=  RCVm5uB8A6X1bh&$@DJG -->
<!-- Back to top button -->
<div class="back-to-top"></div>

<header>
    <!--  bg-primary
    navbar navbar-dark  -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky" data-offset="500">

        <div class="container">
            <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-collapse collapse" id="navbarContent">

                <ul class="navbar-nav ml-auto">

                    <li class="nav-item">
                        <a class="btn btn-primary ml-lg-2" href="/pricing">الاسعار  </a>
                    </li>

                    <li class="nav-item active">

                        <a class="nav-link" href="{{action('HomeStyleController@index')}}">الرئيسية </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">عن الشركة </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#service">الخدمات </a>
                    </li>
                   {{-- <li class="nav-item">
                        <a class=" btn btn-danger" href="{{action('Auth\LoginController@login')}}">تسجيل الدخول</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{action('HomeStyleController@index')}}">تواصل معنا</a>
                    </li>--}}

                </ul>
            </div>
            <a href="http://erp.azhasoft.com" class="navbar-brand ">AZHA<span class="text-primary">.ERP <img style="width:32px; "  src="/uploads/style/azha.ico"></img></span></a>
        </div>
    </nav>

    <div class="container">
        <div class="page-banner home-banner">
            <div class="row align-items-center flex-wrap-reverse h-100">
                <div class="col-md-6 py-5 wow fadeInLeft">
                    <h1 class="text-xl mb-4 text-primary" id="strt">الشركات الصغيرة تحتاج انطلاقة كبيرة </h1>
                    <p class="text-lg  mb-5 text-success"><span style="color: #AE0E0E;font-weight: bold">(AZHA ERP) </span>حقق أعلى المبيعات بإستخدام نظام بسيط</p>
                    <!-- <p class="text-lg text-grey mb-5">برمجيات متكاملة تلبي طموحك</p> -->
                    <a href="/login" class="btn btn-primary btn-split btn-outline-warning">تسجيل الدخول <div class="fab"><span class="mai-play"></span></div></a>
                </div>
                <div class="col-md-6 py-5 wow zoomIn">
                    <div class="img-fluid text-center">
                         <img src="/uploads/style/key-benefits-of-erp1.png" >
                    </div>
                </div>
            </div>
            <a href="http://erp.azhasoft.com" class="btn-scroll" data-role="smoothscroll"><span class="mai-arrow-down"></span></a>
        </div>
    </div>
</header>
<div class="page-section">
    <div class="container">

            <div style="max-width: 400px;
margin: auto;
border: 1px solid #D5CBCBED;
padding: 20px;
border-radius: 10px;">

                    {!! Form::open(['url' => action('TempworkController@post_temp_input'), 'method' => 'post',  'files' => true ]) !!}
                    <div class="row">
                    <div class="form-group col-sm-12">
                        {!! Form::label('name','الإسم :*') !!}
                        {!! Form::text('name', null, ['class' => 'form-control text-right', 'required', 'placeholder' => __( 'unit.name' )]); !!}
                    </div>

                    <div class="form-group col-sm-12 ">
                        {!! Form::label('address','العنوان :*') !!}
                        {!! Form::text('address', null, ['class' => 'form-control text-right', 'required', 'placeholder' =>'العنوان']); !!}
                    </div>

                    <div class="form-group col-sm-12">
                        {!! Form::label('mobile','رقم المبيل :*') !!}
                        {!! Form::text('mobile', null, ['class' => 'form-control text-right', 'required', 'placeholder' =>"رقم الموبيل "]); !!}
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('image','الصورة : ') !!}
                            {!! Form::file('image', ['id' => 'product_brochure', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                            <small>
                                <p class="help-block">
                                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                    @includeIf('components.document_help_text')
                                </p>
                            </small>
                        </div>
                    </div>
                </div>
                    <button type="submit" class="btn btn-success">حفظ </button>
                </form>
            </div>




        <div class="row">

        </div>
    </div>
</div>


<!-- ********الايكونات ********* -->
<footer class="page-footer bg-image" style="background-image: url('{{asset('uploads/style/world_pattern3.svg')}}');">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-3 py-3">
                <h3>AZHA.ERP</h3>
                <p>التواصل مع الشركة والاستفسارات ومتابعة كل جديد لا تترد في الحصول علي المعلومه ابدء معنا الان ...</p>

                <div class="social-media-button">
                    <a href="https://www.facebook.com/azhasoft" target="_blank"><img src="/uploads/style/facebook1.png"></a>
                    <a href="https://wa.me/message/2EIRSSOO3QSTP1" target="_blank"><IMG src="/uploads/style/Whatsapp-Transparent-File.png"></a>
                    <a href="http://erp.azhasoft.com" target="_blank"><IMG src="/uploads/style/instagram.png"></a>
                    <a href="http://erp.azhasoft.com" target="_blank"><IMG src="/uploads/style/youtube.png"></a>
                </div>
            </div>
            <div class="col-lg-3 py-3">
                <h5>معلومات عن البرنامج</h5>
                <ul class="footer-menu">
                    <li><a href="http://erp.azhasoft.com">الرئيسية</a></li>
                    <li><a href="http://erp.azhasoft.com">عن الشركة </a></li>
                    <li><a href="http://erp.azhasoft.com">الخدمات</a></li>
                    <li><a href="http://erp.azhasoft.com">الخدمات والشروط</a></li>
                    <li><a href="http://erp.azhasoft.com">الدعم  الفني&&والخدمات</a></li>
                </ul>
            </div>
            <div class="col-lg-3 py-3">
                <h5>إتصل بنا </h5>
                <p></p>
                <a href="http://erp.azhasoft.com" class="footer-link">
                    +201024649844 - +201024649844
                    جمهورية مصر العربية - القاهرة</a>
                <a href="http://erp.azhasoft.com" class="footer-link">sales@azhasoft.com</a>
            </div>
            <div class="col-lg-3 py-3">
                <h5>أخبار الشركة </h5>
                <p>كن علي الاطلاع دائم بكل جديد ضف بريدك الاكتروني هنا </p>
                <form action="http://erp.azhasoft.com">
                    <input type="text" class="form-control" placeholder="Enter your email..">
                    <button type="submit" class="btn btn-success btn-block mt-2">Subscribe</button>
                </form>
            </div>
        </div>
        <!-- https://erp.neqaty.com.sa/ -->
        <p class="text-center" id="copyright">Copyright &copy;2022 جمهورية مصر العربية القاهرة

            <a href="https://azhasoft.com/" target="_blank">AZHA Soft</a></p>
    </div>
</footer>


<script src="{{ asset('js/jquery-3.5.1.min.js')}}"></script>

<script src="{{ asset('js/bootstrap.bundle.min.js')}}"></script>

<script src="{{ asset('js/google-maps.js')}}"></script>

<script src="{{ asset('js/wow.min.js')}}"></script>

<script src="{{ asset('js/theme.js')}}"></script>

</body>
</html>

