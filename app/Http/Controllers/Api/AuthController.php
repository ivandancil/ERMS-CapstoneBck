<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255'
        ]);

        $user = User::where('email',$request->email)->first();
        
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect'
            ], 401);
        }

        $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ], 200
     );
    }

    public function register(Request $request): JsonResponse 
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|max:255'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        if ($user) 
        {
            $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;

            return response()->json([
                'message' => 'Registration successful',
                'token_type' => 'Bearer',
                'token' => $token,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ]
            ], 201);

        } 
            else 
        {
            return response()->json([
                'message' => 'Something went wrong!',
            ], 500);
        }
        
    }

    public function logout(Request $request): JsonResponse
    {
        if ($request->user()) {
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Logged out Successfully',
            ], 200);
        }

        return response()->json([
            'message' => 'User Not found',
        ], 404);
         
    }
}
