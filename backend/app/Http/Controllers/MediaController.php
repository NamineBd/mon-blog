<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    // POST /api/articles/{id}/images
    public function uploadImage(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        if (!$this->canModifyArticle($request->user(), $article)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $path = $request->file('image')->store('articles', 'public');

        $image = $article->images()->create([
            'image_path' => $path,
        ]);

        return response()->json($image, 201);
    }

    // GET /api/articles/{id}/images
    public function articleImages(Request $request, $id)
    {
        $article = Article::with('images')->findOrFail($id);

        if (!$this->canViewArticle($request->user(), $article)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($article->images);
    }

    // DELETE /api/article-images/{id}
    public function deleteImage(Request $request, $id)
    {
        $image = ArticleImage::with('article')->findOrFail($id);
        $article = $image->article;

        if (!$article) {
            return response()->json(['message' => 'Article not found for this image'], 404);
        }

        if (!$this->canModifyArticle($request->user(), $article)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($image->image_path);

        $image->delete();

        return response()->json(['message' => 'Image deleted']);
    }

    private function canViewArticle($user, Article $article): bool
    {
        return $article->status === 'published'
            || $article->user_id === $user->id
            || $user->is_admin;
    }

    private function canModifyArticle($user, Article $article): bool
    {
        return $user->is_admin || $article->user_id === $user->id;
    }
}
