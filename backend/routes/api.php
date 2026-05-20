<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NewsletterController;

/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Newsletter publique
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
Route::get('/newsletter/confirm/{id}', [NewsletterController::class, 'confirm'])->whereNumber('id');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe']);

/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Articles
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles', [ArticleController::class, 'store']);

    Route::get('/articles/{id}', [ArticleController::class, 'show'])->whereNumber('id');
    Route::put('/articles/{id}', [ArticleController::class, 'update'])->whereNumber('id');
    Route::patch('/articles/{id}', [ArticleController::class, 'update'])->whereNumber('id');
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])->whereNumber('id');

    // Images d’articles
    Route::post('/articles/{id}/images', [MediaController::class, 'uploadImage'])->whereNumber('id');
    Route::get('/articles/{id}/images', [MediaController::class, 'articleImages'])->whereNumber('id');
    Route::delete('/article-images/{id}', [MediaController::class, 'deleteImage'])->whereNumber('id');

    // Commentaires
    Route::get('/articles/{id}/comments', [CommentController::class, 'index'])->whereNumber('id');
    Route::post('/articles/{id}/comments', [CommentController::class, 'store'])->whereNumber('id');

    Route::put('/comments/{id}', [CommentController::class, 'update'])->whereNumber('id');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->whereNumber('id');

    // Admin newsletter
    Route::get('/admin/subscribers', [NewsletterController::class, 'subscribersList'])
        ->middleware(\App\Http\Middleware\AdminMiddleware::class);

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

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
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'ERROR',
            'database' => 'Disconnected',
            'error' => $e->getMessage(),
        ], 500);
    }
});
