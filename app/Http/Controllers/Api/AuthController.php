<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Models\User;
use App\Models\UserLog;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\EmployeeInvitation;
use App\Http\Controllers\Controller;
use App\Mail\EmployeeInvitationMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

         // ✅ Log the user login action
    UserLog::create([
        'user_id' => $user->id,
        'action' => 'User logged in',
    ]);

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
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255'
        ]);
    
        // Check if the email exists in the employees table
        $employee = Employee::where('email', $request->email)->first();
    
        if (!$employee) {
            return response()->json([
                "message" => "This email is not registered in the system."
            ], 404);
        }
    
        // Check if the user already exists
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'User with this email already registered'
            ], 409);
        }
    
        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);
    
        if ($user) {
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
    
        return response()->json([
            'message' => 'Something went wrong!',
        ], 500);
    }
    
    public function logout(Request $request)
    { 
        // Retrieve authenticated user
        $user = $request->user();  
    
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
    
        // Store user logout log
        DB::table('user_logs')->insert([
            'user_id' => $user->id,
            'action' => 'User logged out',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Delete user tokens
        $user->tokens()->delete();
    
        return response()->json([
            'status' => 200,
            'message' => 'LOGGED Out Successfully',
        ]);
    }
    

    public function getUser(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ], 200);
    }
        
}
