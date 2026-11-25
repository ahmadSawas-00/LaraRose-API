<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // هذه الدالة السحرية تقوم بتعديل مسار الصورة ليصبح رابطاً كاملاً يعمل في الموبايل
    protected function mainImage(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? url('storage/' . $value) : null,
        );
    }
}
