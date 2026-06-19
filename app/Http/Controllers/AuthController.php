<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');
        $user = User::where('email', $credentials['email'])->first();

        if ($user && ! $user->is_active) {
            return back()
                ->withErrors(['email' => 'Akun belum aktif. Selesaikan pembayaran dan tunggu verifikasi admin. Informasi login akan dikirim ke email Anda.'])
                ->onlyInput('email');
        }

        if (! $user || ! Auth::attempt(array_merge($credentials, ['is_active' => true]), $remember)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return redirect()->route('participant.dashboard');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! User::where('email', $credentials['email'])->where('is_active', true)->exists()
            || ! Auth::attempt(array_merge($credentials, ['is_active' => true]), $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $roleName = optional(Auth::user()->role)->name;
        if (! in_array($roleName, ['super-admin', 'admin-lms'], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Akun ini bukan admin LMS.'])
                ->onlyInput('email');
        }

        return redirect()->intended(route('admin.courses.index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda sudah logout.');
    }
}
