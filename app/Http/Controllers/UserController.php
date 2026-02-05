<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

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
    $input['email'] = $request->email;
    $input['password'] = $request->password;
    $rules = array('email' => 'required|email|unique:users,email', 'password' => 'required');

    $validator = Validator::make($input, $rules);
    if ($validator->fails())
      return redirect('/register')->with('failed', 'Email telah terdaftar');

    User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    return redirect('/login')->with('success', 'Registrasi akun berhasil');
  }

  public function login(Request $request)
  {
    $user = User::where("email", "=", $request->email)->first();

    if ($user == null)
      return redirect('/register')->with('failed', 'Email belum terdaftar, silahkan mendaftar terlebih dahulu');

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
      if (Auth::logoutOtherDevices($request->password))
        return redirect('/');
    }
    return redirect('/login')->with('failed', 'Email atau Password salah');
  }

  public function logout(Request $request)
  {
    $request->session()->flush();
    Auth::logout();
    return redirect('/');
  }
}
