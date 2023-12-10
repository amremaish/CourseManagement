<?php

namespace Database\Seeders;

use App\Models\BadgeType;
use Illuminate\Database\Seeder;

class BadgeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badgeAchievements = [
            'Beginner' => 0,
            'Intermediate' => 4,
            'Advanced' => 8,
            'Master' => 10
        ];

        foreach ($badgeAchievements as $name => $condition) {
            $existingAchievement = BadgeType::
                where('name', $name)
                ->first();

            if (!$existingAchievement) {
                BadgeType::insert(['name' => $name, 'condition' => $condition]);
            } else {
                if ($existingAchievement->condition != $condition) {
                    echo '* Badge updated [' . $name . '], condition ' . $existingAchievement->condition . ' => ' . $condition . PHP_EOL;
                    BadgeType::where('name', $name)
                        ->update(['condition' => $condition,'updated_at' => now()]);
                }
            }
        }
    }
}
