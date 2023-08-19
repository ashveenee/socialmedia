<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\SocialMediaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/posts', [SocialMediaController::class, 'postCreate']);
Route::get('/posts', [SocialMediaController::class, 'postFetchAll']);

Route::post('/comments', [SocialMediaController::class, 'commentCreate']);
Route::get('/comments/{postId}', [SocialMediaController::class, 'commentFetchAll']);

Route::post('/likes', [SocialMediaController::class, 'likeOrUnlike']);
