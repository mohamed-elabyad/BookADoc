<?php

use App\Http\Controllers\Auth\LoginDoctorController;
use App\Http\Controllers\Auth\LoginUserController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterDoctorController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Stripe\CheckoutController;
use App\Http\Controllers\Doctor\ProfileController;
use App\Http\Controllers\Stripe\WebhookController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => to_route('doctors.index'));

Route::resource('doctors', DoctorController::class)
    ->only(['index', 'show']);

Route::middleware(['auth', 'doctor'])->group(function () {
    // Doctor's appointments
    Route::prefix('doctor')->name('doctor.')->group(function () {
        Route::get('appointments', [\App\Http\Controllers\Doctor\AppointmentsController::class, 'index'])
            ->name('appointments.index');

        Route::patch('appointments/{appointment}', [\App\Http\Controllers\Doctor\AppointmentsController::class, 'update'])
            ->name('appointments.update');

        Route::delete('appointments/{appointment}', [\App\Http\Controllers\Doctor\AppointmentsController::class, 'destroy'])
            ->name('appointments.destroy');

        Route::patch('/doctor/appointments/{appointment}/confirm-cash-payment', [\App\Http\Controllers\Doctor\AppointmentsController::class, 'confirmCashPayment'])
            ->name('appointments.confirm-cash-payment');
    });


    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'user'])->group(function () {
    // user's appointments
    Route::resource('doctors.appointments', \App\Http\Controllers\User\AppointmentController::class)
        ->scoped()
        ->only(['create', 'store']);

    Route::resource('user/appointments', \App\Http\Controllers\User\AppointmentController::class)
        ->only(['index', 'destroy']);
});

Route::middleware('auth')->group(function () {

    Route::controller(ChatController::class)
        ->prefix('chat')
        ->name('chat.')
        ->group(function () {

            Route::get('/', 'index')->name('index');

            Route::post('/', 'store')->name('store');

            Route::get('{conversation}', 'show')->name('show');

            Route::post('{conversation}/messages', 'sendMessage')->name('messages.store');

            Route::patch('{conversation}/read', 'markAsRead')->name('read');
        });


    Route::post('appointments/{appointment}/checkout', [CheckoutController::class, 'create'])
        ->name('stripe.checkout');
    Route::get('payment/success', [CheckoutController::class, 'success'])
        ->name('stripe.success');
    Route::get('payment/cancel', [CheckoutController::class, 'cancel'])
        ->name('stripe.cancel');
});

Route::post('stripe/webhook', [WebhookController::class, 'handle'])
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->name('stripe.webhook');



Route::middleware('guest')->group(function () {

    Route::get('login/user', [LoginUserController::class, 'loginPage'])->name('login');
    Route::post('login/user', [LoginUserController::class, 'login'])->name('login.store');

    Route::get('register/user', [RegisterUserController::class, 'create'])->name('register');
    Route::post('register/user', [RegisterUserController::class, 'store'])->name('register.store');

    Route::get('login/doctor', [LoginDoctorController::class, 'loginPage'])->name('doctor.login');
    Route::post('login/doctor', [LoginDoctorController::class, 'login'])->name('doctor.login.store');

    Route::get('register/doctor', [RegisterDoctorController::class, 'create'])->name('doctor.register');
    Route::post('register/doctor', [RegisterDoctorController::class, 'store'])->name('doctor.register.store');
});

Route::post('logout', LogoutController::class)->middleware('auth')->name('logout');
