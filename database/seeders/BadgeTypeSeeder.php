<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            $existingAchievement = DB::table('badge_types')
                ->where('name', $name)
                ->first();

            if (!$existingAchievement) {
                DB::table('badge_types')->insert(['name' => $name, 'condition' => $condition]);
            } else {
                if ($existingAchievement->condition != $condition) {
                    echo '* Badge updated [' . $name . '], condition ' . $existingAchievement->condition . ' => ' . $condition . PHP_EOL;
                    DB::table('badge_types')
                        ->where('name', $name)
                        ->update(['condition' => $condition,'updated_at' => now()]);
                }
            }
        }
    }
}
