<?php

namespace App\Http\Controllers\Api;

use App\Models\UserLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserLogController extends Controller
{
    // Fetch all user logs with user name included
    public function index()
    {
        $logs = UserLog::with('user:id,name') // Fetch user name
            ->orderBy('timestamp', 'desc') // Order by most recent logs
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'name' => $log->user->name ?? 'N/A', // Get user name or 'N/A' if missing
                    'action' => $log->action,
                    'timestamp' => $log->timestamp,
                    
                ];
            });

        return response()->json(['data' => $logs], 200);
    }
    
      // Store a User Log
      public function store(Request $request)
      {
          $request->validate([
              'action' => 'required|string|max:255',
          ]);
  
          $log = UserLog::create([
              'user_id' => Auth::id(),
              'action' => $request->action,
          ]);
  
          return response()->json(['message' => 'User log recorded', 'log' => $log], 201);
      }
}
