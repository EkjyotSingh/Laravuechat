<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    {{--<meta name="viewport" content="width=device-width, initial-scale=1">--}}
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="//stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script>
        const ls = localStorage.getItem("selected");
let selected = false;
var list = document.querySelectorAll(".list"),
    content = document.querySelector(".content"),
    input = document.querySelector(".message-footer input"),
    open = document.querySelector(".open a");

//init
function init() {
  //input.focus();
  let now = 2;
  const texts = ["İyi akşamlar", "Merhaba, nasılsın?",
                "Harikasın! :)", "Günaydın", "Tünaydın",
                "Hahaha", "Öğlen görüşelim.", "Pekala"];
  for(var i = 4; i < list.length; i++) {
    list[i].querySelector(".time").innerText = `${now} day ago`;
    list[i].querySelector(".text").innerText = texts[(i-4) < texts.length ? (i-4) : Math.floor(Math.random() * texts.length)];
    now++;
  }
}
init();

//process
function process() {
  if(ls != null) {
    selected = true;
    click(list[ls], ls);
  }
  if(!selected) {
    click(list[0], 0);
  }

  list.forEach((l,i) => {
    l.addEventListener("click", function() {
      click(l, i);
    });
  });
  try {
    document.querySelector(".list.active").scrollIntoView(true);
  }
  catch {}
  
}
process();


    </script>
<style>
    * {
  margin: 0;
  padding: 0;
  border: 0;
  box-sizing: border-box;
  font: 16px sans-serif;
}

:focus {
  outline: 0;
}

a {
  text-decoration: none;
}

body {
  background: #F4F7F9;
}

html, body {
  height: 100% !important;
}

.containerr {
  display: flex;
  height: 100% !important;
}

sidebar {
  width: 300px;
  min-width: 300px;
  display: flex;
  background: #fff;
  flex-direction: column;
  border-right: 1px solid #ccc;
  transition: 500ms all;
  z-index: 99;
}
.unread{
    background:rgb(220 215 52 / 25%) !important;
}
sidebar .logo {
  display: flex;
  margin: 10px 0 0 0;
  padding-bottom: 10px;
  align-items: center;
  justify-content: center;
  color: #000;
  font-size: 3em;
  letter-spacing: 7px;
  border-bottom: 1px solid #ccc;
}
sidebar .list-wrap {
  width: 100%;
  overflow: auto;
  height:calc( 100vh - 126px );
  max-height:calc( 100vh - 126px );
}
sidebar .list-wrap .list {
  border-bottom: 1px solid #ccc;
  background: #fff;
  display: flex;
  align-items: center;
  padding: 5px;
  height: 70px;
  cursor: pointer;
}
sidebar .list-wrap .list:hover, sidebar .list-wrap .list.active {
  background: #F4F7F9;
}
sidebar .list-wrap .list img {
  border-radius: 50%;
  width: 50px;
  height: 50px;
  object-fit: cover;
  margin-right: 10px;
  border:1px solid #0000001c;
  /*box-shadow: 1px 2px 3px rgba(0, 0, 0, 0.5);*/
}
sidebar .list-wrap .list .info {
  flex: 1;
}
sidebar .list-wrap .list .info .user {
  font-weight: 700;
}
sidebar .list-wrap .list .info .text {
  display: flex;
  margin-top: 3px;
  font-size: 0.85em;
}
sidebar .list-wrap .list .time {
  margin-right: 5px;
  margin-left: 5px;
  font-size: 0.75em;
  color: #a9a9a9;
}
sidebar .list-wrap .list .count {
  font-size: 0.75em;
  background: #bde2f7;
  box-shadow: 0 5px 15px -5px rgba(0, 0, 0, 0.7);
  padding: 3px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  text-align: center;
  color: #000;
}

.nomessageselected{
    display: flex;
    height: 100%;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}
