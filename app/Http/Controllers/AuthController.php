<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nim_nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'nim_nip.required' => 'NIM/NIP wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->route(Auth::user()->role . '.dashboard');
        }

        return back()
            ->withErrors(['nim_nip' => 'NIM/NIP atau kata sandi salah.'])
            ->onlyInput('nim_nip');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}