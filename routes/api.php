<?php

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\OCRController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserLogController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\PDFParseController;
use App\Http\Controllers\Api\SystemLogController;
use App\Http\Controllers\Api\UploadImageController;
use App\Http\Controllers\Api\DocumentParseController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


    // Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Get logged-in Employee info
    Route::get('/employee', [EmployeeController::class, 'getLoggedInEmployee']);

    // ADMIN Routes - Protected with isAdmin
    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {

          // Get logged-in user info
    Route::get('/user', [AuthController::class, 'getUser']);

    });
});

        Route::post('/parse-document', [PDFParseController::class, 'parse']);
        Route::post('/parse-document', [DocumentParseController::class, 'parse']);

        // Upload Image
        Route::post('/upload-pds', [UploadImageController::class, 'uploadPDS']);

        // Document Uploads(PDS)
        Route::post('/upload-pds', [DocumentController::class, 'upload']);
        Route::get('/documents', [DocumentController::class, 'index']); // to list uploaded PDS
        // Route to delete a file by ID
        Route::delete('files/{id}', [DocumentController::class, 'delete']);


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
        Route::get('/system-logs', [SystemLogController::class, 'index']);
        Route::post('/system-logs', [SystemLogController::class, 'store']);
        Route::get('/user-logs', [UserLogController::class, 'index']);
        Route::post('/user-logs', [UserLogController::class, 'store']);

// Get logged-in user info
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');