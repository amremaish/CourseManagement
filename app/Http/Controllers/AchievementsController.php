<?php

namespace App\Http\Controllers;

use App\Models\AchievementCommentType;
use App\Models\AchievementLessonType;
use App\Models\BadgeType;
use App\Models\User;


class AchievementsController extends Controller
{
    public function index(User $user)
    {
        $achievements = $user->achievements()->get();
        $nextAvailableAchievements = $achievements->pluck('next_available_achievements')->first();

        if ($nextAvailableAchievements === null) {
            $mergedNames = AchievementCommentType::pluck('name')->merge(AchievementLessonType::pluck('name'));
            $nextAvailableAchievements = $mergedNames->unique()->values()->all();
        } else {
            $nextAvailableAchievements = json_decode($nextAvailableAchievements, true);
        }

        $currentBadge = $achievements->pluck('current_badge_id')->first();
        $nextBadge = $achievements->pluck('next_badge_id')->first();

        if ($currentBadge == 0) {
            $nextBadge = BadgeType::orderBy('condition')->first()->name;
            $currentBadge = null;
        } else {
            $nextBadge = BadgeType::find($nextBadge)->name;
            $currentBadge = BadgeType::find($currentBadge)->name;
        }

        $unlockedAchievements = $achievements->pluck('unlocked_achievements')->collapse()->all();

        $remainingToUnlockNextBadge = $achievements->pluck('remaining_to_unlock_next_badge')->first();

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge,
            'remaining_to_unlock_next_badge' => $remainingToUnlockNextBadge,
        ]);
    }
}
