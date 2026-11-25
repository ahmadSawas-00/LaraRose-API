<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'product_id', 'quantity'];

    // لكي نجلب بيانات المنتج (اسمه، سعره، صورته) مع عنصر السلة
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}