.no-message-selected{
    color: #d0d0d0;
    margin-top: 30px;
    font-weight: 400;
    font-size: 16px;
    line-height: 18px;
}
.no-message-found{
    color: #999;
    display: block;
    font-size: 18px;
    line-height: 20px;
    font-style: normal;
    margin-top: 30px;
}
.content {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.content header {
  height: 76px;
  background: #fff;
  border-bottom: 1px solid #ccc;
  display: flex;
  padding: 10px;
  align-items: center;
}
.content header img {
  border-radius: 50%;
  width: 50px;
  height: 50px;
  object-fit: cover;
  margin-right: 10px;
    border:1px solid #0000001c;
  /*box-shadow: 1px 2px 3px rgba(0, 0, 0, 0.5);*/
}
.content header .info {
    display: flex;
    justify-content: space-between;
    align-items: center;
  flex: 1;
}
.content header .info .user {
  font-weight: 700;
}
.content header .info .time {
  display: flex;
  margin-top: 3px;
  font-size: 0.85em;
}
.content header .open {
  display: none;
}
.content header .open a {
  color: #000;
  letter-spacing: 3px;
}

.message-wrap {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 15px;
  overflow: auto;
  height: calc( 100vh - 186px );
  max-height: calc( 100vh - 186px );
}
/*.message-wrap::before {
  content: "";
  margin-bottom: auto;
}*/
.message-wrap .message-list {
  align-self: flex-start;
  max-width: 80%;
  margin-bottom:30px;
}
.message-list .time {
    font-size: 11px!important;
}
.message-list .time span {
    font-size: 11px!important;
}
.message-wrap .message-list.me {
  align-self: flex-end;
  /*min-height:85px;*/
}
.message-wrap .message-list.me .msg {
  background: #bde2f7;
  color: #111;
}
.message-wrap .message-list .msg {
    white-space:pre-wrap;
    word-break: break-word;
  background: #fff;
  box-shadow: 0 5px 15px -5px rgba(0, 0, 0, 0.1);
  padding: 7px 5px;
  margin-bottom: 10px;
  border-radius: 5px;
}
.message-wrap .message-list .time {
  text-align: right;
  color: #999;
  font-size: 0.75em;
}

.message-footer {
  border-top: 1px solid #ddd;
  background: #eee;
  padding: 10px;
  display: flex;
  height: 60px;
  /*position:sticky;*/
}
.message-footer input {
  flex: 1;
  padding: 0 20px;
  border-radius: 5px;
}
.offerer .msg{
    border-bottom-left-radius: 0px!important;
}
.me .msg{
    border-bottom-right-radius: 0px!important;
}

/*//// Delete Modal css*/
.delete_button {
  background-color: transparent;
  font-size: 22px;
  padding: 10px 9px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  opacity: 0.9;
}

.delete_button:hover {
  opacity:1;
}

/* Float cancel and delete buttons and add an equal width */
.cancelbtn, .deletebtn {
  float: left;
  padding: 10px 14px;
  color:white !important;
  cursor:pointer;
}

/* Add a color to the cancel button */
.cancelbtn {
  background-color: #ccc;
}

/* Add a color to the delete button */
.deletebtn {
  background-color: #94C2ED;
}



/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 999; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: #474e5dd4;
    justify-content: center;
    align-items: center;
}
.modal-container{
    text-align: center;
    padding: 18px;
}
/* Modal Content/Box */
.modal-content {
  background-color: #fefefe;
  margin: auto auto auto auto; /* 5% from the top, 15% from the bottom and centered */
  border: 1px solid #888;
  width: 40%; /* Could be more or less, depending on screen size */
}

/* Style the horizontal ruler */
hr {
  border: 1px solid #f1f1f1;
  margin-bottom: 25px;
}

.modal-clearfix {
    display: flex;
    padding: 0px 37px;
    justify-content: space-between;
}
@media only screen and (max-width: 480px), only screen and (max-width: 767px) {
    .modal-content {
  width: 80%; /* Could be more or less, depending on screen size */
}
  sidebar {
    position: absolute;
    width: 100%;
    min-width: 100%;
    height: 0vh;
    bottom: 0;
    box-shadow: 0 5px 25px -5px black;
  }
  sidebar.opened {
    /*height: 70vh !important;*/
    height: calc( 100vh - 126px ) !important;
  }
  sidebar .logo {
    display: none;
  }
  sidebar .list-wrap .list .count {
    font-size: 0.75em;
  }

  header .open {
    display: block !important;
    line-height: 2;
  }
}    
.single-message-delete-container{
    display: flex;
    align-items: center;
    justify-content: flex-end;
}
.single-message-delete-button-left{
    margin-right: 10px;
    color:red;
    cursor:pointer;
}
.single-message-delete-button-right{
    margin-left: 10px;
    color:red;
    cursor:pointer;
}
.latest-message{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis!important;
    width: 157px;
    display: block!important;
}
.btnsendmsg{
    width: 51px;
    padding-left: 10px;
    color: rgb(146 203 241);
    font-size: 35px;
}
.unread_tick{
    font-size: 13px;
}
.read_tick{
    color:#07bf07;
    font-size: 13px;
}
.fa-cog{
    color:#17a5f5;
    font-size:22px;
    cursor:pointer;
    margin-left:13px;
    display:none;
}
@media only screen and (max-width: 320px){
    .latest-message{
    width: 185px;
}
}
@media only screen and (min-width: 370px){
    .latest-message{
    width: 227px;
}
}
@media only screen and (min-width: 450px){
    .latest-message{
    width: 157px;
}
}
@media only screen and (min-width: 767px){
    .navbar{
    display: none;
}
.fa-cog{
    display:inline-block;
}
.message-wrap {
    height: calc( 100vh - 136px );
    max-height: calc( 100vh - 136px );
}
sidebar .list-wrap {
    width: 100%;
    overflow: auto;
    height: calc( 100vh - 76px );
    max-height: calc( 100vh - 76px );
}
}
li.nav-item.dropdown {
    z-index: 999;
}

</style>
@yield('style')
</head>
<body>
    @if(\Auth::check())
    {{--<div id="app">--}}
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{route('profile.edit')}}">Profile Update</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    @endif
            <div id="app">
                @if(Session::has('Success'))

                <div class="container alert alert-success alert-dismissible fade show" role="alert" style="position: fixed;right: 0;left: 0;top: 45px;z-index: 9999;">
                    <strong class="text-success">{{Session::get('Success')}}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                @endif
                @yield('content')
            </div>
    
            @yield('script')
    @if(\Auth::check())
   
    <script>
        var authuser = @JSON(auth()->user());
        var noPersonImage = "{{asset('images/dummy/no-person-img.jpg')}}";
        var noMessageSelected = "{{asset('images/dummy/no-message-select-img.png')}}";
        var noMessageFound = "{{asset('images/dummy/no-message-record.png')}}";
        var appName = "{{config('app.name')}}";
        var logout = "{{route('logout')}}";
        var profileUpdate = "{{route('profile.edit')}}";
        var profileImagePath = "{{asset('storage')}}";
    </script>
    @endif
</body>
</html>
