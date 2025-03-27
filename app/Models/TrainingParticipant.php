<?php

namespace App\Models;

use App\Models\Training;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingParticipant extends Model
{
    use HasFactory;

    protected $fillable = ['training_id', 'fullname', 'jobposition'];

    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id');
    }
}
