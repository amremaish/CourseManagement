<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
    public function create(CreateCommentRequest $request)
    {
        try {
            $user = $request->user();
            $validatedData = $request->validated();
            $comment = Comment::createForUser($validatedData['body'], $user);

            if ($comment) {
                return response()->json(['message' => 'Comment added successfully'], 201);
            } else {
                return response()->json(['message' => 'Failed to add comment'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }
}
