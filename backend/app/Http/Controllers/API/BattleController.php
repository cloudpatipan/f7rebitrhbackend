<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Battle;

class BattleController extends Controller
{
    public function index()
    {
        try {
            $battles = Battle::all();
            if ($battles) {
                return response()->json(['battles' => $battles], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลแบทเทิลไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function battle_all()
    {
        try {
            $battles = Battle::all();
            if ($battles) {
                return response()->json(['battles' => $battles], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลแบทเทิลไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function detail($name)
    {
        try {
            $battle = Battle::where('name', $name)->first();
            if ($battle) {
                return response()->json(['battle' => $battle], 200);
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
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|string',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'description.required' => 'กรุณากรอกคำรายละเอียด',
                'image.required' => 'กรุณา URL รูปภาพ',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {

            try {
                $input = $request->all();

                if (Battle::create($input)) {
                    return response()->json(['message' => 'เพิ่มแบทเทิลสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'เพิ่มแบทเทิลไม่สำเร็จ'], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
            }
        }
    }


    public function show($id)
    {
        try {
            $battle = Battle::where('id', $id)->first();
            if ($battle) {
                return response()->json(['battle' => $battle], 200);
            } else {
                return response()->json(['status' => 404, 'message' => 'ไม่พบแบทเทิล'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function update(Request $request, Battle $battle)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'required|string',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'description.required' => 'กรุณากรอกคำรายละเอียด',
                'image.required' => 'กรุณา URL รูปภาพ',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {

            try {

                $battle->update();
                if ($battle->fill($request->post())->save()) {
                    return response()->json(['message' => 'อัพเดทแบทเทิลสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'อัพเดทแบทเทิลไม่สำเร็จ'], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
            }

        }
    }



    public function destroy(Battle $battle)
    {
        try {
            if ($battle->delete()) {
                return response()->json(['message' => 'ลบแบทเทิลสำเร็จ'], 200);
            } else {
                return response()->json(['message' => 'ลบแบทเทิลไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }

    }
}
