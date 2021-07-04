<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    
    public function profile_edit(){
        return view('profile-update')->with('profile',auth()->user());
    }
    public function profile_update(Request $request){
        $rules=[
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.auth()->user()->id,
            'profile_image_input' => 'mimes:jpeg,jpg,png',
        ];
        $message=[
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is not valid',
            'profile_image_input.mimes' => 'Supported image formats are jpeg,jpg,png',
        ];
        $this->validate($request,$rules,$message);
        $data=[
            'name' => $request->name,
            'email' => $request->email,
        ]; 
        if($request->has('profile_image_input')){
            if(auth()->user()->image){
                Storage::delete(auth()->user()->image);
            }
            $image=$request->profile_image_input->store('images/profile','public');
            $data['image']=$image;
        }
        $user= User::where('id',auth()->user()->id)->first();
        $user->update($data);
        Session::flash('Success','Profile Updated successfully');
        return redirect(route('home'));

    }
}
