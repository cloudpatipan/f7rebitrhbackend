<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function addToFavorite(Request $request)
    {
        try {
            if (auth('sanctum')->check()) {
                $user_id = auth('sanctum')->user()->id;
                $character_id = $request->character_id;

                $characterCheck = Character::where('id', $character_id)->first();
                if ($characterCheck) {
                    if (Favorite::where('character_id', $character_id)->where('user_id', $user_id)->exists()) {
                        return response()->json(['message' => $characterCheck->name . 'ตัวละครมีอยู่แล้วในรายการโปรด',], 400);
                    } else {
                        Favorite::create([
                            'user_id' => $user_id,
                            'character_id' => $character_id,
                        ]);
                        return response()->json(['message' => 'เพิ่มตัวละครลงรายการโปรดสำเร็จ'], 200);
                    }
                } else {
                    return response()->json(['message' => 'ไม่พบตัวละคร'], 404);
                }
            } else {
                return response()->json(['message' => 'เข้าสู่ระบบเพื่อเพิ่มตัวละครลงรายการโปรด'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function viewFavorite()
    {
        try {
            $favorites = Favorite::with(['user'])->get();
            if ($favorites) {
                return response()->json(['favorites' => $favorites], 200);
            } else {
                return response()->json(['status' => 404, 'message' => 'ไม่พบรายการโปรดของผู้ใช้คนนี้'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function myFavorite()
    {
        try {
            $user_id = auth('sanctum')->user()->id;
            $favorites = Favorite::with(['user','character'])->where('user_id', $user_id)->get();
            if ($favorites) {
                return response()->json(['favorites' => $favorites], 200);
            } else {
                return response()->json(['status' => 404, 'message' => 'ไม่พบรายการโปรดของผู้ใช้คนนี้'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }


    public function viewFavoriteUser($slug)
    {
        try {
            $favorites = Favorite::with(['user', 'character'])
            ->whereHas('character', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->get();
            if ($favorites) {
                return response()->json(['favorites' => $favorites], 200);
            } else {
                return response()->json(['status' => 404, 'message' => 'ไม่พบรายการโปรดของผู้ใช้คนนี้'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function deleteFavoriteitem($favorite_id)
    {
        try {
            if (auth('sanctum')->check()) {
                $user_id = auth('sanctum')->user()->id;
                $favoriteitem = Favorite::where('id', $favorite_id)->where('user_id', $user_id)->first();
                if ($favoriteitem) {
                    $favoriteitem->delete();
                    return response()->json(['message' => 'ลบตัวละครในรายการโปรดสำเร็จสำเร็จ'], 200);
                } else {
                    return response()->json(['status' => 404, 'message' => 'ไม่พบตัวละครในรายการโปรด'], 404);
                }
            } else {
                return response()->json(['message' => 'เข้าสู่ระบบเพื่อดำเนินการต่อ'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

}
