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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // ربط العنوان بالمستخدم
            $table->string('title')->nullable(); // مثال: المنزل، العمل
            $table->string('details'); // تفاصيل العنوان نصياً
            $table->decimal('latitude', 10, 8)->nullable(); // خط العرض للخريطة
            $table->decimal('longitude', 11, 8)->nullable(); // خط الطول للخريطة
            $table->boolean('is_default')->default(false); // هل هو العنوان الافتراضي؟
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
