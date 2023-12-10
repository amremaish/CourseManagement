<?php

namespace Database\Seeders;

use App\Models\AchievementCommentType;
use App\Models\AchievementLessonType;
use Illuminate\Database\Seeder;

class AchievementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lessonAchievements = [
            'First Lesson Watched' => 1,
            '5 Lessons Watched' => 5,
            '10 Lessons Watched' => 10,
            '25 Lessons Watched' => 25,
            '50 Lessons Watched' => 50,
        ];

        $commentAchievements = [
            'First Comment Written' => 1,
            '3 Comments Written' => 3,
            '5 Comments Written' => 5,
            '10 Comments Written' => 10,
            '20 Comments Written' => 20,
        ];

        foreach ($lessonAchievements as $name => $condition) {
            $existingAchievement = AchievementLessonType::where('name', $name)
                ->first();

            if (!$existingAchievement) {
                AchievementLessonType::insert(['name' => $name, 'condition' => $condition]);
            } else {
                if ($existingAchievement->condition != $condition) {
                    echo '* LessonType updated [' . $name . '], condition ' . $existingAchievement->condition . ' => ' . $condition . PHP_EOL;
                    AchievementLessonType::where('name', $name)
                        ->update(['condition' => $condition,'updated_at' => now()]);
                }
            }
        }

        foreach ($commentAchievements as $name => $condition) {
            $existingAchievement = AchievementCommentType::where('name', $name)
                ->first();

            if (!$existingAchievement) {
                AchievementCommentType::insert(['name' => $name, 'condition' => $condition]);
            } else {
                if ($existingAchievement->condition != $condition) {
                    echo '* CommentType updated [' . $name . '], condition ' . $existingAchievement->condition . ' => ' . $condition . PHP_EOL;
                    AchievementCommentType::where('name', $name)
                        ->update(['condition' => $condition,'updated_at' => now()]);
                }
            }
        }

    }
}
