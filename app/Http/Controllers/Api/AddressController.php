<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Address;

class AddressController
{
    // جلب عناوين المستخدم الحالي
    public function index(Request $request)
    {
        // نستخدم ()user->addresses لجلب عناوين هذا المستخدم فقط وليس كل العناوين في التطبيق
        $addresses = $request->user()->addresses; 

        return response()->json([
            'status' => 'success',
            'data' => $addresses
        ]);
    }

    // إضافة عنوان جديد
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string',
            'details' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // إنشاء العنوان وربطه بالمستخدم الحالي فوراً
        $address = $request->user()->addresses()->create([
            'title' => $request->title,
            'details' => $request->details,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة العنوان بنجاح',
            'data' => $address
        ], 201);
    }

    // حذف عنوان
    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->find($id);

        if (!$address) {
            return response()->json(['status' => 'error', 'message' => 'العنوان غير موجود'], 404);
        }

        $address->delete();

        return response()->json(['status' => 'success', 'message' => 'تم حذف العنوان بنجاح']);
    }
}