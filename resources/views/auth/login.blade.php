@extends('layouts.app')
@section('content')
@if(Route::currentRouteName() =='register')
    @php
        $active=true;
    @endphp
@endif
<section class="{{isset($active) ? 'active' : ''}}" >
    <div class="containerrr {{isset($active) ? 'active' : ''}}">
        <div class="user signinBx">
            <div class="imgBx" src=""><img src="{{asset('images/login/login1.jpg')}}"></div>
            <div class="formBx">
                <form method="POST" class="login_form" action="{{ route('login.dosignin') }}" onsubmit="return login(event)">
                    @csrf
                    <h2>Sign In</h2>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
                    <span class="error-msg"></span>
                    <input id="password" type="password" name="password" required placeholder="Password">
                    <span class="error-msg"></span>
                    <div class="remember">
                        <input class="" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label rememberlabel" for="remember">Remember Me</label>
                        <span class="error-msg"></span>
                    </div>
                    <button type="submit" class="waves-effect waves-light custom-red">Login</button>
                    <div class="svg-container">
                        <svg class="svg">
                            <circle cx="23" cy="23" r="24">

                            </circle>
                        </svg>
                    </div>
                    <a class="signup">don't have an account? <a href="javascript:void(0)" onclick="toggleForm()">Sign up.</a></a>
                </form>
            </div>
        </div>

        <div class="user signupBx">
            <div class="formBx">
                <form  method="POST" class="register_form" onsubmit="register(event)" action="{{ route('register.dosignup') }}">
                    @csrf
                    <h2>Create an account</h2>
                    <input id="reg-name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Name">
                    <span class="error-msg"></span>
                    <input id="reg-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
                    <span class="error-msg"></span>
                    <input id="reg-password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">
                    <span class="error-msg"></span>
                    <input id="password-confirm" type="password" class="" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                    <button type="submit" class="waves-effect waves-light custom-red">Sign Up</button>
                    <div class="svg-container">
                        <svg class="svg">
                            <circle cx="23" cy="23" r="24">

                            </circle>
                        </svg>
                    </div>
                    <a class="signup">Already have an account? <a href="javascript:void(0)" onclick="toggleForm()">Sign in.</a></a>
                </form>
            </div>
            <div class="imgBx" src=""><img src="{{asset('images/login/login2.jpg')}}"></div>
        </div>
    </div>
</section>
@endsection
@section('script')
<script>
function toggleForm(){
    
    var section=document.querySelector('section');
    var containerrr=document.querySelector('.containerrr');
    containerrr.classList.toggle('active');
    section.classList.toggle('active');
}
function login(event){
    event.preventDefault();
	jQuery(".loading, .custom-red").toggle();
    jQuery(".signinBx .svg-container").css('display','flex');
	$('.error-msg').text('');
	$('#email, #password').removeClass("is-invalid")
	jQuery.ajax({
		type:"post",
		data:$('.login_form').serialize(),
		url:$('.login_form').attr('action'),
		success:function(response){
			if(response.error == true) {
				if(response.data.email) {
					$('#email~.error-msg').text(response.data.email)
					$('#email').addClass("is-invalid")
				}
				if(response.data.password) {
					$('#password~.error-msg').text(response.data.password)
					$('.password-error').append('<span class="text-danger error">'+response.data.password+'</span>')
					$('#password').addClass("is-invalid")
				}
				if(jQuery.isEmptyObject(response.data)) {
					$('#email~.error-msg').text(response.message)
					$('#email, #password').addClass("is-invalid")
				}
			} else {
				if(response.data._token == false) {
					$('.login_form').append('<span class="text-danger error">Please enter correct credentials</span>')
				} else {
                    window.location.href="{{route('home')}}";
				}
			}
            jQuery(".signinBx .svg-container").css('display','none');
			jQuery(".loading, .custom-red").toggle();
            
		}
	});
	return false;
}

