<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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

  public function registerPage()
  {
    return view('register');
  }

  public function loginPage()
  {
    return view('login');
  }

  public function register(Request $request)
  {
    $normalizedPhone = $this->normalizePhoneNumber($request->phone_number);
    $normalizedEmail = $this->normalizeEmail($request->email);

    $request->merge([
      'phone_number' => $normalizedPhone,
      'email' => $normalizedPhone ? null : $normalizedEmail,
    ]);

    $validated = $request->validate([
      'name' => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
      'email' => 'nullable|required_without:phone_number|email|unique:users,email',
      'phone_number' => 'nullable|required_without:email|regex:/^\+62[0-9]{8,13}$/|unique:users,phone_number',
      'password' => 'required|string|confirmed|min:8',
    ], $this->validationMessages, $this->validationAttributes);

    $identifierField = !empty($validated['phone_number']) ? 'phone_number' : 'email';

    User::create([
      'name' => $validated['name'],
      'email' => $identifierField === 'email' ? $validated['email'] : null,
      'phone_number' => $identifierField === 'phone_number' ? $validated['phone_number'] : null,
      'password' => Hash::make($validated['password']),
    ]);

    $loginPrefill = [
      'email' => $identifierField === 'email' ? $validated['email'] : '',
      'phone_number' => $identifierField === 'phone_number' ? preg_replace('/^\+62/', '', $validated['phone_number']) : '',
    ];

    return redirect('/login')
      ->with('success', 'Registrasi akun berhasil')
      ->withInput($loginPrefill);
  }

  public function login(Request $request)
  {
    $normalizedPhone = $this->normalizePhoneNumber($request->phone_number);
    $normalizedEmail = $this->normalizeEmail($request->email);

    $request->merge([
      'phone_number' => $normalizedPhone,
      'email' => $normalizedPhone ? null : $normalizedEmail,
    ]);

    $validated = $request->validate([
      'email' => 'nullable|required_without:phone_number|email',
      'phone_number' => 'nullable|required_without:email|regex:/^\+62[0-9]{8,13}$/',
      'password' => 'required|string',
    ], $this->validationMessages, $this->validationAttributes);

    $field = !empty($validated['phone_number']) ? 'phone_number' : 'email';
    $identifier = $field === 'phone_number' ? $validated['phone_number'] : $validated['email'];

    $user = User::where($field, '=', $identifier)->first();

    if ($user == null)
      return redirect('/register')->with('failed', 'Akun belum terdaftar, silahkan mendaftar terlebih dahulu');

    if (Auth::attempt([$field => $identifier, 'password' => $validated['password']])) {
      $request->session()->regenerate();
      return redirect('/');
    }

    return redirect('/login')->with('failed', 'Email/No. HP atau Password salah');
  }

  public function logout(Request $request)
  {
    $request->session()->flush();
    Auth::logout();
    return redirect('/');
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
