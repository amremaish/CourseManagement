<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LessonUser extends Pivot
{
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'lesson_id',
        'watched',
    ];

    // Define relationships with User and Lesson models
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
