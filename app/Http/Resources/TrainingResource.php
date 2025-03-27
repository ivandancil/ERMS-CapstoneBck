<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'trainingID' => $this->trainingID,
            'training_title' => $this->training_title,
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'duration' => $this->duration,
            'created_at' => $this->created_at,
        ];
    }
}
