<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController
{
    // 1. جلب محتويات السلة للزبون الحالي
    public function index(Request $request)
    {
        // نجلب السلة مع بيانات المنتج لتظهر في الموبايل
        $cartItems = Cart::with('product')->where('user_id', $request->user()->id)->get();

        return response()->json([
            'status' => 'success',
            'data' => $cartItems
        ]);
    }

    // 2. إضافة منتج للسلة (أو زيادة كميته إذا كان موجوداً)
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $userId = $request->user()->id;
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        // البحث هل المنتج موجود مسبقاً في سلة هذا الزبون؟
        $cartItem = Cart::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($cartItem) {
            // إذا موجود، نزيد الكمية فقط
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // إذا غير موجود، نضيفه كعنصر جديد
            $cartItem = Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة المنتج للسلة',
            'data' => $cartItem
        ]);
    }

    // 3. حذف منتج من السلة
    public function remove(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', $request->user()->id)->where('id', $id)->first();

        if (!$cartItem) {
            return response()->json(['status' => 'error', 'message' => 'العنصر غير موجود في السلة'], 404);
        }

        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم الحذف من السلة'
        ]);
    }
}