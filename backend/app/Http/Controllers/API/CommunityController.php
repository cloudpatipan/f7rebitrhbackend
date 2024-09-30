<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Favorite;

class CommunityController extends Controller
{
    public function view($name)
    {
        try {
            $favorites = Favorite::with(['user', 'character'])
            ->whereHas('user', function ($query) use ($name) {
                $query->where('name', $name);
            })
            ->get();

            $blogs = Blog::with(['user'])
            ->whereHas('user', function ($query) use ($name) {
                $query->where('name', $name);
            })
            ->get();
            if ($favorites && $blogs) {
                return response()->json(['favorites' => $favorites, 'blogs' => $blogs], 200);
            } else {
                return response()->json(['status' => 404, 'message' => 'ไม่พบรายการโปรดของผู้ใช้คนนี้'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }
}
