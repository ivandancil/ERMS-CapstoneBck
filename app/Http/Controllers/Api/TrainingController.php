<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrainingResource;
use Illuminate\Support\Facades\Validator;

class TrainingController extends Controller
{
    public function index()
    {
        $this->updateTrainingStatus(); // Ensure statuses are updated before fetching

        // Force refresh the collection to ensure updated status is fetched
        return response()->json(TrainingResource::collection(Training::all()->fresh()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trainingID' => 'required|string|max:50|unique:trainings,trainingID',
            'training_title' => 'required|string|max:50|unique:trainings,training_title',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'errors' => $validator->messages(),
            ], 422);
        }
    
        $now = now();
        $status = ($request->start_datetime > $now) ? 'Upcoming' :
                  (($request->start_datetime <= $now && $request->end_datetime >= $now) ? 'Ongoing' : 'Completed');
    
        // Compute duration in days and hours
        $start = \Carbon\Carbon::parse($request->start_datetime);
        $end = \Carbon\Carbon::parse($request->end_datetime);
        $duration = $start->diff($end)->format('%d days');
    
        $training = Training::create([
            'trainingID' => $request->trainingID,
            'training_title' => $request->training_title,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'duration' => $duration,
            'status' => $status,
        ]);
    
        return response()->json([
            'message' => 'Training Added Successfully',
            'data' => new TrainingResource($training),
        ], 200);
    }
    

    public function update(Request $request, Training $training)
    {
        $validator = Validator::make($request->all(), [
            'trainingID' => 'required|string|max:50|unique:trainings,trainingID,' . $training->id,
            'training_title' => 'required|string|max:50|unique:trainings,training_title,' . $training->id,
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are mandatory',
                'errors' => $validator->messages(),
            ], 422);
        }
    
        $now = now();
        $status = ($request->start_datetime > $now) ? 'Upcoming' :
                  (($request->start_datetime <= $now && $request->end_datetime >= $now) ? 'Ongoing' : 'Completed');
    
        // Compute duration in days and hours
        $start = \Carbon\Carbon::parse($request->start_datetime);
        $end = \Carbon\Carbon::parse($request->end_datetime);
        $duration = $start->diff($end)->format('%d days');
    
        $training->update([
            'trainingID' => $request->trainingID,
            'training_title' => $request->training_title,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'duration' => $duration,
            'status' => $status,
        ]);
    
        return response()->json([
            'message' => 'Training Updated Successfully',
            'data' => new TrainingResource($training),
        ], 200);
    }
    

    public function show(Training $training)
{
    return response()->json([
        'message' => 'Training retrieved successfully',
        'data' => new TrainingResource($training),
    ], 200);
}


    public function destroy(Training $training)
    {
        $training->delete();
        return response()->json([
            'message' => 'Training Deleted Successfully',
        ], 200);
    }

    public function updateTrainingStatus()
    {
        $now = now();
        Log::info("Current time for updateTrainingStatus", ['now' => $now]);

        // Log training records before updating
        $trainings = Training::select('id', 'training_title', 'start_datetime', 'end_datetime', 'status')->get();
        Log::info("Training Data Before Update", ['trainings' => $trainings]);

        // Ensure proper status update
        $upcoming = Training::where('start_datetime', '>', $now)
                            ->where('status', '!=', 'Completed')
                            ->update(['status' => 'Upcoming']);

        $ongoing = Training::where('start_datetime', '<=', $now)
                           ->where('end_datetime', '>=', $now)
                           ->update(['status' => 'Ongoing']);

        $completed = Training::where('end_datetime', '<', $now)
                             ->update(['status' => 'Completed']);

        // Log updates made
        Log::info("Training statuses updated", [
            'Upcoming' => $upcoming,
            'Ongoing' => $ongoing,
            'Completed' => $completed
        ]);

        return response()->json([
            'message' => 'Training statuses updated successfully.',
            'updates' => [
                'Upcoming' => $upcoming,
                'Ongoing' => $ongoing,
                'Completed' => $completed
            ],
            'current_time' => $now
        ]);
    }
}
