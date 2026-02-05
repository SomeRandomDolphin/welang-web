<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class MobileUserController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        $token = auth('api')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return response()->json([
            'status' => "success",
            'message' => "Berhasil melakukan registrasi",
            'data' => [
                'user' => $user,
                'token' => $token,
                'type' => 'Bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $token = auth('api')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($token) {
            return response()->json([
                'status' => "success",
                'message' => "Berhasil melakukan log in",
                'data' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60,
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Email atau password salah",
                'data' => null,
            ], 401);
        }
    }

    public function logout()
    {
        $token = JWTAuth::getToken();
        $invalidate = JWTAuth::invalidate($token);

        if ($invalidate) {
            return response()->json([
                'status' => "success",
                'message' => "Berhasil melakukan log out",
                'data' => [],
            ], 200);
        } else {
            return response()->json([
                'status' => "error",
                'message' => "Gagal melakukan log out",
                'data' => null,
            ], 500);
        }
    }

    public function me()
    {
        return response()->json([
            'status' => "success",
            'message' => "Berhasil mendapatkan pengguna",
            'data' => [
                'user' => auth('api')->user(),
            ],
        ], 200);
    }
}
