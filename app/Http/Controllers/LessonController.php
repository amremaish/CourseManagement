<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Exception;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function watchLesson(Request $request, $lessonId)
    {
        try {
            $user = $request->user();

            $result = Lesson::watchLesson($user->id, $lessonId);

            if ($result) {
                return response()->json(['message' => 'Lesson watched successfully']);
            } else {
                return response()->json(['message' => 'Lesson was already watched']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getTraceAsString()], 500);
        }
    }
}
