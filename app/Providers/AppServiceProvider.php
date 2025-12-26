<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\User;
use App\Observers\AppointmentObserver;
use App\Observers\DoctorObserver;
use App\Observers\PaymentObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        Appointment::observe(AppointmentObserver::class);
        Doctor::observe(DoctorObserver::class);
        User::observe(UserObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
