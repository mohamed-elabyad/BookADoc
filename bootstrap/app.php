<?php

use App\Http\Middleware\EnsureUserIsDoctor;
use App\Http\Middleware\EnsureUserIsUser;
use App\Models\Appointment;
use App\Models\Conversation;
use App\Policies\AppointmentPolicy;
use App\Policies\ConversationPolicy;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'doctor' => EnsureUserIsDoctor::class,
            'user' => EnsureUserIsUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
