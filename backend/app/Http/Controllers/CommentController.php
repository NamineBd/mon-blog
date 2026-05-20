<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Article;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // GET /api/articles/{id}/comments
    public function index(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        if (!$this->canViewArticle($request->user(), $article)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comments = $article->comments()
            ->with('user')
            ->latest()
            ->get();

        return response()->json($comments);
    }

    // POST /api/articles/{id}/comments
    public function store(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        if (!$this->canViewArticle($request->user(), $article)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = $article->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        return response()->json($comment->load('user'), 201);
    }

    // PUT /api/comments/{id}
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $user = $request->user();

        if ($comment->user_id !== $user->id && !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        return response()->json($comment->load('user'));
    }

    // DELETE /api/comments/{id}
    public function destroy(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $user = $request->user();

        if ($comment->user_id !== $user->id && !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }

    private function canViewArticle($user, Article $article): bool
    {
        return $article->status === 'published'
            || $article->user_id === $user->id
            || $user->is_admin;
    }
}
