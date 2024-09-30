<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Character;
use App\Models\Favorite;

class CharacterController extends Controller
{
    public function index()
    {
        try {
            $characters = Character::with('role')->get();
            if ($characters) {
                return response()->json(['characters' => $characters], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลตัวละครไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }

    }

    public function character_all()
    {
        try {
            $characters = Character::with('role')->get();
            if ($characters) {
                return response()->json(['characters' => $characters], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลตัวละครไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }

    }

    public function detail($slug)
    {
        try {
            $character = Character::with('role')->where('slug', $slug)->first();
            if ($character) {
                return response()->json(['character' => $character], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลตัวละครไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'avatar' => 'required|image',
                'image' => 'nullable|image',
                'name' => 'required|string|max:255',
                'voice_actor' => 'required|string|max:255',
                'description' => 'required|string',
                'background' => 'required|image',
                'role_id' => 'required|exists:roles,id',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'description.required' => 'กรุณากรอกรายละเอียด',
                'voice_actor.required' => 'กรุณากรอกนักพากย์',
                'role_id.required' => 'กรุณาเลือกประเภท',
                'role_id.exists' => 'บทบาทที่เลือกไม่มีอยู่ในระบบ',
                'avatar.required' => 'กรุณาอัพโหลดรูปภาพตัวละคร',
                'avatar.image' => 'กรุณาอัพโหลดเป็นรูปภาพตัวละคร',
                'avatar.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpeg, png, jpg, gif, หรือ svg',
                'image.required' => 'กรุณาอัพโหลดรูปภาพเสริม',
                'image.image' => 'กรุณาอัพโหลดเป็นรูปภาพเสริม',
                'image.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpeg, png, jpg, gif, หรือ svg',
                'image.max' => 'ขนาดรูปภาพต้องไม่เกิน 2MB',
                'background.required' => 'กรุณาอัพโหลดรูปภาพพื้นหลัง',
                'background.image' => 'กรุณาอัพโหลดเป็นรูปภาพ',
                'background.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpeg, png, jpg, gif, หรือ svg',
                'background.max' => 'ขนาดรูปภาพต้องไม่เกิน 2MB',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {

            try {
                $input = $request->all();

                $input['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->input('name')), '-'));

                if ($request->hasFile('avatar')) {
                    $file = $request->file('avatar');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = 'images/character/' . $input['slug'];
                    $file->move($destinationPath, $filename);
                    $input['avatar'] = $filename;
                }

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = 'images/character/' . $input['slug'];
                    $file->move($destinationPath, $filename);
                    $input['image'] = $filename;
                }

                if ($request->hasFile('background')) {
                    $file = $request->file('background');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = 'images/character/' . $input['slug'];
                    $file->move($destinationPath, $filename);
                    $input['background'] = $filename;
                }

                if (Character::create($input)) {
                    return response()->json(['message' => 'เพิ่มตัวละครสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'เพิ่มตัวละครไม่สำเร็จ'], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
            }
        }
    }

    public function show($id)
    {
        try {
            $character = Character::with('role')->where('id', $id)->first();
            if ($character) {
                return response()->json(['character' => $character], 200);
            } else {
                return response()->json(['status' => 404, 'message' => 'ไม่พบตัวละคร'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function update(Request $request, Character $character)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'avatar' => 'nullable|image',
                'image' => 'nullable|image',
                'name' => 'required|string|max:255',
                'voice_actor' => 'required|string|max:255',
                'description' => 'required|string',
                'background' => 'nullable|image',
                'role_id' => 'required|exists:roles,id',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'description.required' => 'กรุณากรอกรายละเอียด',
                'voice_actor.required' => 'กรุณากรอกนักพากย์',
                'role_id.required' => 'กรุณาเลือกประเภท',
                'role_id.exists' => 'บทบาทที่เลือกไม่มีอยู่ในระบบ',
                'image.required' => 'กรุณาอัพโหลดรูปภาพ',
                'image.image' => 'กรุณาอัพโหลดเป็นรูปภาพตัวละคร',
                'image.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpeg, png, jpg, gif, หรือ svg',
                'image.max' => 'ขนาดรูปภาพต้องไม่เกิน 2MB',
                'background.required' => 'กรุณาอัพโหลดรูปภาพพื้นหลัง',
                'background.image' => 'กรุณาอัพโหลดเป็นรูปภาพ',
                'background.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpeg, png, jpg, gif, หรือ svg',
                'background.max' => 'ขนาดรูปภาพต้องไม่เกิน 2MB',
            ]
        );

        if ($validator->fails()) {

            return response()->json(['errors' => $validator->errors(), 422]);
        } else {
            try {
                $oldSlug = $character->slug;

                $newSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->input('name')), '-'));

                if ($oldSlug !== $newSlug) {
                    // เปลี่ยนชื่อ Slug เมื่อถูกเปลี่ยน
                    $oldPath = 'images/character/' . $oldSlug;
                    $newPath = 'images/character/' . $newSlug;

                    if (File::exists($oldPath)) {
                        File::moveDirectory($oldPath, $newPath);
                    }
                }


                $character->slug = $newSlug;

                if ($request->hasFile('avatar')) {
                    $destination = 'images/character/' . $character->slug . '/' . $character->avatar;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }

                    $file = $request->file('avatar');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = 'images/character/' . $character->slug;
                    $file->move($destinationPath, $filename);
                    $character->avatar = $filename;
                }

                if ($request->hasFile('image')) {
                    $destination = 'images/character/' . $character->slug . '/' . $character->image;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }

                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = 'images/character/' . $character->slug;
                    $file->move($destinationPath, $filename);
                    $character->image = $filename;
                }

                if ($request->hasFile('background')) {
                    $destination = 'images/character/' . $character->slug . '/' . $character->background;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }

                    $file = $request->file('background');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = 'images/character/' . $character->slug;
                    $file->move($destinationPath, $filename);
                    $character->background = $filename;
                }

                $character->update();
                if ($character->fill($request->post())->save()) {
                    return response()->json(['message' => 'อัพเดทตัวละครสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'อัพเดทตัวละครไม่สำเร็จ'], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
            }
        }
    }

    public function destroy(Character $character)
    {

        try {
            // ตรวจสอบว่ามีรูปภาพหรือไม่ และลบรูปภาพนั้น
            $destination = 'images/character/' . $character->slug . '/' . $character->avatar;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            $destination = 'images/character/' . $character->slug . '/' . $character->image;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            $destination = 'images/character/' . $character->slug . '/' . $character->background;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            if ($character->delete()) {
                return response()->json(['message' => 'ลบตัวละครสำเร็จ'], 200);
            } else {
                return response()->json(['message' => 'ลบตัวละครไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }
    public function favorite_count()
    {
        try {
            $favoritesCount = Favorite::select('character_id')
                ->selectRaw('count(*) as count')
                ->groupBy('character_id')
                ->get();

                return response()->json(['favoritescount' => $favoritesCount], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }
}
