<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Auth;

class LoginUserController extends Controller
{
    public function loginPage()
    {
        return view('auth.login-user');
    }

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->validated();

        $remember = $request->filled('remember');

        if (! Auth::attempt(array_merge($credentials, ['role' => 'user']), $remember)){
            return redirect()->back()->with('error', 'Invalid Credentials');
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }
}
