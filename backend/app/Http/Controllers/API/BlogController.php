<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\Favorite;
use App\Models\User;
use App\Models\Blog;


class BlogController extends Controller
{
    public function index()
    {
        try {
            $blogs = Blog::with(['user', 'comments'])->get();
            if ($blogs) {
                return response()->json(['blogs' => $blogs], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลบล็อกไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function blog_all()
    {
        try {
            $blogs = Blog::with(['user', 'comments'])->get();
            if ($blogs) {
                return response()->json(['blogs' => $blogs], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลบล็อกไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function blog_detail($name)
    {
        try {
            $blog = Blog::with(['user', 'comments'])->where('name', $name)->first();
            $blog_all = Blog::with(['user'])->inRandomOrder()->limit(4)->get();
            if ($blog) {
                $blog->increment('view', 1);
                return response()->json(['blog' => $blog, 'blog_all' => $blog_all], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลตัวละครไม่สำเร็จ'], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function blog_count()
    {
        try {
            $blogsCount = Blog::with(['user' => function ($query) {
                $query->select(['id', 'name']);
            }])
            ->selectRaw('user_id, count(*) as count')  // นับจำนวน blogs โดย group ตาม user_id
            ->groupBy('user_id')  // groupBy ตาม user_id
            ->get();

            return response()->json(['blogscount' => $blogsCount], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function myBlog()
    {
        try {
            $user_id = auth('sanctum')->user()->id;
            $blogs = Blog::with(['user', 'comments'])->where('user_id', $user_id)->get();
            if ($blogs) {
                return response()->json(['blogs' => $blogs], 200);
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
                        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        'name' => 'required|string|max:255',
                        'content' => 'required|string',
                        'view' => 'nullable|integer',
                    ],
                    [
                        'name.required' => 'กรุณากรอกชื่อ',
                        'content.required' => 'กรุณากรอกเนื้อหา',
                        'image.required' => 'กรุณาอัพโหลดรูปภาพ',
                        'image.image' => 'กรุณาอัพโหลดเป็นรูปภาพ',
                        'image.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpeg, png, jpg, gif, หรือ svg',
                        'image.max' => 'ขนาดรูปภาพต้องไม่เกิน 2MB',
                    ]
                );

                // ตรวจสอบ validation
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                // ดึง ID ผู้ใช้จาก token
                $user_id = auth('sanctum')->user()->id;

                // จัดการการอัปโหลดรูปภาพ
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = public_path('images/blog/' . $user_id);
                    $file->move($destinationPath, $filename);
                } else {
                    $filename = null;
                }

                // สร้างข้อมูลบล็อกใหม่
                $blog = Blog::create([
                    'user_id' => $user_id,
                    'name' => $request->name,
                    'content' => $request->content,
                    'image' => $filename,
                    'view' => $request->view ?? 0, // ตั้งค่า view เป็น 0 หากไม่ส่งมา
                ]);

                if ($blog) {
                    return response()->json(['message' => 'เพิ่มบล็อกสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'เพิ่มบล็อกไม่สำเร็จ'], 400);
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
                $blog = Blog::find($id);
                $user_id = auth('sanctum')->user()->id;
                if ($blog->user_id == $user_id) {
                    $blog = Blog::with(['user'])->where('id', $id)->first();
                    if ($blog) {
                        return response()->json(['blog' => $blog], 200);
                    } else {
                        return response()->json(['status' => 404, 'message' => 'ไม่พบบล็อก'], 404);
                    }
                } else {
                    return response()->json(['message' => 'คุณไม่ใช่เข้าของบล็อกนี้'], 401);
                }
            } else {
                return response()->json(['message' => 'ไม่พบการเข้าสู่ระบบ'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function update(Request $request, Blog $blog)
    {
        try {
            // ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
            if (auth('sanctum')->check()) {

                // ดึง ID ผู้ใช้จาก token
                $user_id = auth('sanctum')->user()->id;
                if ($blog->user_id == $user_id) {
                    $validator = Validator::make(
                        $request->all(),
                        [
                            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                            'name' => 'required|string|max:255',
                            'content' => 'required|string',
                            'view' => 'nullable|integer',
                        ],
                        [
                            'name.required' => 'กรุณากรอกชื่อ',
                            'content.required' => 'กรุณากรอกเนื้อหา',
                            'image.required' => 'กรุณาอัพโหลดรูปภาพ',
                            'image.image' => 'กรุณาอัพโหลดเป็นรูปภาพ',
                            'image.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpeg, png, jpg, gif, หรือ svg',
                            'image.max' => 'ขนาดรูปภาพต้องไม่เกิน 2MB',
                        ]
                    );

                    // ตรวจสอบ validation
                    if ($validator->fails()) {
                        return response()->json(['errors' => $validator->errors()], 422);
                    }

                    // จัดการการอัปโหลดรูปภาพ
                    if ($request->hasFile('image')) {
                        $destination = 'images/blog/' . $blog->user_id . '/' . $blog->image;
                        if (File::exists($destination)) {
                            File::delete($destination);
                        }
                        $file = $request->file('image');
                        $extension = $file->getClientOriginalExtension();
                        $filename = time() . '_' . uniqid() . '.' . $extension;
                        $destinationPath = 'images/blog/' . $blog->user_id;
                        $file->move($destinationPath, $filename);
                        $blog->image = $filename;
                    }

                    $blog->update();
                    if ($blog->fill($request->post())->save()) {
                        return response()->json(['message' => 'อัพเดทบล็อกสำเร็จ'], 200);
                    } else {
                        return response()->json(['message' => 'อัพเดทบล็อกไม่สำเร็จ'], 400);
                    }
                } else {
                    return response()->json(['message' => 'คุณไม่ใช่เข้าของบล็อกนี้'], 401);
                }
            } else {
                return response()->json(['message' => 'ไม่พบการเข้าสู่ระบบ'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Blog $blog)
    {
        try {
            if (auth('sanctum')->check()) {
                $user_id = auth('sanctum')->user()->id;
                if ($blog->user_id == $user_id) {
                    // ตรวจสอบว่ามีรูปภาพหรือไม่ และลบรูปภาพนั้น
                    $destination = 'images/blog/' . $blog->user_id . '/' . $blog->image;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }

                    if ($blog->delete()) {
                        return response()->json(['status' => 200, 'message' => 'ลบบล็อกสำเร็จ'], 200);
                    } else {
                        return response()->json(['message' => 'ลบบล็อกไม่สำเร็จ'], 400);
                    }
                } else {
                    return response()->json(['message' => 'คุณไม่ใช่เข้าของบล็อกนี้'], 401);
                }
            } else {
                return response()->json(['message' => 'ไม่พบการเข้าสู่ระบบ'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }
}
