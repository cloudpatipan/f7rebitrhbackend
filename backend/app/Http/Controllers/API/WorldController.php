<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\World;

class WorldController extends Controller
{
    public function index()
    {
        try {
            $worlds = World::all();
            if ($worlds) {
                return response()->json(['status' => 200, 'world' => $worlds], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลโลกไม่สำเร็จ'], status: 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }

    }

    public function world_all()
    {
        try {
            $worlds = World::all();
            if ($worlds) {
                return response()->json(['status' => 200, 'worlds' => $worlds], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลโลกไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function detail($slug)
    {
        try {
            $world = World::where('slug', $slug)->first();
            if ($world) {
                return response()->json(['status' => 200, 'world' => $world], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลโลกไม่สำเร็จ'], 400);
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
                'image' => 'required|image',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'description.required' => 'กรุณากรอกรายละเอียด',
                'image.required' => 'กรุณาอัพโหลดรูปภาพ',
                'image.image' => 'กรุณาอัพโหลดเป็นรูปภาพ',
                'image.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpeg, png, jpg, gif, หรือ svg',
                'image.max' => 'ขนาดรูปภาพต้องไม่เกิน 2MB',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {

            try {
                $input = $request->all();

                $input['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->input('name')), '-'));

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = 'images/world/' . $input['slug'];
                    $file->move($destinationPath, $filename);
                    $input['image'] = $filename;
                }

                if (World::create($input)) {
                    return response()->json(['status' => 200, 'message' => 'เพิ่มโลกสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'เพิ่มตัวโลกไม่สำเร็จ'], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
            }

        }
    }

    public function show($id)
    {
        try {
            $world = World::where('id', $id)->first();
            if ($world) {
                return response()->json(['status' => 200, 'world' => $world], 200);
            } else {
                return response()->json(['status' => 404, 'message' => 'ไม่พบโลก'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function update(Request $request, World $world)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'image' => 'nullable|image',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'description.required' => 'กรุณากรอกรายละเอียด',
                'image.required' => 'กรุณาอัพโหลดรูปภาพ',
                'image.image' => 'กรุณาอัพโหลดเป็นรูปภาพ',
                'image.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpeg, png, jpg, gif, หรือ svg',
                'image.max' => 'ขนาดรูปภาพต้องไม่เกิน 2MB',
            ]
        );

        if ($validator->fails()) {

            return response()->json(['errors' => $validator->errors()], 422);
        } else {

            try {
                $world->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->input('name')), '-'));

                if ($request->hasFile('image')) {
                    $destination = 'images/world/' . $world->slug . '/' . $world->image;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }

                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $destinationPath = 'images/world/' . $world->slug;
                    $file->move($destinationPath, $filename);
                    $world->image = $filename;
                }

                    $world->update();
                    if ($world->fill($request->post())->save()) {
                        return response()->json(['status' => 200, 'message' => 'อัพเดทโลกสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'อัพเดทโลกไม่สำเร็จ'], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
            }
        }
    }

    public function destroy(world $world)
    {
        try {
            // ตรวจสอบว่ามีรูปภาพหรือไม่ และลบรูปภาพนั้น

            $destination = 'images/world/' . $world->slug . '/' . $world->image;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            if ($world->delete()) {
                return response()->json(['status' => 200, 'message' => 'ลบโลกสำเร็จ'], 200);
            } else {
                return response()->json(['message' => 'ลบโลกไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }
}
