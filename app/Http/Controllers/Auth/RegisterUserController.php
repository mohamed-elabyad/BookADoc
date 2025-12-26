<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Notifications\UserRegisteredNotification;
use Illuminate\Support\Facades\Auth;

class RegisterUserController extends Controller
{
    public function create(){
        return view('auth.register-user');
    }

    public function store(RegisterUserRequest $request){
        $validated = $request->validated();

        $user = User::create(array_merge($validated, ['role' => 'user']));

        Auth::login($user);

        $user->notify(
            new UserRegisteredNotification($user)
        );

        return redirect()->intended('/')->with('success', 'Registeration succeeded');
    }
}
