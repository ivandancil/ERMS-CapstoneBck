<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
    
        if ($users->isEmpty()) {
            return response()->json([]); // Return an empty array if no users found
        }
    
        return response()->json(UserResource::collection($users)); // Use UserResource for all users
    }
    

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
          
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email',
            'password' => 'required|string|min:8|confirmed', // Validates that password is required, a string, at least 8 characters long, and confirmed
            'role' => 'required|in:admin,user', // Validates that the role is required and must be either 'admin' or 'user'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $validator->messages(),
            ],  422);
        } 

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password before saving it
            'role' => $request->role, 
        ]);

        return response()->json([
            'message' => 'System User Added Successfully',
            'data' => new UserResource($user),
        ],  200);
    }
    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email,' . $employee->id,
            'password' => Hash::make($request->password), // Hash the password before saving it
            'role' => $request->role, 
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'All fields are mandatory',
                'errors' => $validator->messages(),
            ],  422);
        } 

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password before saving it
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'User Updated Successfully',
            'data' => new UserResource($user),
        ],  200);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'User Deleted Successfully',
        ],  200);
    }

    public function user(Request $request)
{
    return response()->json([
        'id' => $request->user()->id,
        'name' => $request->user()->name,
        'email' => $request->user()->email,
        'role' => $request->user()->role,
        'job_position' => $request->user()->job_position ?? 'N/A', // Ensure job_position is included
    ]);
}


}
