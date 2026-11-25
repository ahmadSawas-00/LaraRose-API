<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete(); // رقم الطلب
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // المنتج
            $table->integer('quantity'); // الكمية المطلوبة من هذا المنتج
            $table->decimal('price', 8, 2); // سعر المنتج "وقت الشراء" لضمان ثبات الفاتورة إذا تغير السعر لاحقاً
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
