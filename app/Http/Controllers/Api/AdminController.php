<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class AdminController
{
    // جلب جميع طلبات المتجر (مع اسم الزبون صاحب الطلب)
    public function allOrders()
    {
        $orders = Order::with('user:id,name,phone')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    // تحديث حالة الطلب
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'الطلب غير موجود'], 404);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث حالة الطلب بنجاح',
            'data' => $order
        ]);
    }

    // إضافة منتج جديد مع رفع الصورة
    public function storeProduct(Request $request)
    {
        // 1. التحقق من البيانات المرسلة (بما فيها الصورة)
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // يجب أن تكون صورة ولا تتجاوز 2 ميجا
        ]);

        // 2. معالجة وحفظ الصورة
        $imagePath = null;
        if ($request->hasFile('main_image')) {
            // سيتم حفظ الصورة في مسار: storage/app/public/products
            $imagePath = $request->file('main_image')->store('products', 'public');
        }

        // 3. إدخال بيانات المنتج في قاعدة البيانات مع مسار الصورة
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'main_image' => $imagePath,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة المنتج بنجاح',
            'data' => $product
        ], 201);
    }

    // حذف المنتج مع صورته
    public function destroyProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'المنتج غير موجود'], 404);
        }

        // استخراج المسار النسبي للصورة من الرابط الكامل
        // بما أننا استخدمنا Accessor، يجب أن نتعامل مع القيمة الأصلية المحفوظة في قاعدة البيانات
        $imagePath = $product->getRawOriginal('main_image'); 

        // التحقق من وجود صورة للمنتج وحذفها من السيرفر
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        // حذف المنتج من قاعدة البيانات
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف المنتج وصورته بنجاح'
        ]);
    }
}