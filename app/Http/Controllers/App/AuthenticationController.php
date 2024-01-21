<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Authentication\LoginRequest;

class AuthenticationController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->validated();
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $token = $request->user()->createToken('authenticate')->plainTextToken;

            return response()->json(['status'=> 200,'authentication_token' => $token, 'message' => '']);
        }else{
            return response()->json(['status'=> 404,'message' => 'Incorrect username or password, Please try again.']);
        }
    }

    // public function register(RegisterRequest $request)
    // {
    //     $user = $request->validated();
        
    //     $user = User::create([
    //         'name' => $user['name'],
    //         'email' => $user['email'],
    //         'password' => bcrypt($user['password']),
    //     ]);

    //     return response()->json(['status' => 200, 'message' =>'User Succesfully Created!']);
    // }

    public function tokenVerification(Request $request) 
    {
        $user = $request->user();
        
        $data = [
            'email'      =>  $user->email,
            'name'       =>  $user->name,
            'active'     =>  $user->active,
        ];

        return $data;
    }

    public function logout(Request $request){
        
        $request->user()->tokens()->delete();

        return response()->json(['status' => 200]);
    }
}
