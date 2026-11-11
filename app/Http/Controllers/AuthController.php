<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            return $user->role === 'petugas'
                ? redirect()->route('petugas.dashboard')
                : redirect()->route('siswa.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'petugas') {
                return redirect()->intended(route('petugas.dashboard'))
                    ->with('success', "Selamat datang kembali, Petugas {$user->name}!");
            }

            return redirect()->intended(route('siswa.dashboard'))
                ->with('success', "Selamat datang, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}
