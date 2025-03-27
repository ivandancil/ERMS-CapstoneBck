<?php

namespace App\Http\Controllers\Api;

use App\Models\Training;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrainingResource;
use Illuminate\Support\Facades\Validator;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::all();
    
        if ($trainings->isEmpty()) {
            return response()->json([]); // Return an empty array
        }
    
        return response()->json($trainings);
    }
    

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trainingID' => 'required|string|max:50|unique:trainings,trainingID',
            'training_title' => 'required|string|max:50|unique:trainings,training_title',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'duration' => 'required|string|max:50', // Simplified to one string
           
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $validator->messages(),
            ],  422);
        } 

        $training = Training::create([
            'trainingID' => $request->trainingID,
            'training_title' => $request->training_title,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'duration' => $request->duration, // Directly store combined value
           
        ]);

        return response()->json([
            'message' => 'Training Added Successfully',
            'data' => new TrainingResource($training),
        ],  200);
    }

    public function show(Training $training)
    {
        return new TrainingResource($training);
    }

    public function update(Request $request, Training $training)
{
    $validator = Validator::make($request->all(), [
        'trainingID' => 'required|string|max:50|unique:trainings,trainingID,' . $training->id,
        'training_title' => 'required|string|max:50|unique:trainings,training_title,' . $training->id,
        'start_datetime' => 'required|date',
        'end_datetime' => 'required|date|after_or_equal:start_datetime',
        'duration' => 'required|string|max:50',
    ]);

    if($validator->fails())
    {
        return response()->json([
            'message' => 'All fields are mandatory',
            'errors' => $validator->messages(),
        ], 422);
    } 

    $training->update([
        'trainingID' => $request->trainingID,
        'training_title' => $request->training_title,
        'start_datetime' => $request->start_datetime,
        'end_datetime' => $request->end_datetime,
        'duration' => $request->duration,
    ]);

    return response()->json([
        'message' => 'Training Updated Successfully',
        'data' => new TrainingResource($training),
    ], 200);
}

public function destroy(Training $training)
{
    $training->delete();
    return response()->json([
        'message' => 'Training Deleted Successfully',
    ],  200);
}
    
}
