<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** POST /api/v1/auth/login (publik, di-throttle 5/menit di route) */
    public function login(LoginRequest $request): JsonResponse
    {
        $login = trim((string) ($request->input('login') ?: $request->input('email')));

        // Berformat email -> cek kolom email, selain itu -> nim_nip
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim_nip';
        $value = $field === 'email' ? strtolower($login) : $login;

        $user = User::where($field, $value)->first();

        // SATU pesan untuk akun salah maupun password salah,
        // supaya penyerang tidak bisa menebak akun yang terdaftar.
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email/NIM/NIP atau kata sandi salah.'],
            ]);
        }

        $token = $user->createToken($request->device_name ?? 'api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }

    /** POST /api/v1/auth/logout (auth:sanctum): mencabut token yang sedang dipakai */
    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent(); // 204
    }

    /** GET /api/v1/me (auth:sanctum) */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}