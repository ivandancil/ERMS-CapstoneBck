<?php

namespace App\Http\Controllers\Api;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveRequestResource;

class LeaveRequestController extends Controller
{
    // USER: Submit Leave Request
    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => auth()->id(),
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Leave request submitted.',
            'data' => new LeaveRequestResource($leaveRequest),
        ], 201);
    }

    // USER: View Their Leave Requests
    public function userRequests()
    {
        $userId = auth()->id();
        $requests = LeaveRequest::where('user_id', $userId)->get();

        return response()->json([
            'data' => LeaveRequestResource::collection($requests)
        ]);
    }

    // ADMIN: View All Leave Requests
    public function index()
    {
        $requests = LeaveRequest::with('user')->get();

        return response()->json([
            'data' => LeaveRequestResource::collection($requests)
        ]);
    }

    // ADMIN: Approve/Deny Leave Request
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Denied',
            'admin_comment' => 'nullable|string',
        ]);

        $leave = LeaveRequest::findOrFail($id);
        $leave->status = $request->status;
        $leave->admin_comment = $request->admin_comment;
        $leave->save();

        return response()->json([
            'message' => 'Leave request updated.',
            'data' => new LeaveRequestResource($leave)
        ]);
    }

      // USER: Update their Leave Request
      public function updateUserRequest(Request $request, $id)
      {
          \Log::info('Authenticated User ID: ' . auth()->id());  // Log the user id
          \Log::info('Requested Leave Request ID: ' . $id);  // Log the id you're trying to update
      
          // Validate the incoming request data
          $request->validate([
              'leave_type' => 'required|string',
              'start_date' => 'required|date',
              'end_date' => 'required|date|after_or_equal:start_date',
              'reason' => 'nullable|string',
          ]);
      
          // Find the leave request ensuring it belongs to the authenticated user
          $leaveRequest = LeaveRequest::where('id', $id)
              ->where('user_id', auth()->id())
              ->first();
      
          if (!$leaveRequest) {
              \Log::warning('Leave request not found for this user.');  // Log if no request is found
              return response()->json(['message' => 'Record not found'], 404);
          }
      
          // Update the leave request with the validated data
          $leaveRequest->update([
              'leave_type' => $request->leave_type,
              'start_date' => $request->start_date,
              'end_date' => $request->end_date,
              'reason' => $request->reason,
          ]);
      
          return response()->json([
              'message' => 'Leave request updated successfully.',
              'data' => new LeaveRequestResource($leaveRequest),
          ]);
      }
      


    // USER or ADMIN: Delete Leave Request
    public function destroy($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->delete();

        return response()->json([
            'message' => 'Leave request deleted successfully.'
        ]);
    }
}
