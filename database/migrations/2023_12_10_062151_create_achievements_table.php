<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->integer('number_of_watched_lessons')->default(0);
            $table->integer('number_of_comments')->default(0);
            $table->json('unlocked_achievements')->nullable();
            $table->json('next_available_achievements')->nullable();
            $table->foreignId('user_id')->unique()->constrained('users')->index();
            $table->foreignId('current_badge_id')->nullable()->constrained('badge_types');
            $table->foreignId('next_badge_id')->nullable()->constrained('badge_types');
            $table->integer('remaining_to_unlock_next_badge')->default(0);;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
