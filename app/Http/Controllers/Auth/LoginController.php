<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Lockout login manual berbasis RateLimiter bawaan Laravel — pengganti
     * trait ThrottlesLogins yang sudah tidak ada lagi di Laravel 12.
     *
     * Kuncinya kombinasi username+IP, BUKAN IP saja, supaya brute-force di
     * satu akun tidak ikut mengunci akun/staf lain yang login dari IP yang
     * sama (mis. satu kantor di belakang NAT/router yang sama).
     */
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 60;

    private function throttleKey(Request $request): string
    {
        return Str::lower($request->input('username')) . '|' . $request->ip();
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route($this->redirectByRole());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login untuk akun ini. Coba lagi dalam {$seconds} detik.",
            ])->onlyInput('username');
        }

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            return redirect()->route($this->redirectByRole())
                ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        RateLimiter::hit($key, self::DECAY_SECONDS);

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }

    private function redirectByRole(): string
    {
        $user = Auth::user();

        if ($user->hasRole('staf_unit')) {
            return 'staf.pengajuan.index';      // prefix staf. + pengajuan.index
        }

        if ($user->hasRole('kasium')) {
            return 'kasium.verifikasi.index';   // prefix kasium. + verifikasi.index
        }

        if ($user->hasRole('pimpinan')) {
            return 'pimpinan.approval.index';   // prefix pimpinan. + approval.index
        }

        return 'dashboard.index';
    }
}
