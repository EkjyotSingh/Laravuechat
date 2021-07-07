@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
<form action="{{route('profile.update')}}" method="post" enctype="multipart/form-data">
    @csrf
    @method("PUT")
    <div class="form-group text-center align-items-center justify-content-center d-flex flex-column">
        <img id="preview-profile-image" src="{{asset($profile->image)}}" alt="..."  onerror="this.onerror=null; this.src=`{{asset('/images/dummy/no-person-img.jpg')}}`" class=" text-center border @error('profile_image_input') border-danger @else border-dark  @enderror" style="width:102px;height:102px;">
        <span class=" mt-4" style="cursor:pointer;font-size:24px;" onclick="document.getElementById('profile_image_input').click();"><i class="fa fa-camera" aria-hidden="true"></i></span> 
        <input type="file" class="d-none" name="profile_image_input" id="profile_image_input" onchange="imageChange()">
        @error('profile_image_input')
        <span class="text-danger mt-2" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror 
</div>
    <div class="form-group">
        <label for="exampleInputName1">Name</label>
        <input type="text" name="name" class="@error('name') is-invalid @enderror form-control" id="exampleInputName1" aria-describedby="nameHelp" placeholder="Enter name" value="{{$profile->name}}">
        @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror  
    </div>
    <div class="form-group">
      <label for="exampleInputEmail1">Email address</label>
      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" value="{{$profile->email}}">
      @error('email')
      <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
      </span>
  @enderror
    </div>
    {{--<div class="form-group">
      <label for="exampleInputPassword1">Old Password</label>
      <input type="password" namw="old_password" class="@error('old_password') is-invalid @enderror form-control" id="exampleInputPassword1" placeholder="Old Password">
      @error('old_password')
      <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
      </span>
  @enderror
    </div>
    <div class="form-group">
        <label for="exampleInputPassword2">New Password</label>
        <input type="password" name="password" class="@error('password') is-invalid @enderror form-control" id="exampleInputPassword2" placeholder="New Password">
        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror  
    </div>
      <div class="form-group">
        <label for="exampleInputPassword3">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" id="exampleInputPassword3" placeholder="Confirm Password">  
    </div>--}}
      <div class="form-group">
      <a href="{{route('home')}}" class="btn btn-secondary mr-5">Cancel</a>
    <button type="submit" class="btn btn-primary">Submit</button>
      </div>
  </form>
</div>
</div>
</div>
@endsection
@section('script')
<script>
    function imageChange() {
  const [file] = document.getElementById('profile_image_input').files
  if (file) {
    document.getElementById('preview-profile-image').src = URL.createObjectURL(file)
  }
}
</script>
@endsection
