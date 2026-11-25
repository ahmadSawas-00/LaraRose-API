<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderController
{
    // عرض جميع طلبات المستخدم (شاشة طلباتي)
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    // عرض تفاصيل طلب معين (شاشة تتبع الطلب)
    public function show(Request $request, $id)
    {
        // جلب الطلب مع المنتجات التي بداخله
        $order = $request->user()->orders()->with('items.product')->find($id);

        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'الطلب غير موجود'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    // إنشاء طلب جديد (عملية الشراء)
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'items' => 'required|array', // المصفوفة التي يرسلها الموبايل وتحتوي على المنتجات والسلة
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // نبدأ Transaction لضمان عدم حفظ بيانات ناقصة
        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $orderItems = [];

            // 1. حساب الإجمالي وتجهيز المنتجات
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                // نعتمد سعر الخصم إن وجد، وإلا السعر الأساسي
                $price = $product->discount_price ?? $product->price; 
                
                $totalAmount += ($price * $item['quantity']);
                
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $price
                ];
            }

            // 2. إنشاء الطلب الأساسي
            $order = $request->user()->orders()->create([
                'address_id' => $request->address_id,
                'total_amount' => $totalAmount,
                'status' => 'pending' // قيد الانتظار
            ]);

            // 3. إدخال المنتجات داخل الطلب
            $order->items()->createMany($orderItems);

            // حفظ التعديلات في قاعدة البيانات
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم إرسال طلبك بنجاح',
                'data' => $order->load('items')
            ], 201);

        } catch (\Exception $e) {
            // التراجع عن كل شيء في حال حدوث أي خطأ
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء معالجة الطلب'
            ], 500);
        }
    }
}