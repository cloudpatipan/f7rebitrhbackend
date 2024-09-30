<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::all();
            if ($users) {
                return response()->json(['users' => $users], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลผู้ใช้ไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function user_all()
    {
        try {
            $users = User::all();
            if ($users) {
                return response()->json(['users' => $users], 200);
            } else {
                return response()->json(['message' => 'ดึงข้อมูลผู้ใช้ไม่สำเร็จ'], 400);
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
                'email' => 'required|string|email|max:255|unique:users',
                'avatar' => 'nullable|image',
                'password' => 'required|string|min:8',
                'role' => 'boolean',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'email.required' => 'กรุณากรอกอีเมล',
                'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                'email.max' => 'ความยาวของอีเมลต้องไม่เกิน :max ตัวอักษร',
                'email.unique' => 'อีเมลนี้ถูกใช้ไปแล้ว',
                'password.required' => 'กรุณากรอกรหัสผ่าน',
                'password.min' => 'รหัสผ่านต้องมีอย่างน้อย :min ตัวอักษร',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {

            try {
                if ($request->hasFile('avatar')) {
                    $profileName = time() . '.' . $request->avatar->getClientOriginalExtension();
                    $request->avatar->move(public_path('images/avatar'), $profileName);

                    $request->avatar = $profileName;
                }

                $user = User::Create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'avatar' => $request->avatar,
                    'password' => Hash::make($request->password),
                ]);

                if ($user) {
                    return response()->json([
                        'status' => 200,
                        'user' => $user,
                        'message' => 'สมัครสมาชิกสำเร็จ'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'สมัครสมาชิกไม่สำเร็จ'
                    ], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
            }

        }
    }

    public function show($id)
    {
        try {
            $user = User::where('id', $id)->first();
            if ($user) {
                return response()->json(['user' => $user], 200);
            } else {
                return response()->json(['message' => 'ไม่พบผู้ใช้'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'avatar' => 'nullable|image',
                'password' => 'required|string|min:8',
                'role' => 'boolean',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อ',
                'email.required' => 'กรุณากรอกอีเมล',
                'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                'email.max' => 'ความยาวของอีเมลต้องไม่เกิน :max ตัวอักษร',
                'email.unique' => 'อีเมลนี้ถูกใช้ไปแล้ว',
                'password.required' => 'กรุณากรอกรหัสผ่าน',
                'password.min' => 'รหัสผ่านต้องมีอย่างน้อย :min ตัวอักษร',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {

            try {
                if ($request->hasFile('avatar')) {
                    $destination = 'images/avatar/' . $user->avatar;
                    if (File::exists($destination)) {
                        File::delete($destination);
                    }

                    $profileName = time() . '.' . $request->avatar->getClientOriginalExtension();
                    $request->avatar->move(public_path('images/avatar'), $profileName);

                    $user->avatar = $profileName;
                }

                $user->update();
                ([
                    'name' => $request->name,
                    'email' => $request->email,
                    'avatar' => $request->avatar,
                    'password' => Hash::make($request->password),
                    'role' => $request->input('role') ? '1' : '0',
                ]);

                $user->update();
                if ($user->fill($request->post())->save()) {
                    return response()->json(['message' => 'อัพเดทสมาชิกสำเร็จ'], 200);
                } else {
                    return response()->json(['message' => 'อัพเดทสมาชิกไม่สำเร็จ'], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
            }

        }
    }

    public function destroy(User $user)
    {
        try {
            // ตรวจสอบว่ามีรูปภาพหรือไม่ และลบรูปภาพนั้น
            $destination = 'images/avatar/' . $user->avatar;
            if (File::exists($destination)) {
                File::delete($destination);
            }

            if ($user->delete()) {
                return response()->json(['message' => 'ลบผู้ใช้สำเร็จ'], 200);
            } else {
                return response()->json(['message' => 'ลบผู้ใช้ไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

}
