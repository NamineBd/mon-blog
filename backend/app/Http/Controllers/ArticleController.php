<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    // GET /api/articles
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Article::with(['user', 'comments.user', 'images']);

        if ($user && $user->is_admin) {
            $articles = $query->latest()->paginate(10);
        } elseif ($user) {
            $articles = $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                ->orWhere('status', 'published');
            })->latest()->paginate(10);
        } else {
            $articles = $query->where('status', 'published')
                            ->latest()
                            ->paginate(10);
        }

        return response()->json($articles);
    }

    // POST /api/articles
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'excerpt'     => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'status'      => 'required|in:draft,published',
        ]);

        $validated['user_id'] = $request->user()->id;

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                                                 ->store('covers', 'public');
        }

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $article = Article::create($validated);

        return response()->json($article, 201);
    }

    // GET /api/articles/{article}
    public function show($id)
    {
        $article = Article::with(['user', 'comments.user', 'images'])->find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article not found',
                'received_id' => $id,
            ], 404);
        }

        $user = auth()->user();

        $canView = $article->status === 'published'
                || $article->user_id === $user?->id
                || $user?->is_admin;

        if (!$canView) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($article);
    }

    // PUT|PATCH /api/articles/{article}
    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article not found',
                'received_id' => $id,
            ], 404);
        }

        if (!$this->canModify($request->user(), $article)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'content'     => 'sometimes|string',
            'excerpt'     => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'status'      => 'sometimes|in:draft,published',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }

            $validated['cover_image'] = $request->file('cover_image')
                                                ->store('covers', 'public');
        }

        if (
            isset($validated['status'])
            && $validated['status'] === 'published'
            && $article->status !== 'published'
        ) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return response()->json($article);
    }

    // DELETE /api/articles/{article}
    public function destroy(Request $request, $id)
    {
        $article = Article::with('images')->find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article not found',
                'received_id' => $id,
            ], 404);
        }

        if (!$this->canModify($request->user(), $article)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        foreach ($article->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted']);
    }

    // Helper privé — évite de répéter la même condition partout
    private function canModify($user, Article $article): bool
    {
        if (!$user) {
            return false;
        }

        return $user->is_admin || $article->user_id === $user->id;
    }

}