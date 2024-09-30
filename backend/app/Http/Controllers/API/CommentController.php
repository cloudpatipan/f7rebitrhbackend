<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\Comment;
class CommentController extends Controller
{
    public function index()
    {
        try {
            $comments = Comment::with(['user', 'post'])->get();
            if ($comments) {
                return response()->json(['comments' => $comments], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลคอมเม้นไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function comment_all()
    {
        try {
            $comments = Comment::with(['user', 'post'])->get();
            if ($comments) {
                return response()->json(['comments' => $comments], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลคอมเม้นไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function comment_detail($name)
    {
        try {
            $comment = Comment::with(['user', 'post'])->where('name', $name)->first();
            $comment_all = Comment::with(['user', 'post'])->inRandomOrder()->limit(4)->get();
            if ($comment) {
                return response()->json(['comment' => $comment, 'comment_all' => $comment_all], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลตัวละครไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function myComment()
    {
        try {
            $user_id = auth('sanctum')->user()->id;
            $comments = Comment::with(['user', 'post'])->where('user_id', $user_id)->get();
            if ($comments) {
                return response()->json(['comments' => $comments], 200);
            } else {
                return response()->json(['status' => 404, 'message' => 'ไม่พบรายการโปรดของผู้ใช้คนนี้'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            // ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
            if (auth('sanctum')->check()) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'post_id' => 'required',
                        'content' => 'required|string',
                    ],
                    [
                        'content.required' => 'กรุณากรอกเนื้อหา',
                    ]
                );

                // ตรวจสอบ validation
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                // ดึง ID ผู้ใช้จาก token
                $user_id = auth('sanctum')->user()->id;

                // สร้างข้อมูลคอมเม้นใหม่
                $comment = Comment::create([
                    'post_id' => $request->post_id,
                    'user_id' => $user_id,
                    'content' => $request->content,
                ]);

                if ($comment) {
                    return response()->json(['message' => 'เพิ่มคอมเม้นสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'เพิ่มคอมเม้นไม่สำเร็จ'], 400);
                }
            } else {
                return response()->json(['message' => 'ไม่พบการเข้าสู่ระบบ'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            if (auth('sanctum')->check()) {
                $comment = Comment::find($id);
                $user_id = auth('sanctum')->user()->id;
                if ($comment->user_id == $user_id) {
                    $comment = Comment::with(['user', 'post'])->where('id', $id)->first();
                    if ($comment) {
                        return response()->json(['comment' => $comment], 200);
                    } else {
                        return response()->json(['status' => 404, 'message' => 'ไม่พบคอมเม้น'], 404);
                    }
                } else {
                    return response()->json(['message' => 'คุณไม่ใช่เข้าของคอมเม้นนี้'], 401);
                }
            } else {
                return response()->json(['message' => 'ไม่พบการเข้าสู่ระบบ'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function update(Request $request, Comment $comment)
    {
        try {
            // ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
            if (auth('sanctum')->check()) {
                // ดึง ID ผู้ใช้จาก token
                $user_id = auth('sanctum')->user()->id;
                if ($comment->user_id == $user_id) {
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'content' => 'required|string',
                        ],
                        [
                            'content.required' => 'กรุณากรอกเนื้อหา',
                        ]
                    );

                    // ตรวจสอบ validation
                    if ($validator->fails()) {
                        return response()->json(['errors' => $validator->errors()], 422);
                    }


                    $comment->update();
                    if ($comment->fill($request->post())->save()) {
                        return response()->json(['message' => 'อัพเดทคอมเม้นสำเร็จ'], 200);
                    } else {
                        return response()->json(['message' => 'อัพเดทคอมเม้นไม่สำเร็จ'], 400);
                    }
                } else {
                    return response()->json(['message' => 'คุณไม่ใช่เข้าของคอมเม้นนี้'], 401);
                }
            } else {
                return response()->json(['message' => 'ไม่พบการเข้าสู่ระบบ'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Comment $comment)
    {
        try {
            if (auth('sanctum')->check()) {
                $user_id = auth('sanctum')->user()->id;
                if ($comment->user_id == $user_id) {
                if ($comment->delete()) {
                    return response()->json(['status' => 200, 'message' => 'ลบคอมเม้นสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'ลบคอมเม้นไม่สำเร็จ'], 400);
                }
            } else {
                return response()->json(['message' => 'คุณไม่ใช่เข้าของคอมเม้นนี้'], 401);
            }
        } else {
            return response()->json(['message' => 'ไม่พบการเข้าสู่ระบบ'], 401);
        }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }
}
