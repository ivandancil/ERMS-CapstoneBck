<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // Fetch attendance records for the authenticated user
    public function index()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->orderBy('date', 'desc')->get();
        return response()->json($attendance);
    }

    // Clock In
    public function clockIn()
    {
        $user = Auth::user();
        $date = now()->format('Y-m-d');

        // Check if already clocked in today
        $existingRecord = Attendance::where('user_id', $user->id)->where('date', $date)->first();
        if ($existingRecord) {
            return response()->json(['message' => 'You have already clocked in today'], 400);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'time_in' => now()->format('H:i:s'),
            'time_out' => null,
            'overtime' => 0,
            'status' => 'Present',
        ]);

        return response()->json($attendance, 201);
    }

    // Clock Out
    public function clockOut($id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json(['message' => 'Attendance record not found'], 404);
        }

        if ($attendance->time_out) {
            return response()->json(['message' => 'You have already clocked out'], 400);
        }

        $timeOut = now();
        $timeIn = \Carbon\Carbon::parse($attendance->time_in);
        $hoursWorked = $timeIn->diffInHours($timeOut);
        $overtime = max($hoursWorked - 8, 0); // Overtime is calculated if > 8 hours worked

        $attendance->update([
            'time_out' => $timeOut->format('H:i:s'),
            'overtime' => $overtime,
        ]);

        return response()->json($attendance);
    }
}
