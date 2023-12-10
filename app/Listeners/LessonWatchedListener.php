<?php

namespace App\Listeners;

use App\Services\AchievementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LessonWatchedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    protected AchievementService $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $this->achievementService->saveNewWatchedLesson($event->user);
    }
}
