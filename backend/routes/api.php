<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NewsletterController;

/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Newsletter publique
Route::get('/newsletter/confirm/{id}',         [NewsletterController::class, 'confirm']);
Route::get('/newsletter/unsubscribe/{token}',  [NewsletterController::class, 'unsubscribe']);

/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // ── Articles ────────────────────────────────────────────────────────
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::get('/articles/{article}', [ArticleController::class, 'show']);
    Route::put('/articles/{article}', [ArticleController::class, 'update']);
    Route::patch('/articles/{article}', [ArticleController::class, 'update']);
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy']);

    // ── Médias (APRÈS les routes articles de base) ───────────────────────
    Route::post('/articles/{article}/images', [MediaController::class, 'uploadImage']);
    Route::get('/articles/{article}/images', [MediaController::class, 'articleImages']);
    Route::delete('/article-images/{image}', [MediaController::class, 'deleteImage']);

    // ── Commentaires ─────────────────────────────────────────────────────
    Route::get('/articles/{article}/comments', [CommentController::class, 'index']);
    Route::post('/articles/{article}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // ── Newsletter ────────────────────────────────────────────────────────
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
    
    // CORRIGÉ: Utilisation du middleware avec le namespace complet
    // Si vous n'avez pas enregistré le middleware 'admin' dans bootstrap/app.php,
    // utilisez le namespace complet à la place:
    Route::get('/admin/subscribers', [NewsletterController::class, 'subscribersList'])
         ->middleware(\App\Http\Middleware\AdminMiddleware::class);
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
            'status'    => 'OK',
            'database'  => 'Connected',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'   => 'ERROR',
            'database' => 'Disconnected',
            'error'    => $e->getMessage(),
        ], 500);
    }
});