<?php

namespace Database\Seeders;

use App\Models\Lesson;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AchievementTypeSeeder::class);
        $this->call(BadgeTypeSeeder::class);
        $lessons = Lesson::factory()
            ->count(20)
            ->create();
    }
}
