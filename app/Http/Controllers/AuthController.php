<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
//
    }

public function showLoginForm(){
return view('auth.login');
}

    public function register(Request $request) {
        $rules = [
			'name' 			=> 	'required|max:50|regex:/^[a-zA-Z\\s]*$/',
			'email' 				=> 	'required|unique:users,email',
			'password' => 'required|confirmed|min:6',
		];
		$message =	[
			'password.required' =>	'Please enter password',
			'email.required'    =>	'Please enter email',
			'name.required' =>	'Please enter full name',
			'password.min'  =>	'Password must have atleast 6 characters',
            'password.confirmed'    =>	'Password and confirm password must match',
		];
		
		
		$validator 	= 	Validator::make($request->all(), $rules, $message);
		
		if ($validator->fails()) {
			$errors_object = json_decode(json_encode($validator->errors()), true);
			$error_msg_array = array();
			foreach($errors_object as $field_name => $error_array) {
				$error_msg_array[$field_name] = $error_array[0];
			}
			//if(count($error_msg_array) == 1) {
				foreach($error_msg_array as $key => $error) {
					return Response::json(array(
						'error'		=> 	true,
						'message' 	=> 	$error,
						'data' 		=> 	$error_msg_array
					), 200);
				}
			//}
			return Response::json(array(
				'error'		=> 	true,
				'message' 	=> 	"Some fields are empty or having an error",
				'data' 		=> 	$error_msg_array
			), 200);
		}
        $password = Hash::make($request->input('password'));
        try{
            $action = User::create([
                "name" 	=> 	$request->input('name'),
                "email" 		=> 	$request->input('email'),
                "password" 		=> 	$password,
            ]);
            if($action) {
                try {
                    $credentials = $request->only('email', 'password');
                    $token = Auth::attempt($credentials);
                    return response()->json([
                        'error' => false,
                        'message' => 'Signup is successfull',
                        'data' => (object)["_token" => rand()]
                    ]);
                } catch(\Exception $e) {
                    return response()->json([
                        'error' => true,
                        'message' => 'There is some error while login please try agian.',
                        'data' => (object)[]
                    ]);
                }
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'There is some error while creating user, please try after some time.',
                    'data' => (object)[]
                ]);
            }
        } catch(\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'There is some error while creating user, please try after some time.',
                'data' => (object)[$e]
            ]);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request){
    	$rules = [
			'email' 				=> 	'required|email',
			'password' 				=> 	'required|min:6',
		];
		$message =	[
			'password.required' 	=>	'Please enter password',
			'email.required' 		=>	'Please enter email',
			'password.min' 			=>	'Password must have atleast 6 characters',
		];

        $validator 	= 	Validator::make($request->all(), $rules, $message);
		
		if ($validator->fails()) {
			$errors_object = json_decode(json_encode($validator->errors()), true);
			$error_msg_array = array();
			foreach($errors_object as $field_name => $error_array) {
				$error_msg_array[$field_name] = $error_array[0];
			}
			return Response::json(array(
				'error'		=> 	true,
				'message' 	=> 	"Some fields are empty or having an error",
				'data' 		=> 	$error_msg_array
			), 200);
		}
        try{
            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid Credentials',
                    'data' => (object)[]
                ]);
            }
            return response()->json([
                'error' => false,
                'message' => 'Signin is successfull',
                'data' => (object)["_token" => rand()]
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'There is some error while login please try again',
                'data' => (object)[$e]
            ]);
        }
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return redirect(route('login'));
    }
}
