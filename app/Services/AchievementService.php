<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\AchievementCommentType;
use App\Models\AchievementLessonType;
use App\Models\BadgeType;
use App\Events\BadgeUnlocked;
use App\Events\AchievementUnlocked;
use App\Models\User;
use Illuminate\Support\Facades\Event;

class AchievementService
{

    /**
     * Handle the addition of a new comment by a user.
     *
     * @param User $user User who added the comment
     *
     * @return void
     */
    public function saveNewComment(User $user): bool
    {
        // Retrieve or create achievements associated with the user
        $achievements = $user->achievements ?? Achievement::create(['user_id' => $user->id]);

        // Increment the count of comments made by the user
        $achievements->number_of_comments++;

        // Process the achievements based on the user's actions
        return $this->processAchievements($achievements, $user);
    }

    /**
     * Handle the addition of a new watched lesson by a user.
     *
     * @param User $user User who watched the lesson
     *
     * @return void
     */
    public function saveNewWatchedLesson(User $user): void
    {
        // Retrieve or create achievements associated with the user
        $achievements = $user->createAchievementIfNotExists();

        // Increment the count of watched lessons by the user
        $achievements->number_of_watched_lessons++;

        // Process the achievements based on the user's actions
        $this->processAchievements($achievements, $user);
    }

    /**
     * Process achievements based on user actions.
     *
     * @param mixed $achievements Achievements object related to the user
     * @param mixed $user User object
     *
     * @return bool
     */
    private function processAchievements($achievements, $user)
    {
        // Retrieve unlocked achievements based on user actions
        $unlockedAchievements = $this->getUnlockedAchievements($achievements);

        // Determine the current badge achieved by the user
        $currentBadge = $this->getCurrentBadge($unlockedAchievements);

        // Find the next badge that the user can achieve
        $nextBadge = $this->getNextBadge($unlockedAchievements);

        // Dispatch event for unlocking the current badge
        $this->dispatchBadgeEvent($achievements, $currentBadge, $user);

        // Dispatch event for unlocking new achievements
        $this->dispatchAchievementEvent($achievements, $unlockedAchievements, $user);

        // Retrieve the list of available achievements for the user
        $nextAvailableAchievements = $this->getNextAvailableAchievements($achievements);

        // Update the user's achievements properties
        $achievements->current_badge_id = $currentBadge->id;
        $achievements->next_badge_id = $nextBadge->id;
        $achievements->unlocked_achievements = $unlockedAchievements;
        $achievements->next_available_achievements = $nextAvailableAchievements;
        $achievements->remaining_to_unlock_next_badge = $nextBadge->condition - count($unlockedAchievements);
        return $achievements->save();
    }

    /**
     * Retrieve unlocked achievements based on user's actions.
     *
     * @param mixed $achievements Achievements object related to the user
     *
     * @return array List of unlocked achievement names
     */
    private function getUnlockedAchievements(mixed $achievements): array
    {

        $commentsCondition = AchievementCommentType::select('name', 'condition')
            ->where('condition', '<=', intval($achievements->number_of_comments));

        $lessonsCondition = AchievementLessonType::select('name', 'condition')
            ->where('condition', '<=', intval($achievements->number_of_watched_lessons));

        return $commentsCondition->union($lessonsCondition)
            ->orderBy('condition')
            ->pluck('name')
            ->toArray();

    }

    /**
     * Get the current badge achieved by the user.
     *
     * @param array $unlockedAchievements List of unlocked achievements
     *
     * @return mixed Current badge achieved by the user
     */
    private function getCurrentBadge(array $unlockedAchievements): mixed
    {
        return BadgeType::where('condition', '<=', count($unlockedAchievements))
            ->orderBy('condition', 'DESC')
            ->first();
    }

    /**
     * Find the next badge that the user can achieve.
     *
     * @param array $unlockedAchievements List of unlocked achievements
     *
     * @return mixed Next badge that the user can achieve
     */
    private function getNextBadge($unlockedAchievements): mixed
    {
        return BadgeType::where('condition', '>', count($unlockedAchievements))
            ->orderBy('condition')
            ->first();
    }

    /**
     * Dispatch event for unlocking the current badge if changed.
     *
     * @param mixed $achievements Achievements object related to the user
     * @param mixed $currentBadge Current badge achieved by the user
     * @param mixed $user User object
     *
     * @return void
     */
    private function dispatchBadgeEvent($achievements, $currentBadge, $user)
    {
        if ($achievements->current_badge_id != $currentBadge->id) {
            Event::dispatch(new BadgeUnlocked($currentBadge->name, $user));
        }
    }

    /**
     * Dispatch event for unlocking new achievements.
     *
     * @param mixed $achievements Achievements object related to the user
     * @param array $unlockedAchievements List of unlocked achievements
     * @param mixed $user User object
     *
     * @return void
     */
    private function dispatchAchievementEvent($achievements, $unlockedAchievements, $user): void
    {

        if (!empty($unlockedAchievements)) {
            if (!empty($achievements->unlocked_achievements)) {
                $unlockedAchievementTemp = $achievements->unlocked_achievements;
                if (end($unlockedAchievements) !== end($unlockedAchievementTemp)) {
                    Event::dispatch(new AchievementUnlocked(end($unlockedAchievements), $user));
                }
            } else {
                Event::dispatch(new AchievementUnlocked(end($unlockedAchievements), $user));
            }
        }
    }

    /**
     * Retrieve the list of available achievements for the user.
     *
     * @param mixed $achievements Achievements object related to the user
     *
     * @return array List of available achievements for the user
     */
    private function getNextAvailableAchievements(mixed $achievements): array
    {
        $commentsCondition = AchievementCommentType::select('name', 'condition')
            ->where('condition', '>', intval($achievements->number_of_comments));

        $lessonsCondition = AchievementLessonType::select('name', 'condition')
            ->where('condition', '>', intval($achievements->number_of_watched_lessons));

        return $commentsCondition->union($lessonsCondition)
            ->orderBy('condition')
            ->pluck('name')
            ->toArray();
    }


}
