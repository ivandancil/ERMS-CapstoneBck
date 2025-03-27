<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'employeeID',
        'lastname',
        'firstname',
        'middlename',
        'sex',
        'dateOfBirth',
        'civilStatus',
        'phoneNumber',
        'email',
        'address',
        'jobPosition',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'user_id'); 
    }
}
