<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAuthController extends Controller
{
    /** Tampilkan halaman login */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin(Auth::user());
        }
        return view('auth.login');
    }

    /** Proses login web session */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Cek banned
        if ($user->is_banned) {
            Auth::logout();
            $request->session()->invalidate();
            return back()->withErrors([
                'email' => 'Akun kamu telah dibanned.' .
                    ($user->ban_reason ? ' Alasan: ' . $user->ban_reason : ''),
            ]);
        }

        return $this->redirectAfterLogin($user);
    }

    /** Logout web session */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'Kamu berhasil logout.');
    }

    /** Redirect berdasarkan role */
    private function redirectAfterLogin($user)
    {
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    }
}
