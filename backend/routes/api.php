<?php 

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NewsletterController;

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Newsletter (confirmation et désabonnement sans auth)
Route::get('/newsletter/confirm/{id}', [NewsletterController::class, 'confirm']);
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe']);

// Routes protégées par auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Articles (CRUD complet)
    Route::apiResource('articles', ArticleController::class);

    // Commentaires (spécifiques à un article)
    Route::prefix('articles/{article}')->group(function () {
        Route::get('/comments', [CommentController::class, 'index']);
        Route::post('/comments', [CommentController::class, 'store']);
    });
    // Modification/suppression d'un commentaire (par son ID)
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Médias
    Route::post('/articles/{article}/images', [MediaController::class, 'uploadImage']);
    Route::delete('/article-images/{image}', [MediaController::class, 'deleteImage']);
    Route::get('/articles/{article}/images', [MediaController::class, 'articleImages']);

    // Newsletter (abonnement nécessite auth  non, mais l'admin liste seulement)
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
    Route::get('/admin/subscribers', [NewsletterController::class, 'subscribersList'])->middleware('admin');
});


/*
|--------------------------------------------------------------------------
| ROUTE DE SANTÉ
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    try {
        DB::select('SELECT 1');
        return response()->json([
            'status' => 'OK',
            'database' => 'Connected',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'ERROR',
            'database' => 'Disconnected',
            'error' => $e->getMessage()
        ], 500);
    }
});