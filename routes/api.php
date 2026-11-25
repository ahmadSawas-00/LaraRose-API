<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// استدعاء المتحكمات (سنقوم بإنشائها في الخطوة القادمة)
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\CartController;

/*
|--------------------------------------------------------------------------
| المسارات العامة (Public Routes) - لا تحتاج تسجيل دخول
|--------------------------------------------------------------------------
*/

// المصادقة (التسجيل والدخول)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// استعراض الكتالوج (التصنيفات والمنتجات)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']); // تفاصيل منتج معين


/*
|--------------------------------------------------------------------------
| المسارات المحمية (Protected Routes) - تحتاج Token (تسجيل دخول)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // إدارة الحساب
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // إدارة العناوين (يختصر مسارات الإضافة والتعديل والحذف)
    Route::apiResource('addresses', AddressController::class);

    // الطلبات (Orders)
    Route::get('/orders', [OrderController::class, 'index']); // عرض كل طلبات المستخدم
    Route::get('/orders/{id}', [OrderController::class, 'show']); // تفاصيل طلب معين
    Route::post('/orders', [OrderController::class, 'store']); // إنشاء طلب جديد (الشراء)
        // المفضلة
    // المفضلة (يجب أن تكون داخل هذا القوس)
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{productId}', [FavoriteController::class, 'toggle']);

    Route::post('/products', [AdminController::class, 'storeProduct']); // الرابط الجديد

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'add']);
    Route::delete('/cart/{id}', [CartController::class, 'remove']);
});

/*
|--------------------------------------------------------------------------
| مسارات لوحة التحكم (Admin Panel) - تحتاج Token وأن يكون المستخدم Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('admin')->group(function () {
    
    // إدارة الطلبات
    Route::get('/orders', [AdminController::class, 'allOrders']); // جلب كل طلبات المتجر
    Route::put('/orders/{id}/status', [AdminController::class, 'updateOrderStatus']); // تعديل حالة الطلب
    Route::post('/products', [AdminController::class, 'storeProduct']); // الرابط الجديد
    // إدارة المنتجات
    Route::post('/products', [AdminController::class, 'storeProduct']);
    Route::delete('/products/{id}', [AdminController::class, 'destroyProduct']); // رابط الحذف
    });

