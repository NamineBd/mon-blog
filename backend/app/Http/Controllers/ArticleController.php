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

        if ($user->is_admin) {
            $articles = $query->latest()->paginate(10);
        } else {
            $articles = $query
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('status', 'published');
                })
                ->latest()
                ->paginate(10);
        }

        return response()->json($articles);
    }
    
    // Pour envoyer un fichier image utiliser multipart/form-data et non application/json
    // POST /api/articles
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
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

    // GET /api/articles/{id}
    public function show(Request $request, $id)
    {
        $article = Article::with(['user', 'comments.user', 'images'])->findOrFail($id);
        $user = $request->user();

        if (!$this->canView($user, $article)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($article);
    }

    // PUT|PATCH /api/articles/{id}
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        if (!$this->canModify($request->user(), $article)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'excerpt' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'sometimes|in:draft,published',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }

            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        if (
            isset($validated['status']) &&
            $validated['status'] === 'published' &&
            $article->status !== 'published'
        ) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return response()->json(
            $article->load(['user', 'comments.user', 'images'])
        );
    }

    // DELETE /api/articles/{id}
    public function destroy(Request $request, $id)
    {
        $article = Article::with('images')->findOrFail($id);

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

    private function canView($user, Article $article): bool
    {
        return $article->status === 'published'
            || $article->user_id === $user->id
            || $user->is_admin;
    }

    private function canModify($user, Article $article): bool
    {
        return $user->is_admin || $article->user_id === $user->id;
    }
}
