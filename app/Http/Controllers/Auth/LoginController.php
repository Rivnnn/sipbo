<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
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

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->route($this->redirectByRole())
                ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

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
