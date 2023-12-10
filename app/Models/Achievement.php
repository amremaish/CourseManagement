<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'number_of_achievements',
        'number_of_badges',
        'user_id',
        'current_badge_id',
        'next_badge_id',
        'remaining_to_unlock_next_badge'
    ];
    protected $casts = [
        'unlocked_achievements' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currentBadge()
    {
        return $this->belongsTo(BadgeType::class, 'current_badge_id');
    }

    public function nextBadge()
    {
        return $this->belongsTo(BadgeType::class, 'next_badge_id');
    }

}
