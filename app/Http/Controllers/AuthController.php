<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('guest')->only(['login','register']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'=>'string|required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits:11'
        ]);
        $user = User::create([
            'name'=> $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);
        
        //using sanctum to createToken
        $token = $user->createToken('key')->plainTextToken;
        return response(['user' => $user, 'token' => $token], 201);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return ['message' => 'logout successfully'];
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::where('email', $request['email'])->first();
        if(!$user)
        {
            return response(['error' => 'invalid email'],401);
        }
        
        $token = $user->createToken('key')->plainTextToken;
        return response(['user' => $user, 'token' => $token], 201);
    }
}
