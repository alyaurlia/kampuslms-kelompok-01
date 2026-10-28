<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required'    => 'NIM/NIP atau email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $login = trim($request->input('login'));

        // Berformat email -> cek kolom email, selain itu -> nim_nip
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        $field   = $isEmail ? 'email' : 'nim_nip';
        $value   = $isEmail ? strtolower($login) : $login;

        if (Auth::attempt(
            [$field => $value, 'password' => $request->input('password')],
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            return redirect()->route(Auth::user()->role . '.dashboard');
        }

        return back()
            ->withErrors(['login' => 'NIM/NIP, email, atau kata sandi salah.'])
            ->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}