<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
    
        if ($employees->isEmpty()) {
            return response()->json([]); 
        }
    
        return response()->json([
            'message' => $employees->isEmpty() ? 'No employees found' : 'Employees retrieved successfully',
            'data' => EmployeeResource::collection($employees),
        ], 200);
    }
    

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employeeID'   => 'required|string|max:50|unique:employees,employeeID',
            'lastname'     => 'required|string|max:255',
            'firstname'    => 'required|string|max:255',
            'middlename'   => 'nullable|string|max:255',
            'sex'          => 'required|in:Male,Female,Other', 
            'dateOfBirth'  => 'required|date',
            'civilStatus'  => 'required|string|in:Single,Married,Divorced,Widowed',
            'phoneNumber'  => 'required|string|max:15|regex:/^[0-9+\-\s]+$/', 
            'email'        => 'required|email|max:255|unique:employees,email',
            'address'      => 'required|string',
            'jobPosition'  => 'required|string|max:255',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $validator->messages(),
            ],  422);
        } 

        $employee = Employee::create([
            'employeeID'   => $request->employeeID,
            'lastname'     => $request->lastname,
            'firstname'    => $request->firstname,
            'middlename'   => $request->middlename ?? null, 
            'sex'          => $request->sex,
            'dateOfBirth'  => $request->dateOfBirth,
            'civilStatus'  => $request->civilStatus,
            'phoneNumber'  => $request->phoneNumber,
            'email'        => $request->email,
            'address'      => $request->address,
            'jobPosition'  => $request->jobPosition,
        ]);

        return response()->json([
            'message' => 'Employee Added Successfully',
            'data' => new EmployeeResource($employee),
        ],  200);
    }

    public function show(Employee $employee)
    {
        return new EmployeeResource($employee);
    }

    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'employeeID' => 'required|string|max:50|unique:employees,employeeID,' . $employee->id,
            'lastname'     => 'required|string|max:255',
            'firstname'    => 'required|string|max:255',
            'middlename'   => 'nullable|string|max:255',
            'sex'          => 'required|in:Male,Female,Other', 
            'dateOfBirth'  => 'required|date',
            'civilStatus'  => 'required|string|in:Single,Married,Divorced,Widowed', 
            'phoneNumber'  => 'required|string|max:15|regex:/^[0-9+\-\s]+$/', 
            'email' => 'required|email|max:255|unique:employees,email,' . $employee->id,
            'address' => 'required|string',
            'jobPosition'  => 'required|string|max:255',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'All fields are mandatory',
                'errors' => $validator->messages(),
            ],  422);
        } 

        $employee->update([
            'employeeID'   => $request->employeeID,
            'lastname'     => $request->lastname,
            'firstname'    => $request->firstname,
            'middlename'   => $request->middlename ?? null, 
            'sex'          => $request->sex,
            'dateOfBirth'  => $request->dateOfBirth,
            'civilStatus'  => $request->civilStatus,
            'phoneNumber'  => $request->phoneNumber,
            'email'        => $request->email,
            'address'      => $request->address,
            'jobPosition'  => $request->jobPosition,
        ]);

        return response()->json([
            'message' => 'Employee Updated Successfully',
            'data' => new EmployeeResource($employee),
        ],  200);
    }

    public function destroy(Employee $employee)
    {
        // Try to find the user based on employee's email
        $user = \App\Models\User::where('email', $employee->email)->first();
    
        if ($user) {
            $user->delete(); // or $user->forceDelete() if you're using soft deletes
        }
    
        $employee->delete();
    
        return response()->json([
            'message' => 'Employee and corresponding user deleted successfully',
        ], 200);
    }
    

    public function getLoggedInEmployee(Request $request): JsonResponse
    {
        // Get the authenticated user's email
        $userEmail = $request->user()->email;

        // Find the employee with the same email
        $employee = Employee::where('email', $userEmail)->first();

        // If the employee is not found, return an error response
        if (!$employee) {
            return response()->json([
                'message' => 'Employee record not found.'
            ], 404);
        }

        return response()->json([
            'employee' => $employee
        ], 200);
    }

    public function getTotalEmployees()
{
    $count = Employee::count();
    return response()->json(['total' => $count]);
}

}
