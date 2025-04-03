<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemLog extends Model
{
    use HasFactory;
    
    protected $fillable = ['category', 'user', 'action', 'timestamp'];
}
