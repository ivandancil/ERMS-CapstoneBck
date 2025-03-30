<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TrainingParticipant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TrainingParticipantResource;

class TrainingParticipantController extends Controller
{
    public function index()
    {
        $participants = TrainingParticipant::with('training')->get();
    
        return TrainingParticipantResource::collection($participants);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'training_id' => 'required|exists:trainings,id',
            'training_title' => 'required|string|max:50',
            'fullname' => 'required|string|max:50',
            'jobposition' => 'required|string|max:50',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $participant = TrainingParticipant::create([
            'training_id' => $request->training_id,
            'training_title' => $request->training_title,
            'fullname' => $request->fullname,
            'jobposition' => $request->jobposition,
        ]);
    

        return response()->json([
            'message' => 'Participant enrolled successfully',
             'data' => new TrainingParticipantResource($participant),
        ], 200);
    }
    

}
