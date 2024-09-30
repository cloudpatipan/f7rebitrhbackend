<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\User;

class ProfileController extends Controller
{
    public function viewprofile()
    {
        try {
            if (auth('sanctum')->check()) {
                $user = auth('sanctum')->user();
                return response()->json(['user' => $user], 200);
            } else {
                return response()->json(['message' => 'เข้าสู่ระบบเพื่อเข้าถึงโปรไฟล์'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }

    }

    public function updateprofile(Request $request)
    {

        if (auth('sanctum')->check()) {
            $validator = Validator::make(
                $request->all(),
                [
                    'avatar' => 'nullable|image',
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
                    $user = Auth::user();

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
                    if ($user->fill($request->post())->save()) {
                        return response()->json(['message' => 'อัพเดทโปรไฟล์สำเร็จ', 'user' => $user], 200);
                    } else {
                        return response()->json(['message' => 'อัพเดทโปรไฟล์ไม่สำเร็จ'], 400);
                    }
                } catch (\Exception $e) {
                    return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
                }
            }
        } else {
            return response()->json(['message' => 'กรุณาเข้าสู่ระบบเพื่อเข้าถึงโปรไฟล์'], 401);
        }

    }

    public function updatepassword(Request $request)
    {
        try {
            if (auth('sanctum')->check()) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'current_password' => 'required',
                        'new_password' => 'required|string|min:8|confirmed',
                        'new_password_confirmation' => 'required',
                    ],
                    [
                        'current_password.required' => 'กรุณากรอกรหัสผ่านปัจจุบัน',
                        'new_password.required' => 'กรุณากรอกรหัสผ่านใหม่',
                        'new_password.min' => 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
                        'new_password.confirmed' => 'ยืนยันรหัสผ่านใหม่ไม่ตรงกัน',
                        'new_password_confirmation.required' => 'กรุณกรอกยืนรหัสผ่านอีกครั้ง',
                    ]
                );

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                } else {

                    try {
                        $user = Auth::user();

                        if (!Hash::check($request->input('current_password'), $user->password)) {
                            return response()->json(['message' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง'], 400);
                        }

                        $user->update([
                            'password' => Hash::make($request->input('new_password')),
                        ]);

                        return response()->json(['message' => 'เปลี่ยนรหัสผ่านสำเร็จ'], 200);
                    } catch (\Exception $e) {
                        return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
                    }
                }

            } else {
                return response()->json(['message' => 'กรุณาเข้าสู่ระบบเพื่อเปลี่ยนรหัสผ่าน']);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'มีบางอย่างผิดพลาดจริงๆ!'], 500);
        }
    }

}
