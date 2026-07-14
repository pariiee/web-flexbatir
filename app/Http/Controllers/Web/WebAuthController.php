<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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

    /** Tampilkan halaman register */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin(Auth::user());
        }
        return view('auth.register');
    }

    /** Proses registrasi */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'username.unique'    => 'Username sudah dipakai.',
            'username.alpha_dash'=> 'Username hanya boleh huruf, angka, strip, dan underscore.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard')
            ->with('status', 'Akun berhasil dibuat. Selamat datang, ' . $user->name . '!');
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
