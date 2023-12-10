<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Lesson extends Model
{
    use HasFactory;

    /**
     * @throws Exception
     */
    public static function watchLesson($userId, $lessonId)
    {
        self::findOrFail($lessonId);
        $lessonUser = LessonUser::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->first();
        if (!$lessonUser) {
            LessonUser::create([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'watched' => true,
            ]);
            return true;
        }
        if (!$lessonUser->watched) {
            $lessonUser->update(['watched' => true]);
            return true;
        }
        return false;

    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title'
    ];
}
