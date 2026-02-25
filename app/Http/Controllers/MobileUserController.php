<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class MobileUserController extends Controller
{
    /**
     * Register a new user from the mobile app.
     * POST /api/mobile/register
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = auth('api')->attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil melakukan registrasi',
            'data'    => [
                'user'       => $user,
                'token'      => $token,
                'type'       => 'Bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ],
        ], 201);
    }

    /**
     * Login and return a JWT token.
     * POST /api/mobile/login
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $token = auth('api')->attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        if (!$token) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah',
                'data'    => null,
            ], 401);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil melakukan log in',
            'data'    => [
                'token'      => $token,
                'type'       => 'Bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ],
        ], 200);
    }

    /**
     * Invalidate the current JWT token (logout).
     * POST /api/mobile/logout
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal melakukan log out',
                'data'    => null,
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil melakukan log out',
            'data'    => [],
        ], 200);
    }

    /**
     * Return the currently authenticated user.
     * GET /api/mobile/me
     */
    public function me()
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mendapatkan pengguna',
            'data'    => [
                'user' => auth('api')->user(),
            ],
        ], 200);
    }
}