<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    // Upload d'image supplémentaire pour un article
    public function uploadImage(Request $request, Article $article)
    {
        // Vérifier permission sur l'article
        if ($article->user_id !== $request->user()->id && !$request->user()->is_admin) {
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

    // Supprimer une image supplémentaire
    public function deleteImage(Request $request, ArticleImage $image)
    {
        $article = $image->article;
        if ($article->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['message' => 'Image deleted']);
    }

    // Optionnel : récupérer toutes les images d'un article
    public function articleImages(Article $article)
    {
        return response()->json($article->images);
    }
}