<?php

namespace App\Http\Controllers\Api;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController
{
    public function store(Request $request)
    {
        // التحقق من أن الزبون أدخل كافة البيانات المطلوبة
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string'
        ]);

        // حفظ الرسالة في قاعدة البيانات
        ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إرسال رسالتك بنجاح، سنتواصل معك قريباً'
        ], 201);
    }
}