<?php

namespace App\Models;

use App\Models\TrainingParticipant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Training extends Model
{
    use HasFactory;

    protected $table = 'trainings';

    protected $fillable = [
        'trainingID',
        'training_title',
        'start_datetime',
        'end_datetime',
        'duration',
       
    ];

    public function participants()
    {
        return $this->hasMany(TrainingParticipant::class);
    }
}
