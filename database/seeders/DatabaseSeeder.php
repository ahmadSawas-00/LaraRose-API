<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء مستخدم للتجربة (لكي تسجل دخول به لاحقاً)
        User::create([
            'name' => 'لورانس روز',
            'phone' => '0900000000',
            'password' => Hash::make('123456'), // الباسورد
            'role'=>'admin',
        ]);
        User::create([
            'name' => 'أحمد (زبون)',
            'phone' => '0911111111',
            'password' => Hash::make('123456'),
            // لا نحتاج لكتابة role هنا لأن الافتراضي هو user
        ]);

        // 2. إنشاء تصنيفات
        $perfumes = Category::create(['name' => 'عطور فاخرة', 'image' => 'التصنيفات 18.png']);
        $gifts = Category::create(['name' => 'باقات وهدايا', 'image' => 'التصنيفات 18.png']);

        // 3. إنشاء منتجات داخل تصنيف العطور
        Product::create([
            'category_id' => $perfumes->id,
            'name' => 'عطر العود الملكي',
            'description' => 'عطر شرقي فاخر بلمسات العود',
            'price' => 250.00,
            'stock_quantity' => 50,
            'main_image' => 'تفاصيل المنتج 5.png',
        ]);

        Product::create([
            'category_id' => $perfumes->id,
            'name' => 'عطر المسك الأبيض',
            'description' => 'عطر هادئ ومميز للاستخدام اليومي',
            'price' => 150.00,
            'discount_price' => 120.00, // سعر مخفض
            'stock_quantity' => 20,
            'main_image' => 'تفاصيل المنتج 5.png',
        ]);

        // 4. إنشاء منتجات داخل تصنيف الهدايا
        Product::create([
            'category_id' => $gifts->id,
            'name' => 'باقة الجوري الأحمر',
            'description' => 'باقة ورد جوري أحمر مع تغليف فاخر',
            'price' => 100.00,
            'stock_quantity' => 10,
            'main_image' => 'شراء الان 11.png',
        ]);
    }
}