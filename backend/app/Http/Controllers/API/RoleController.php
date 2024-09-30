<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::all();
            if ($roles) {
                return response()->json(['roles' => $roles], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลบทบาทไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function role_all()
    {
        try {
            $roles = Role::all();
            if ($roles) {
                return response()->json(['role' => $roles], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลบทบาทไม่สำเร็จ'], 400);
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
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {

            try {
                $input = $request->all();

                $input['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->input('name')), '-'));

                if (Role::create($input)) {
                    return response()->json(['message' => 'เพิ่มบทบาทสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'เพิ่มบทบาทไม่สำเร็จ'], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
            }
        }
    }


    public function show($id)
    {
        try {
            $role = Role::where('id', $id)->first();
            if ($role) {
                return response()->json(['role' => $role], 200);
            } else {
                return response()->json(['status' => 404, 'message' => 'ไม่พบบทบาท'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function update(Request $request, Role $role)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {

            try {
                $role->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->input('name')), '-'));

                $role->update();
                if ($role->fill($request->post())->save()) {
                    return response()->json(['message' => 'อัพเดทบทบาทสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'อัพเดทบทบาทไม่สำเร็จ'], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
            }

        }
    }



    public function destroy(Role $role)
    {
        try {
            if ($role->delete()) {
                return response()->json(['message' => 'ลบบทบาทสำเร็จ'], 200);
            } else {
                return response()->json(['message' => 'ลบบทบาทไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }

    }
}
