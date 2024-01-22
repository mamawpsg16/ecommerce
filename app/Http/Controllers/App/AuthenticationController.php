<?php

namespace App\Http\Controllers\App;

use App\Models\User;
use App\Models\CartItem;
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

    public function getPartials(Request $request){
        $user_id = $request->user()->id;
        $cart_items_count = CartItem::where('user_id', $user_id)->count();
        return response(['cart_items_count' => $cart_items_count]);
    }
    
}
