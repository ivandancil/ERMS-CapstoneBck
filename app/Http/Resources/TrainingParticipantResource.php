<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingParticipantResource extends JsonResource
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
            'fullname' => $this->fullname,
            'jobposition' => $this->jobposition,
            'training_title' => $this->training->training_title,
            'created_at' => $this->created_at,
        ];
    }
}
