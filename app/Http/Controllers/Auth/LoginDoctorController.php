<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginDoctorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginDoctorController extends Controller
{
    public function loginPage()
    {
        return view('auth.login-doctor');
    }

    public function login(LoginDoctorRequest $request)
    {
        $credentials = $request->validated();

        $remember = $request->filled('remember');

        if (! Auth::attempt(array_merge($credentials, ['role' => 'doctor']), $remember)) {
            return redirect()->back()->with('error', 'Invalid Credentials');
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }
}
