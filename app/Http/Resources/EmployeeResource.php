<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'employeeID' => $this->employeeID,
            'lastname'     => $this->lastname,
            'firstname'    => $this->firstname,
            'middlename'   => $this->middlename ?? null, // Nullable field
            'sex'          => $this->sex,
            'dateOfBirth'  => $this->dateOfBirth,
            'civilStatus'  => $this->civilStatus,
            'phoneNumber'  => $this->phoneNumber,
            'email'        => $this->email,
            'address'      => $this->address,
            'jobPosition'  => $this->jobPosition,
            'created_at' => $this->created_at,
        ];
    }
}
