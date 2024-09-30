<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BattleController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\CharacterController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\CommunityController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WorldController;
use Illuminate\Support\Facades\Route;


//ผู้ใช้ทั่วไป
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('profile/update-information', [ProfileController::class, 'updateprofile']);
    Route::post('profile/update-password', [ProfileController::class, 'updatepassword']);
    Route::get('profile', [ProfileController::class, 'viewprofile']);
    Route::resource('/blog', controller: BlogController::class);
    Route::get('myfavorite', [FavoriteController::class, 'myFavorite']);
    Route::get('myblog', [BlogController::class, 'myBlog']);
    Route::resource('/comments', controller: CommentController::class);
});

Route::get('favorite', [FavoriteController::class, 'viewFavorite']);
Route::get('favorite/{slug}', [FavoriteController::class, 'viewFavoriteUser']);
Route::post('add-to-favorite', [FavoriteController::class, 'addToFavorite']);
Route::delete('delete-favoriteitem/{favorite_id}', [FavoriteController::class, 'deleteFavoriteitem']);

Route::get('/checkingAuthenticated', function () {
    if (auth('sanctum')->check()) {
        return response()->json(['status' => 200, 'message' => 'เข้าสู่ระบบสำเร็จ']);
    } else {
        return response()->json(['status' => 401, 'message' => 'กรุณาเข้าสู่ระบบ']);
    }
});


//ผู้ใช้ระดับ Admin
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::resource('admin/character', CharacterController::class);
    Route::resource('admin/role', RoleController::class);
    Route::resource('admin/world', WorldController::class);
    Route::resource('admin/user', controller: UserController::class);
    Route::resource('admin/battle', controller: BattleController::class);
    Route::get('admin/favorite_count', [CharacterController::class, 'favorite_count']);
    Route::get('admin/blog_count', [BlogController::class, 'blog_count']);
    Route::get('/checkingAuthenticatedAdmin', [AuthController::class, 'checkingAuthenticatedAdmin']);
});

Route::get('/user-all', [UserController::class, 'user_all']);

Route::get('/communityuser/{name}', [CommunityController::class, 'view']);

Route::get('/blog-all', [BlogController::class, 'blog_all']);
Route::get('/comment-all', [CommentController::class, 'comment_all']);
Route::get('/blog-detail/{name}', [BlogController::class, 'blog_detail']);

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');

Route::get('character', [CharacterController::class, 'character_all']);
Route::get('character/detail/{slug}', [CharacterController::class, 'detail']);

Route::get('role', [RoleController::class, 'role_all']);

Route::get('world', [WorldController::class, 'world_all']);
Route::get('world/detail/{slug}', [WorldController::class, 'detail']);

Route::get('battle', [BattleController::class, 'battle_all']);
Route::get('battle/detail/{name}', [BattleController::class, 'detail']);

