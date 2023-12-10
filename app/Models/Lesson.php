<?php

namespace App\Models;

use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Event;

class Lesson extends Model
{
    use HasFactory;

    /**
     * @throws Exception
     */
    public static function watchLesson($userId, $lessonId)
    {
        $is_watched = false;
        $user = User::findOrFail($userId);
        $lessonUser = LessonUser::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->first();

        if (!$lessonUser) {
            $lessonUser = LessonUser::create([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'watched' => true,
            ]);
            $is_watched = true;
        }
        if (!$lessonUser->watched) {
            $lessonUser->update(['watched' => true]);
            $is_watched = true;
        }
        if ($is_watched) {
            Event::dispatch(new LessonWatched($user));
        }
        return $is_watched;

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
