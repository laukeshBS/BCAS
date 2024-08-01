@extends('admin.auth.auth_master')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
@section('auth_title')
    Login | Admin Panel
@endsection

@section('auth-content')
     <!-- login area start -->
     <div class="login-area">
        <div class="container">
            <div class="login-box ptb--100">
                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf
                    <div class="login-form-head">
                        <h4>Sign In</h4>
                        <p>Hello there, Sign in and start managing your Admin Panel</p>
                    </div>
                    <div class="login-form-body">
                        @include('admin.layouts.partials.messages')
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Email address or Username</label>
                            <input type="text" id="exampleInputEmail1" name="email">
                            <i class="ti-email"></i>
                            <div class="text-danger"></div>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" id="exampleInputPassword1" name="password">
                            <i class="ti-lock"></i>
                            <div class="text-danger"></div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="password" class="col-md-4 control-label">Captcha</label>
                            <div class="col-md-12">
                                <div class="form-gp">
                                    <div class="captcha">
                                        <span>{!! captcha_img() !!}</span>
                                        <i class="btn-refresh fa fa-refresh"></i>
                                    </div>
                                </div>
                                <div class="form-gp">
                                    <label for="exampleInputchptcha">Enter Captcha</label>
                                    <input id="captcha" type="text"  name="captcha">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4 rmber-area">
                            <div class="col-12">
                                <div class="custom-control custom-checkbox mr-sm-2">
                                    <input type="checkbox" class="custom-control-input" id="customControlAutosizing" name="remember">
                                    <label class="custom-control-label" for="customControlAutosizing">Remember Me</label>
                                </div>
                            </div>
                        </div>
                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit">Sign In <i class="ti-arrow-right"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(".btn-refresh").click(function(){
            $.ajax({
                type: 'GET',
                url: '{{ url('/refresh_captcha') }}',
                success: function(data){
                    $(".captcha span").html(data.captcha);
                }
            });
        });

        var key = "{{ session('bsrandom') }}";

        var CryptoJSAesJson = {
            stringify: function (cipherParams) {
                var j = { ct: cipherParams.ciphertext.toString(CryptoJS.enc.Base64) };
                if (cipherParams.iv) j.iv = cipherParams.iv.toString();
                if (cipherParams.salt) j.s = cipherParams.salt.toString();
                return JSON.stringify(j);
            },
            parse: function (jsonStr) {
                var j = JSON.parse(jsonStr);
                var cipherParams = CryptoJS.lib.CipherParams.create({
                    ciphertext: CryptoJS.enc.Base64.parse(j.ct)
                });
                if (j.iv) cipherParams.iv = CryptoJS.enc.Hex.parse(j.iv);
                if (j.s) cipherParams.salt = CryptoJS.enc.Hex.parse(j.s);
                return cipherParams;
            }
        };

        document.querySelector("form").addEventListener("submit", function(e) {
            e.preventDefault();
           
            const passwordInput = this.querySelector("input[type=password]");
            const password = passwordInput.value;
            passwordInput.value = CryptoJS.AES.encrypt(JSON.stringify(password), key, {
                format: CryptoJSAesJson
            }).toString();
            this.submit();
        });
        
    </script>
    <!-- login area end -->
@endsection
