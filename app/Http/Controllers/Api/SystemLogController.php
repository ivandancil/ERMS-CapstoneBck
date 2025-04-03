<?php

namespace App\Http\Controllers\Api;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SystemLogController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => SystemLog::orderBy('timestamp', 'desc')->paginate(10),
        ]);
    }
    

    public function store(Request $request)
{
    $validated = $request->validate([
        'category' => 'required|string',
        'user' => 'nullable|string',
        'action' => 'required|string',
        'timestamp' => 'required|date',
    ]);

    $log = SystemLog::create($validated);
    return response()->json($log, 201);
}

}
