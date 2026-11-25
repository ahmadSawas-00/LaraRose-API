<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق مما إذا كان المستخدم مسجل دخول وصلاحيته "admin"
        if ($request->user() && $request->user()->role === 'admin') {
            return $next($request); // تفضل بالدخول
        }

        // إذا كان زبوناً عادياً أو غير مسجل، اطرده برسالة خطأ
        return response()->json([
            'status' => 'error',
            'message' => 'غير مصرح لك بالدخول، هذه الصلاحية لمدير المتجر فقط'
        ], 403);
    }
}
