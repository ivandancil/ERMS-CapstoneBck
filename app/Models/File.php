<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;

     // Allow mass assignment for the file_name property along with other attributes
     protected $fillable = ['file_name', 'original_name', 'file_path', 'uploaded_at'];
}
