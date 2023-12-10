<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body',
        'user_id'
    ];

    /**
     * Create a comment for the given user.
     *
     * @param string $body
     * @param User $user
     * @return Model
     */
    public static function createForUser(string $body, User $user)
    {
        try {
            $comment = $user->comments()->create([
                'body' => $body,
            ]);
            return $comment;
        } catch (\Exception $e) {
            // Log the exception or handle it accordingly
            return null;
        }
    }

    /**
     * Get the user that wrote the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
