<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;

class FavoriteController
{
    // جلب قائمة المفضلة للمستخدم الحالي
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites;

        return response()->json([
            'status' => 'success',
            'data' => $favorites
        ]);
    }

    // إضافة أو إزالة المنتج من المفضلة (بضغطة زر واحدة)
    public function toggle(Request $request, $productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'المنتج غير موجود'], 404);
        }

        // دالة toggle الذكية: تضيف المنتج إذا لم يكن موجوداً، وتحذفه إذا كان موجوداً
        $request->user()->favorites()->toggle($productId);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث المفضلة بنجاح'
        ]);
    }
}