function register(event){
    event.preventDefault();
	jQuery(".loading, .custom-red").toggle();
    jQuery(".signupBx .svg-container").css('display','flex');
	$('.error-msg').text('');
	$('#reg-email, #reg-password','#reg-name').removeClass("is-invalid")
	jQuery.ajax({
		type:"post",
		data:$('.register_form').serialize(),
		url:$('.register_form').attr('action'),
		success:function(response){
			if(response.error == true) {
                if(response.data.name) {
					$('#reg-name~.error-msg').text(response.data.name)
					$('#reg-name').addClass("is-invalid")
				}
				if(response.data.email) {
					$('#reg-email~.error-msg').text(response.data.email)
					$('#reg-email').addClass("is-invalid")
				}
				if(response.data.password) {
					$('#reg-password~.error-msg').text(response.data.password)
					$('.reg-password-error').append('<span class="text-danger error">'+response.data.password+'</span>')
					$('#reg-password').addClass("is-invalid")
				}
				if(jQuery.isEmptyObject(response.data)) {
					$('#reg-email~.error-msg').text(response.message)
					$('#reg-email, #reg-password, #reg-name').addClass("is-invalid")
				}
			} else {
				if(response.data._token == false) {
					$('.register_form').append('<span class="text-danger error">Please enter correct credentials</span>')
				} else {
                    window.location.href="{{route('home')}}";			
                }
			}
            jQuery(".signupBx .svg-container").css('display','none');
			jQuery(".loading, .custom-red").toggle();
		}
	});
	return false;
}
</script>
@endsection
@section('style')
<style>
    .svg-container{
    display:none;
    justify-content:center;
    }
    .svg{
        width:55px;
        height:55px;
        position:relative;
        animation:animaterotate 2s linear infinite;
    }
    @keyframes animaterotate{
        0%{
            transform:rotate(0deg);
        }
        100%{
            transform:rotate(360deg)
        }
    }
    .svg circle{
        width:100%;
        height:100%;
        fill:none;
        stroke-width:3;
        stroke:#00a1ff;
        transform:translate(5px,5px);
        stroke-dasharray:150;
        stroke-dashoffset:150;
        animation:animateoffset 2s linear infinite;
    }
    @keyframes animateoffset{
        0%,100%{
            stroke-dashoffset:150;
        }
        50%{
            stroke-dashoffset:0;
        }
        50.1%{
            stroke-dashoffset:300;
        }
    }
    .error-msg{
        font-size:13px;
        color:#f71414;
    }
section{
    position:relative;
    min-height:100vh;
    background:#557085;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
    transition:0.5s;
}
section.active{
    background:#3e3f39;
}
section .containerrr{
    position:relative;
    width:800px;
    height:500px;
    background:#fff;
    box-shadow:0 15px 50px rgba(0,0,0,0.1);
    overflow:hidden;
}
section .containerrr .user{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%;
    display:flex;
}
section .containerrr .user .imgBx{
    position:relative;
    width:50%;
    height:100%;
    transition:0.5s;
}
section .containerrr .user .imgBx img{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%;
    object-fit:cover;
}
section .containerrr .user .formBx{
    position:relative;
    width:50%;
    height:100%;
    background:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px;
    transition:0.5s;
}
section .containerrr .user .formBx h2{
    font-size:18px;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:2px;
    text-align:center;
    width:100%;
    margin-bottom:10px;
    color:#555;
}
section .containerrr .user .formBx input[type="text"],
section .containerrr .user .formBx input[type="email"],
section .containerrr .user .formBx input[type="password"]{
    width:100%;
    padding:10px;
    background:#f5f5f5;
    color:#333;
    /*border:none;
    outline:none;
    box-shadow:none;*/
    font-size:14px;
    margin:8px 0px;
    letter-spacing:1px;
    font-weight:300;
}
section .containerrr .user .formBx .remember{
    margin:8px 0px;
    display:flex;
    align-items:center;
}
section .containerrr .user .formBx input[type="checkbox"]{
    margin-right:10px;
}
section .containerrr .user .formBx .rememberlabel{
    font-size:13px;
}
section .containerrr .user .formBx button{
    max-width:100px;
    background:#677eff;
    color:#fff;
    font-size:14px;
    font-weight:500;
    letter-spacing:1px;
    transition:0.5s;
    padding:10px 20px;
    margin-top:10px;
    display:block;
}
section .containerrr .user.signupBx .formBx button{
    background:#e73e49;
}
section .containerrr .user .formBx .signup{
    position:relative;
    margin-top:20px;
    font-size:12px;
    letter-spacing:1px;
    color:#555;
    text-transform:uppercase;
    font-weight:300;
    text-decoration:none;
    margin-top: 16px;
    display:inline-block;
}
section .containerrr .user .formBx .signup a{
    font-weight:600;
    text-decoration:none;
    color:#577eff;
}
.signupBx{
    display:none;
}
section .containerrr .signupBx {
    pointer-events:none;
}
section .containerrr.active .signupBx {
    pointer-events:initial;
}
section .containerrr .signupBx .formBx{
    top:100%;
}
section .containerrr.active .signupBx .formBx{
    top:0;
}
section .containerrr .signupBx .imgBx{
    top:-100%;
    transition:0.5s;
}
section .containerrr.active .signupBx .imgBx
{
        top:0;
}



section .containerrr .signinBx .formBx{
    top:0;
}
section .containerrr.active .signinBx .formBx{
    top:100%;
}
section .containerrr .signinBx .imgBx{
    top:0;
    transition:0.5s;
}
section .containerrr.active .signinBx .imgBx
{
        top:-100%;
}

@media (max-width: 991px){
    section .containerrr{
        max-width:400px;
    }
    section .containerrr .imgBx{
        display:none;
    }
    section .containerrr .user .formBx{
        width:100%;
    }
    section .containerrr.active .signinBx .formBx{
        top:-100%;
    }
}
</style>
@endsection
