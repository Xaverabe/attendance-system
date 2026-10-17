<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_room_id',
        'student_id',
        'date',
        'status',
        'notes',
    ];

    public function classroom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
