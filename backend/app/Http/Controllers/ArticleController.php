<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['user', 'comments.user', 'images']);

        // Si l'utilisateur n'est pas admin, il ne voit que ses propres articles + ceux publiés des autres
        if (!$request->user()->is_admin) {
            $query->where(function ($q) use ($request) {
                $q->where('user_id', $request->user()->id)
                  ->orWhere('status', 'published');
            });
        }

        $articles = $query->latest()->paginate(10);
        return response()->json($articles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->only(['title', 'content', 'excerpt', 'status']);
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $data['cover_image'] = $path;
        }

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $article = Article::create($data);

        return response()->json($article, 201);
    }

    public function show(Article $article)
    {
        // Vérifier si l'article est accessible
        if ($article->status !== 'published' && $article->user_id !== auth()->id() && !auth()->user()?->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($article->load(['user', 'comments.user', 'images']));
    }

    public function update(Request $request, Article $article)
    {
        // Vérifier permission
        if ($article->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'excerpt' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'sometimes|in:draft,published',
        ]);

        if ($request->hasFile('cover_image')) {
            // Supprimer ancienne image
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }
            $path = $request->file('cover_image')->store('covers', 'public');
            $article->cover_image = $path;
        }

        $article->fill($request->only(['title', 'content', 'excerpt', 'status']));

        if ($request->has('status') && $request->status === 'published' && $article->status !== 'published') {
            $article->published_at = now();
        }

        $article->save();

        return response()->json($article);
    }

    public function destroy(Request $request, Article $article)
    {
        if ($article->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Supprimer les images associées
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
}