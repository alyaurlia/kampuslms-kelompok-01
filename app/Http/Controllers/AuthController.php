<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nim'      => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'nim.required'      => 'NIM wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->route(Auth::user()->role . '.dashboard');
        }

        return back()
            ->withErrors(['nim' => 'NIM atau kata sandi salah.'])
            ->onlyInput('nim');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}