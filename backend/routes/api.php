<?php 

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

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