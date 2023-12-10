<?php

namespace App\Models;

use App\Events\CommentWritten;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Event;

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
    public static function create(string $body, User $user): ?Model
    {

        try {
            $comment = $user->comments()->create([
                'body' => $body,
            ]);
            Event::dispatch(new CommentWritten($user));
            return $comment;
        } catch (\Exception $e) {
            echo $e;
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
