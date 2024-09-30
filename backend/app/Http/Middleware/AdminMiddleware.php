<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // ตรวจสอบว่าผู้ใช้เข้าสู่ระบบอยู่หรือไม่
        if (auth()->check()) {
            // ตรวจสอบว่าผู้ใช้มีสิทธิ์ 'server:admin'
            if (auth()->user()->tokenCan('server:admin')) {
                return $next($request);
            } else {
                // ส่งข้อผิดพลาด 403 ถ้าผู้ใช้ไม่มีสิทธิ์
                return response()->json([
                    'message' => 'ไม่มีสิทธิ์เข้าใช้งาน! คุณต้องมีระดับ แอดมิน',
                ], 403);
            }
        } else {
            // ส่งข้อผิดพลาด 401 ถ้าผู้ใช้ยังไม่ได้เข้าสู่ระบบ
            return response()->json([
                'status' => 401,
                'message' => 'กรุณาเข้าสู่ระบบก่อน',
            ], 401);
        }
    }    
}
