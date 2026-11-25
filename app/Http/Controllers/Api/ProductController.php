<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
class ProductController
{
    // جلب كل المنتجات النشطة
    public function index(Request $request)
    {
        // نبدأ ببناء الطلب مع جلب اسم التصنيف ليفيد مطور الموبايل
        $query = Product::with('category:id,name');

        // 1. فلترة بالبحث النصي (في الاسم أو الوصف)
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // 2. فلترة برقم التصنيف (لجلب منتجات قسم معين فقط)
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // 3. فلترة حسب السعر (الحد الأدنى والأعلى)
        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('price', '<=', $request->max_price);
        }

        // جلب النتائج وترتيبها من الأحدث للأقدم
        $products = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    // عرض تفاصيل منتج معين
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنتج غير موجود'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }
}