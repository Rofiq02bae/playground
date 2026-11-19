<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tujuan setelah login, sesuai role (RBAC):
     * admin (perusahaan) -> panel admin, pemilik/penggarap -> webapp.
     */
    private function homeFor($user): string
    {
        return $user->isAdmin()
            ? route('admin.articles.index')
            : route('app.home');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->homeFor(Auth::user()));
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Login bisa pakai email atau username (pemilik/penggarap di
        // lapangan belum tentu punya email, akunnya dibuatkan perusahaan).
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$field => $data['login'], 'password' => $data['password']], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => 'Email/username atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->homeFor(Auth::user()));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
