<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** Tujuan pulang per role: pembeli ke toko, admin ke panel, pemilik/penggarap ke webapp. */
    private function homeFor(User $user): string
    {
        if ($user->isAdmin()) {
            return route('admin.articles.index');
        }

        if ($user->isPembeli()) {
            return route('shop.index');
        }

        return route('app.home');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->homeFor(Auth::user()));
        }

        return view('shop.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$field => $data['login'], 'password' => $data['password']], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => 'Email/username atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->homeFor(Auth::user()));
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect($this->homeFor(Auth::user()));
        }

        return view('shop.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'username' => $this->uniqueUsername($data['email']),
            'password' => $data['password'],
            'role' => User::ROLE_PEMBELI,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('shop.index'))
            ->with('success', 'Akun pembeli dibuat. Selamat datang di toko FunFarm!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.index');
    }

    /** Username otomatis dari bagian depan email (unik, ditambah sufiks bila sudah dipakai). */
    private function uniqueUsername(string $email): string
    {
        $base = Str::slug(Str::before($email, '@'), '_') ?: 'pembeli';
        $username = $base;
        $i = 2;

        while (User::query()->where('username', $username)->exists()) {
            $username = $base.'_'.$i++;
        }

        return $username;
    }
}
