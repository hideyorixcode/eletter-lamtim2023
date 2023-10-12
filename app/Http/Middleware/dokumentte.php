<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class dokumentte
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
        if (Auth::check()) {
            $level = auth()->user()->level;
            if (auth()->user()->active == 1) :
                if (in_array($level, ['adpim', 'admin', 'superadmin', 'sespri', 'umum'])) {
                    return $next($request);
                } else {
                    if ($request->ajax()) {
                        return response()->json('tidak dapat mengakses halaman ini ' . $request->fullUrl() . '.', 403);
                    } else {
                        return redirect('dashboard')
                            ->with('pesan_status', [
                                'tipe' => 'error',
                                'desc' => 'Tidak Dapat Mengakses Halaman ini',
                                'title' => 'Halaman Khusus SUPERADMIN|ADMIN|PROTOKOL PIMPINAN|UMUM|SESPRI'
                            ]);
                    }
                }
            else :
                if ($request->ajax()) {
                    return response()->json('tidak dapat mengakses halaman ini ' . $request->fullUrl() . '.', 403);
                } else {
                    return redirect('/')
                        ->with('pesan_status', [
                            'tipe' => 'error',
                            'desc' => 'Tidak Dapat Mengakses Halaman ini',
                            'title' => 'akun anda belum aktif'
                        ]);
                }
            endif;
        }
    }
}
