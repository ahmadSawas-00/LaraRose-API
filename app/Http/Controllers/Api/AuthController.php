<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController 
{
    // 1. تسجيل مستخدم جديد
    public function register(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6', // يجب ألا تقل عن 6 أحرف
        ]);

        // إنشاء المستخدم في قاعدة البيانات وتشفير كلمة المرور
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // إنشاء الـ Token الخاص بتطبيق الموبايل
        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنشاء الحساب بنجاح',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }

    // 2. تسجيل الدخول
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // البحث عن المستخدم برقم الهاتف
        $user = User::where('phone', $request->phone)->first();

        // التحقق من وجود المستخدم وصحة كلمة المرور
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'رقم الهاتف أو كلمة المرور غير صحيحة'
            ], 401);
        }

        // إنشاء Token جديد
        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    // 3. عرض بيانات المستخدم الحالي (البروفايل)
    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }

    // 4. تسجيل الخروج (حذف الـ Token)
    public function logout(Request $request)
    {
        // حذف الـ Token الحالي الذي يستخدمه الموبايل
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    // تحديث بيانات الملف الشخصي (الاسم أو كلمة المرور)
    public function updateProfile(Request $request)
    {
        // جلب الزبون الحالي الذي أرسل الطلب
        $user = $request->user();

        // التحقق من صحة البيانات المرسلة
        $request->validate([
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6', // اختياري في حال أراد تغيير الباسورد فقط
        ]);

        // إذا أرسل اسماً جديداً، نقوم بتحديثه
        if ($request->has('name') && $request->name != '') {
            $user->name = $request->name;
        }

        // إذا أرسل كلمة مرور جديدة، نقوم بتشفيرها وتحديثها
        if ($request->has('password') && $request->password != '') {
            $user->password = bcrypt($request->password);
        }

        // حفظ التعديلات في قاعدة البيانات
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث بيانات البروفايل بنجاح',
            'data' => $user
        ]);
    }
}