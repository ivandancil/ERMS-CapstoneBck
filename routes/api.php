<?php

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\TrainingController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeaveRequestController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Http\Controllers\Api\TrainingParticipantController;


    // Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // USER Routes - Only access own leave requests
    Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
    Route::get('/my-leave-requests', [LeaveRequestController::class, 'userRequests']);
    Route::put('/my-leave-requests/{id}', [LeaveRequestController::class, 'updateUserRequest']);
    
    // Get logged-in Employee info
    Route::get('/employee', [EmployeeController::class, 'getLoggedInEmployee']);

    // ADMIN Routes - Protected with isAdmin
    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {

       
          // Get logged-in user info
    Route::get('/user', [AuthController::class, 'getUser']);

      

     
    });
});

    // View all leave requests
    Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
    Route::put('/leave-requests/{id}', [LeaveRequestController::class, 'update']);
    Route::delete('/leave-requests/{id}', [LeaveRequestController::class, 'destroy']); // Admin/User

      // Payroll Routes
    Route::post('/payroll/upload', [PayrollController::class, 'uploadPayroll']);
    Route::get('/payroll/pending', [PayrollController::class, 'getPendingPayrolls']);
    Route::put('/payroll/update-status/{id}', [PayrollController::class, 'updatePayrollStatus']);
    Route::get('/payroll/view/{id}', [PayrollController::class, 'viewPayroll']);
    Route::get('/payroll/file/{filename}', function ($filename) {
        $filePath = "payrolls/$filename";
    
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json(['error' => 'File not found.'], 404);
        }
    
        return response()->json([
            'file_url' => asset("storage/$filePath"),
        ]);
    });

   // Admin-only Resources
   Route::apiResource('employees', EmployeeController::class);
   Route::get('/employees/count', function () {
    $count = Employee::count();

    return response()->json([
        'status' => 'success',
        'total' => $count
    ]);
});

   Route::apiResource('users', UserController::class);
   Route::apiResource('trainings', TrainingController::class);
   Route::apiResource('training-participants', TrainingParticipantController::class);

// Get logged-in user info
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');