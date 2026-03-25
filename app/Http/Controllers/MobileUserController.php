<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class MobileUserController extends Controller
{
    private array $validationMessages = [
        'required' => ':attribute wajib diisi.',
        'required_without' => ':attribute wajib diisi.',
        'email' => 'Format :attribute tidak valid.',
        'unique' => ':attribute sudah terdaftar.',
        'regex' => 'Format :attribute tidak valid.',
        'confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        'min' => ':attribute minimal :min karakter.',
        'max' => ':attribute maksimal :max karakter.',
    ];

    private array $validationAttributes = [
        'name' => 'nama lengkap',
        'email' => 'email',
        'phone_number' => 'no. HP',
        'password' => 'kata sandi',
    ];

    /**
     * Register a new user from the mobile app.
     * POST /api/mobile/register
     */
    public function register(Request $request)
    {
        $normalizedPhone = $this->normalizePhoneNumber($request->phone_number);
        $normalizedEmail = $this->normalizeEmail($request->email);

        $request->merge([
            'phone_number' => $normalizedPhone,
            'email' => $normalizedPhone ? null : $normalizedEmail,
        ]);

        $this->validate($request, [
            'name'         => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
            'email'        => 'nullable|required_without:phone_number|email|unique:users,email',
            'phone_number' => 'nullable|required_without:email|regex:/^\+62[0-9]{8,13}$/|unique:users,phone_number',
            'password'     => 'required|string|min:6',
        ], $this->validationMessages, $this->validationAttributes);

        $identifierField = $request->filled('phone_number') ? 'phone_number' : 'email';
        $identifierValue = $identifierField === 'phone_number' ? $request->phone_number : $request->email;

        $user = User::create([
            'name'         => $request->name,
            'email'        => $identifierField === 'email' ? $request->email : null,
            'phone_number' => $identifierField === 'phone_number' ? $request->phone_number : null,
            'password'     => Hash::make($request->password),
        ]);

        $token = auth('api')->attempt([$identifierField => $identifierValue, 'password' => $request->password]);

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
        $normalizedPhone = $this->normalizePhoneNumber($request->phone_number);
        $normalizedEmail = $this->normalizeEmail($request->email);

        $request->merge([
            'phone_number' => $normalizedPhone,
            'email' => $normalizedPhone ? null : $normalizedEmail,
        ]);

        $this->validate($request, [
            'email'        => 'nullable|required_without:phone_number|email',
            'phone_number' => 'nullable|required_without:email|regex:/^\+62[0-9]{8,13}$/',
            'password'     => 'required|string',
        ], $this->validationMessages, $this->validationAttributes);

        $identifierField = $request->filled('phone_number') ? 'phone_number' : 'email';
        $identifierValue = $identifierField === 'phone_number' ? $request->phone_number : $request->email;

        $token = auth('api')->attempt([$identifierField => $identifierValue, 'password' => $request->password]);

        if (!$token) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email/No. HP atau password salah',
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
        } catch (\Throwable $e) {
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

    private function normalizePhoneNumber($phoneNumber)
    {
        if (!is_string($phoneNumber)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phoneNumber);
        if (!$digits) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (str_starts_with($digits, '62')) {
            $digits = substr($digits, 2);
        }

        if ($digits === '') {
            return null;
        }

        return '+62' . $digits;
    }

    private function normalizeEmail($email)
    {
        if (!is_string($email)) {
            return null;
        }

        $trimmed = trim($email);
        if ($trimmed === '' || strtolower($trimmed) === 'null') {
            return null;
        }

        return $trimmed;
    }
}