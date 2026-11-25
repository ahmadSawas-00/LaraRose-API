<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $guarded = []; // يسمح بإدخال البيانات لجميع الحقول

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}