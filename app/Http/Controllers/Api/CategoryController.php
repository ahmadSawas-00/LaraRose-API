<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;

class CategoryController
{
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
}