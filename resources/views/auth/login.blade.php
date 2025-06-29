@extends('layouts.auth2_old')
@section('title', __('lang_v1.login'))

@section('content')

            <div class="" style="background-color: white; padding: 10px 30px 30px 30px;border-radius: 10px;max-width: 350px;margin: auto; margin-top: 70px;">

                <div style="text-align: center;
                    color: #FFF;
                    background-color: #31313C;
                    margin: -30px -30px 30px -30px;
                    border-radius: 10px 10px 0px 0px;
                    padding-top: 1px;
                    padding-bottom: 15px;">

                    <h3 style="color: #FFFFFF">Prosoft ERP</h3>

                </div>

               {{-- <div class="login-header" style="text-align: center;margin-bottom: 20px;">
                    <p class="form-header ">@lang('lang_v1.login')</p>
                </div>--}}

                <form method="POST" action="{{ route('login') }}" id="login-form">
                    {{ csrf_field() }}

                    {{--User name--}}
                    <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}" >
                        @php
                            $username = old('username');
                            $password = null;
                            if(config('app.env') == 'demo'){
                                $username = 'admin';
                                $password = '123456';

                                $demo_types = array(
                                    'all_in_one' => 'admin',
                                    'super_market' => 'admin',
                                    'pharmacy' => 'admin-pharmacy',
                                    'electronics' => 'admin-electronics',
                                    'services' => 'admin-services',
                                    'restaurant' => 'admin-restaurant',
                                    'superadmin' => 'superadmin',
                                    'woocommerce' => 'woocommerce_user',
                                    'essentials' => 'admin-essentials',
                                    'manufacturing' => 'manufacturer-demo',
                                );

                                if( !empty($_GET['demo_type']) && array_key_exists($_GET['demo_type'], $demo_types) ){
                                    $username = $demo_types[$_GET['demo_type']];
                                }
                            }
                        @endphp
                        <input id="username" type="text" class="form-control" name="username" value="{{ $username }}" required autofocus placeholder="@lang('lang_v1.username')">
                        <span class="fa fa-user form-control-feedback"></span>
                        @if ($errors->has('username'))
                            <span class="help-block">
                        <strong>{{ $errors->first('username') }}</strong>
                    </span>
                        @endif
                    </div>

                    {{--Password--}}
                    <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                        <input id="password" type="password" class="form-control" name="password"
                               value="{{ $password }}" required placeholder="@lang('lang_v1.password')">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        @if ($errors->has('password'))
                            <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                        @endif
                    </div>


                    <div class="form-group">
                        <div class="checkbox icheck">
                            <label style="color: #0c0c0c">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> @lang('lang_v1.remember_me')
                            </label>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-flat btn-login" style="border-radius: 10px;height: 50px;font-size: 19px;">@lang('lang_v1.login')</button>

                   </div>
                    <div class="form-group" style="padding-bottom: 9px;">
                        @if(config('app.env') != 'demo')
                            <a href="{{ route('password.request') }}" class="pull-right" style="color: #0c0c0c">
                                @lang('lang_v1.forgot_your_password')
                            </a>
                        @endif
                    </div>

                       </form>

            </div>


            @if(config('constants.allow_test'))
           <div class="" style="text-align: center;background-color: white; padding: 6px 10px 15px 10px;border-radius: 10px;max-width: 350px;margin: auto; margin-top: 70px;">
             <h3>لتجربة البرنامج يمكنك الضغط هنا </h3>
               <button type="button" class="btn btn-danger btn-flat btn-login" style="border-radius: 10px;height: 50px;font-size: 19px;" id="test" >تجربة البرنامج </button>

           </div>
       @endif

@stop



@section('javascript')
<script type="text/javascript">
   $(document).ready(function(){
       $('#change_lang').change( function(){
           window.location = "{{ route('login') }}?lang=" + $(this).val();
       });

       $('#test').click( function (e) {
          e.preventDefault();
          $('#username').val('demouser');
          $('#password').val("123456");
          $('form#login-form').submit();
       });
   })
</script>
@endsection
