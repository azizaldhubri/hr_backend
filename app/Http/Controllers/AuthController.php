<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;

class AuthController extends Controller
{
    // Register Method

    public function Register(RegisterRequest $request)
    {
        $request->validated();
        $user = User::create([                       
            'name' => $request->name,             
            'national_id'=>'32443',
                'job_title'=>'ds4f',
                'phone_number'=>'89494',
                'birth_date'=>'2025-01-23',
                'hire_date'=>'2025-01-23',
                'nationality'=>'hgdd', 
                'department_id'=>'1', 
                'gender'=>'ذكر', 
                'employment_type'=>'كامل',
                'salary'=>'1000',
                'status'=>'activ',
                'role'=>'admin',       
                'role_id'=>'1',          
                'file_paths',
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('token')->accessToken;
        $refreshToken = $user->createToken('authTokenRefresh')->accessToken;
        return response()->json([
            'user' => $user,
            'token' => $token,

        ], 200);
    }

    // Login Method
    public function Login(LoginRequest $request)
    {
        $request->validated();
        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = $request->user();
        $token = $user->createToken('token')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    // Logout Method
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
