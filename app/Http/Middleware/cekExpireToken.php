<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class cekExpireToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('api/*')) {
            if (date('Y-m-d H:i:s') <= Auth::user()->expire_token) {
                return $next($request);
            } else {
                $httpStatus = 401;
                $res['message'] = 'Token Expire, Mohon gunakan Token yang Valid';
                $res['status'] = 'Failed';
                $res['http_status'] = $httpStatus;
                return response()->json($res, $httpStatus);
            }
        }
    }
}
