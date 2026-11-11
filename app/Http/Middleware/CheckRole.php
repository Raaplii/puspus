<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! in_array($user->role, $roles, true)) {
            // Redirect user to their own dashboard
            if ($user->role === 'petugas') {
                return redirect()->route('petugas.dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki akses ke halaman siswa.');
            }

            return redirect()->route('siswa.dashboard')->with('error', 'Akses ditolak: Halaman pengawasan hanya untuk petugas/penjaga perpustakaan.');
        }

        return $next($request);
    }
}
