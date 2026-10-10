<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Form "Lupa kata sandi" (isi email).
     */
    public function requestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim tautan reset ke email.
     */
    public function sendLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        // Hasilnya sengaja diabaikan: respons selalu sama, baik email terdaftar
        // maupun tidak, supaya tidak bisa dipakai untuk menebak akun (user enumeration).
        Password::sendResetLink(['email' => mb_strtolower(trim($request->email))]);

        return back()->with(
            'success',
            'Jika email tersebut terdaftar, tautan untuk mengatur ulang kata sandi telah dikirim.'
        );
    }

    /**
     * Form kata sandi baru (dibuka dari tautan di email).
     */
    public function resetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Simpan kata sandi baru.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $status = Password::reset(
            [
                'email'                 => mb_strtolower(trim($request->email)),
                'password'              => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token'                 => $request->token,
            ],
            function ($user, string $password) {
                // Cast 'hashed' pada model User yang meng-hash password.
                $user->forceFill([
                    'password'       => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                // Putus semua sesi lama akun ini (berlaku bila SESSION_DRIVER=database).
                if (config('session.driver') === 'database') {
                    DB::table(config('session.table', 'sessions'))
                        ->where('user_id', $user->getKey())
                        ->delete();
                }

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('success', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru.');
        }

        // Token salah, kedaluwarsa, atau email tidak cocok: satu pesan yang sama.
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.']);
    }
}